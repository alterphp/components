<?php

/**
* This is bootstrap for phpUnit unit tests,
* use README.md for more details
*/

if (!class_exists('PHPUnit_Framework_TestCase') ||
    version_compare(PHPUnit_Runner_Version::id(), '3.5') < 0
) {
    die('PHPUnit framework is required, at least 3.5 version');
}

if (!class_exists('PHPUnit_Framework_MockObject_MockBuilder')) {
    die('PHPUnit MockObject plugin is required, at least 1.0.8 version');
}

define('TESTS_PATH', __DIR__);
define('VENDOR_PATH', realpath(__DIR__ . '/../vendor'));

$classLoaderFile = VENDOR_PATH . '/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
if (!file_exists($classLoaderFile)) {
    die('cannot find vendor, run: php bin/vendors.php');
}
require_once $classLoaderFile;
$loader = new Symfony\Component\ClassLoader\UniversalClassLoader;
$loader->registerNamespaces(array(
    'Symfony' => VENDOR_PATH . '/symfony/src',
    'AlterPHP\\Tests' => __DIR__,
    'AlterPHP' => __DIR__ . '/../src',
));
$loader->register();