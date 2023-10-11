<?php

namespace Deployer;

require 'recipe/common.php';

// Config
set('keep_releases', 3);

set('repository', 'git@github.com:christiancannata/agrispesa.git');

add('shared_files', ['wp-config.php', 'wp-content/debug.log', 'wp-content/advanced-cache.php']);
add('shared_dirs', ['wp-content/uploads', 'wp-content/plugins', 'wp-content/wp-rocket-config']);
add('writable_dirs', ['wp-content/uploads', 'wp-content/wp-rocket-config']);

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

after('deploy:failed', 'deploy:unlock');

after('deploy:publish', 'reload:php');
after('deploy:prepare', 'ssh:permission');
//after('deploy:release', 'permission:reset');
//after('deploy:publish', 'deploy:cloudflare');

task('reload:php', function () {
    run('chown -R root:www-data {{deploy_path}}');
    run('chown developer {{deploy_path}}/shared/wp-content/uploads/invoices');
    run('sudo /usr/sbin/service php8.0-fpm restart');
    run('sudo /usr/sbin/service nginx restart');
});


desc('Deploys project');
task('deploy', [
    'deploy:prepare',
    'deploy:publish',
]);