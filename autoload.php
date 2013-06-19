<?php

$loader = require_once __DIR__.'/vendor/autoload.php';

use Doctrine\Common\Annotations\AnnotationRegistry;

if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->add('IntlDateFormatter', __DIR__.'/src/Symfony/Component/Locale/Resources/stubs');
    $loader->add('Collator', __DIR__.'/src/Symfony/Component/Locale/Resources/stubs');
    $loader->add('Locale', __DIR__.'/src/Symfony/Component/Locale/Resources/stubs');
    $loader->add('NumberFormatter', __DIR__.'/src/Symfony/Component/Locale/Resources/stubs');
}

AnnotationRegistry::registerLoader(function($class) use ($loader) {
    $loader->loadClass($class);

    return class_exists($class, false);
});
AnnotationRegistry::registerFile(__DIR__.'/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
