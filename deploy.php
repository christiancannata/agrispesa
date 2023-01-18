<?php

namespace Deployer;

require 'recipe/common.php';
require 'contrib/cloudflare.php';

// Config

set('repository', 'git@github.com:christiancannata/agrispesa.git');

add('shared_files', ['wp-config.php']);
add('shared_dirs', []);
add('writable_dirs', ['wp-content/uploads']);

add('cloudflare', [
    'api_key' => '46a284e4975fed5c1f3c095e969c645b80f37',
    'zone_id' => '6d39387e1c6d2d50e7c91de92c59deaf',
    'email' => 'christiancannata@gmail.com'
]);
// Hosts

host('46.101.145.102')
    ->set('remote_user', 'root')
    ->set('deploy_path', '/var/www/agrispesa')
    ->set('branch', 'master');

// Hooks

after('deploy:failed', 'deploy:unlock');

after('deploy:publish', 'reload:php');
after('reload:php', 'deploy:cloudflare');

task('reload:php', function () {
    run('sudo /usr/sbin/service php8.2-fpm restart');
});


desc('Deploys project');
task('deploy', [
    'deploy:prepare',
    'deploy:publish',
]);