<?php

namespace Deployer;

require 'recipe/common.php';
require 'contrib/cloudflare.php';

// Config
set('keep_releases', 1);

set('repository', 'git@github.com:christiancannata/agrispesa.git');

add('shared_files', ['wp-config.php', 'wp-content/debug.log']);
add('shared_dirs', ['wp-content/uploads']);
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
//after('deploy:publish', 'deploy:cloudflare');

task('reload:php', function () {
    run('sudo /usr/sbin/service php7.4-fpm restart');
});


desc('Deploys project');
task('deploy', [
    'deploy:prepare',
    'deploy:publish',
]);