<?php
ini_set("display_errors",1);
$loader = require __DIR__.'/vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

use Silex\Application as SilexApplication;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use DerAlex\Silex\YamlConfigServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Provider\TranslationServiceProvider;

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

class Application extends SilexApplication
{
    use Silex\Application\TranslationTrait;
    use Silex\Application\TwigTrait;
    use Silex\Application\UrlGeneratorTrait;
}

$app = new Application();

define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
$app['debug'] = false;

$app->register(new YamlConfigServiceProvider(__DIR__.'/config/config.yml'));

$configMonolog      = $app['config']['monolog'];
$configTwig         = $app['config']['twig'];
$configDoctrine     = $app['config']['doctrine']['options'];
$configDoctrineOrm  = $app['config']['doctrine']['orm'];
$configParams       = $app['config']['params'];
$configTranslator   = $app['config']['translate'];
$confTransCfStdLang = $app['config']['translator_config']['default_lang'];

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new MonologServiceProvider(), $configMonolog);
$app->register(new TwigServiceProvider(), $configTwig);
$app->register(new DoctrineServiceProvider, $configDoctrine);
$app->register(new DoctrineOrmServiceProvider(), $configDoctrineOrm);
$app->register(new TranslationServiceProvider(), $configTranslator);

$app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
    $translator->addLoader('yaml', new Symfony\Component\Translation\Loader\YamlFileLoader());
    
    foreach (glob(__DIR__ . '/resources/locale/*.yml') as $locale) {
        $lang = str_replace(".yml", "", basename($locale));
        $translator->addResource('yaml', $locale, $lang);
    }
    return $translator;
}));

$app['translator']->setLocale($confTransCfStdLang);

$app['params'] = $configParams;

$app['asset_path'] = '/src/assets';
$app['lib_path']   = '/lib';

$app['version']    = '1.41.5';

$apiCtrl = new MotoHelper\ApiMobile();
$app->mount('/api/mobile', $apiCtrl);

$apiCtrl = new MotoHelper\Api();
$app->mount('/api/v1', $apiCtrl);

$adminCtrl = new MotoHelper\Admin();
$app->mount('/admin', $adminCtrl);


$appCtrl = new MotoHelper\App();
$app->mount('/', $appCtrl);



return $app;
