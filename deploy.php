<?php

namespace Deployer;

require 'recipe/common.php';

// Config
set('keep_releases', 1);

set('repository', 'git@github.com:christiancannata/agrispesa.git');

add('shared_files', ['wp-config.php', 'wp-content/debug.log']);
add('shared_dirs', ['wp-content/uploads']);
add('writable_dirs', ['wp-content/uploads']);

// Hosts

host('167.71.36.33')
    ->set('remote_user', 'root')
    ->set('deploy_path', '/var/www/agrispesa')
    ->set('branch', 'master');

// Hooks

after('deploy:failed', 'deploy:unlock');

after('deploy:publish', 'reload:php');
//after('deploy:publish', 'deploy:cloudflare');

task('reload:php', function () {
    run('sudo /usr/sbin/service php8.1-fpm restart');
});


desc('Deploys project');
task('deploy', [
    'deploy:prepare',
    'deploy:publish',
]);