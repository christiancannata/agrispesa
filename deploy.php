<?php

namespace Deployer;

require 'recipe/common.php';

// Config

set('repository', 'git@github.com:christiancannata/agrispesa.git');

add('shared_files', ['wp-config.php']);
add('shared_dirs', []);
add('writable_dirs', ['wp-content/uploads']);

// Hosts

host('46.101.145.102')
    ->set('remote_user', 'root')
    ->set('deploy_path', '/var/www/agrispesa')
    ->set('branch', 'master');

// Hooks

after('deploy:failed', 'deploy:unlock');

after('deploy:publish', 'reload:php');


task('reload:php', function () {
    run('sudo /usr/sbin/service php8.2-fpm restart');
});


desc('Deploys project');
task('deploy', [
    'deploy:prepare',
    'deploy:publish',
]);