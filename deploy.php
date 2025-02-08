<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'kimai.svenkrefeld.de');

// Project repository
set('repository', 'git@github.com:svenkrefeld/kimai.git');

// Allocate tty for git clone. Default value is false.
set('git_tty', false);

set('http_user', 'hosting136608');
set('writable_mode', 'chmod');

set('keep_releases', 3);

// Shared files/dirs between deploys 
set('shared_files', ['.env']);
set('shared_dirs', []);

// Writable dirs by web server 
set('writable_dirs', ['var']);
set('allow_anonymous_stats', false);

// Hosts
host('svenkrefeld.de')
    ->set('branch', 'main')
    ->setRemoteUser('hosting136608')
    ->setPort(22)
    ->setForwardAgent(true)
    ->setSshMultiplexing(false)
    ->setDeployPath('~/sites/{{application}}');

// Tasks
desc('Deploy your project');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:cache:clear',
    'deploy:publish',
]);

desc('Installing kimai');
task('deploy:build', function () {
    run('cd {{release_path}} && bin/console kimai:update');
});

desc('Clears cache');
task('deploy:cache:clear', function () {
    if (false !== strpos(get('composer_options', ''), '--no-scripts')) {
        run('{{bin/console}} cache:clear {{console_options}}');
    }
});


desc('Installs vendors');
task('deploy:vendors', function () {
    if (!commandExist('unzip')) {
        warning('To speed up composer installation setup "unzip" command with PHP zip extension.');
    }
    run('cd {{release_or_current_path}} && COMPOSER_MEMORY_LIMIT=-1 {{bin/composer}} {{composer_action}} {{composer_options}} 2>&1');
});

after('deploy:vendors', 'deploy:build');

// If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
