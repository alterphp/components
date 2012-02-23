AlterPHP Components
====================

Some components I use in my developments (mostly with Symfony2)

[![Build Status](https://secure.travis-ci.org/alterphp/components.png?branch=master)](http://travis-ci.org/alterphp/components)

Components list
--------------------

*   AlterPHP\Component\Form\Type\EntityBitType

    Based on Symfony2 components, this field type provides the way to __store
a collection of entities in a simple integer field/column__. It works the same
as _EntityType_ but takes and returns an integer that is the sum of each entity
bitPower value.

    bitPower field is an integer indicating the power of two that is assigned to the entity and must be __unique__.

    This component has following dependencies :
    * Symfony
    * Doctrine

*   AlterPHP\Component\HttpFoundation\RedirectionResponseWithCookie

    Based on Symfony2 components, this class provides a simple RedirectResponse object
with the ability to set cookies from the constructor.

    This component has following dependencies :
    * Symfony


Installation in a Symfony2 project
--------------------

Add following lines in the deps file :

    [alterphp]
        git=http://github.com/alterphp/components.git
        target=alterphp/components

Then run :

    bin/vendors install

Finally, in app/autoload.php, make the following add :

```php
<?php

// ...

$loader->registerNamespaces(array (
    'Symfony' => array (__DIR__ . '/../vendor/symfony/src', __DIR__ . '/../vendor/bundles'),
    // ...
   ' AlterPHP'   => __DIR__.'/../vendor/alterphp/components/src',
));
```