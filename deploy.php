<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'kimai.svenkrefeld.de');

// Project repository
set('repository', 'git@github.com:svenkrefeld/kimai.git');

// Allocate tty for git clone. Default value is false.
set('git_tty', false);

// Shared files/dirs between deploys 
set('shared_files', ['.env']);
set('shared_dirs', []);

// Writable dirs by web server 
set('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts
host('svenkrefeld.de')
    ->stage('production')
    ->set('branch', 'main')
    ->roles('app')
    ->user('hosting136608')
    ->port(22)
    ->forwardAgent(true)
    ->multiplexing(false)
    ->set('deploy_path', '~/sites/{{application}}');

// Tasks
desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

desc('Installing kimai');
task('build', function () {
    run('cd {{release_path}} && bin/console kimai:install -n');
});

after('deploy:vendors', 'build');

// If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
