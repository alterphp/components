#!/usr/bin/env php
<?php

// dependent libraries for test environment

define('VENDOR_PATH', __DIR__ . '/../vendor');

if (!is_dir(VENDOR_PATH)) {
    mkdir(VENDOR_PATH, 0775, true);
}

$deps = array(
    array('Symfony', 'http://github.com/symfony/symfony.git', 'v2.0.11'),
    array('Doctrine/ORM', 'https://github.com/doctrine/doctrine2.git', '2.1.6'),
    array('Doctrine/Common', 'https://github.com/doctrine/common.git', '2.1.4'),
    array('Doctrine/DBAL', 'https://github.com/doctrine/dbal.git', '2.1.6'),
);

foreach ($deps as $dep) {
    list($name, $url, $rev) = $dep;

    echo "> Installing/Updating $name\n";

    $installDir = VENDOR_PATH.'/'.$name;
    if (!is_dir($installDir)) {
        system(sprintf('git clone %s %s', $url, $installDir));
    }

    system(sprintf('cd %s && git fetch origin && git reset --hard %s', $installDir, $rev));
}