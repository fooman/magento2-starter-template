<?php
namespace Deployer;

require 'vendor/jalogut/magento2-deployer-plus/recipe/magento_2_2_5.php';

require __DIR__.'/vendor/autoload.php';
$env = new \Dotenv\Loader(__DIR__. '/m2-deploy-settings.txt');
$env->load();

// Use timestamp for release name
set('release_name', function () {
    return date('YmdHis');
});

// Magento dir into the project root. Set "." if magento is installed on project root
set('magento_dir', '.');
// [Optional] Git repository. Only needed if not using build + artifact strategy
set('repository', '');
// Space separated list of languages for static-content:deploy
set('languages', getenv('M2_DEPLOYER_LOCALES'));

// OPcache configuration
#task('cache:clear:opcache', 'sudo /etc/init.d/php7.3-fpm reload');
#after('cache:clear', 'cache:clear:opcache');
#after('deploy:override_shared', 'deploy:writable');

// Build host
localhost('build');

// Remote Servers
host('stage')
    ->hostname(getenv('M2_DEPLOYER_STAGE_HOSTNAME'))
    ->user(getenv('M2_DEPLOYER_STAGE_USER'))
    ->set('deploy_path', getenv('M2_DEPLOYER_STAGE_PATH'))
    ->stage('stage')
    ->roles('app');

host('prod')
    ->hostname(getenv('M2_DEPLOYER_PROD_HOSTNAME'))
    ->user(getenv('M2_DEPLOYER_PROD_USER'))
    ->set('deploy_path', getenv('M2_DEPLOYER_PROD_PATH'))
    ->stage('prod')
    ->roles('app');
