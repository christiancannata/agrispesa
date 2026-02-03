<?php

namespace Deployer;

require 'recipe/common.php';

// Config
set('keep_releases', 3);

set('repository', 'git@github.com:christiancannata/agrispesa.git');

add('shared_files', ['wp-config.php', 'wp-content/debug.log','nginx.conf']);
add('shared_dirs', ['wp-content/uploads','wp-content/upgrade', 'wp-content/plugins', 'wp-content/wp-rocket-config','wp-content/w3tc-config','nginx.conf']);
add('writable_dirs', ['wp-content/uploads', 'wp-content/wp-rocket-config', 'wp-content/upgrade','wp-content/w3tc-config']);

// Hosts

host('167.71.36.33')
    ->set('remote_user', 'root')
    ->set('deploy_path', '/var/www/agrispesa')
    ->set('branch', 'master');

// Hooks
task('ssh:permission', function () {
    run('chmod 400 ~/.ssh/id_rsa');
})->desc('Artisan migrations');


task('permission:reset', function () {
    run('chown -R root:www-data {{deploy_path}}');
    //run('cd {{deploy_path}} && find . -type f -exec chmod 644 {} \;');
})->desc('Fix permissions');


task('reload:php', function () {
    run('chown -R root:www-data {{deploy_path}}');
    run('chown developer:www-data -R {{deploy_path}}/shared/wp-content/uploads/invoices/');
    run('sudo /usr/sbin/service php8.0-fpm restart');
    run('sudo /usr/sbin/service nginx restart');
});

task('wp:fix_permissions', function () {
    // Shared paths reali
    $shared = '{{deploy_path}}/shared';

    // 1) Plugins: devono essere aggiornabili da WP/WP-CLI => www-data owner
    run("chown -R www-data:www-data {$shared}/wp-content/plugins");
    run("find {$shared}/wp-content/plugins -type d -exec chmod 775 {} \\;");
    run("find {$shared}/wp-content/plugins -type f -exec chmod 664 {} \\;");

    // 2) Upgrade dir: WordPress lo usa per update/zip
    run("mkdir -p {$shared}/wp-content/upgrade");
    run("chown -R www-data:www-data {$shared}/wp-content/upgrade");
    run("chmod -R 775 {$shared}/wp-content/upgrade");

    // 3) Uploads: anche questi devono essere scrivibili
    run("chown -R www-data:www-data {$shared}/wp-content/uploads");
    run("find {$shared}/wp-content/uploads -type d -exec chmod 775 {} \\;");
    run("find {$shared}/wp-content/uploads -type f -exec chmod 664 {} \\;");

    // 4) (OPZIONALE ma utile) rimuovi ACL estese sui plugins se hai quel + nei permessi
    // Commenta se non vuoi toccare ACL
    run("setfacl -Rb {$shared}/wp-content/plugins || true");
})->desc('Fix WordPress shared permissions (plugins/update)');

after('deploy:failed', 'deploy:unlock');

after('deploy:publish', 'reload:php');
after('deploy:prepare', 'ssh:permission');
after('deploy:publish', 'wp:fix_permissions');
//after('deploy:release', 'permission:reset');
//after('deploy:publish', 'deploy:cloudflare');


desc('Deploys project');
task('deploy', [
    'deploy:prepare',
    'deploy:publish',
]);