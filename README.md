AlterPHP Components
====================

Some components I use in my developments (mostly with Symfony2)


Components list
--------------------

*   AlterPHP\Component\HttpFoundation\RedirectionResponseWithCookie :
Based on Symfony2 components, this class provides a simple RedirectResponse object
with the ability to send cookies to the client.

    This component has following dependencies :
    *   Symfony\Component\HttpFoundation\RedirectResponse
    *   Symfony\Component\HttpFoundation\Cookie


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