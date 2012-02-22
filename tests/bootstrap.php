<?php

/**
* This is bootstrap for phpUnit unit tests,
* use README.md for more details
*/

define('TESTS_PATH', __DIR__);
define('VENDOR_PATH', realpath(__DIR__ . '/../vendor'));

$classLoaderFile = VENDOR_PATH . '/Symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
if (!file_exists($classLoaderFile)) {
    die('cannot find vendor, run: php bin/vendors.php');
}

require_once $classLoaderFile;

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

if (!class_exists('PHPUnit_Framework_TestCase') ||
    version_compare(PHPUnit_Runner_Version::id(), '3.5') < 0
) {
    die('PHPUnit framework is required, at least 3.5 version');
}

if (!class_exists('PHPUnit_Framework_MockObject_MockBuilder')) {
    die('PHPUnit MockObject plugin is required, at least 1.0.8 version');
}
$loader = new UniversalClassLoader;
$loader->registerNamespaces(array(
    'Symfony' => array(VENDOR_PATH . '/Symfony/src', VENDOR_PATH . '/Symfony/tests'),
    'Doctrine\\Common' => VENDOR_PATH . '/Doctrine/Common/lib',
    'Doctrine\\ORM' => VENDOR_PATH . '/Doctrine/ORM/lib',
    'Doctrine\\DBAL' => VENDOR_PATH . '/Doctrine/DBAL/lib',
    'AlterPHP\\Tests' => __DIR__,
    'AlterPHP' => __DIR__ . '/../src',
));
$loader->register();

AnnotationRegistry::registerLoader(function($class) use ($loader) {
    $loader->loadClass($class);
    return class_exists($class, false);
});
AnnotationRegistry::registerFile(VENDOR_PATH . '/Doctrine/ORM/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');