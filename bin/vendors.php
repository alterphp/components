#!/usr/bin/env php
<?php

// dependent libraries for test environment

define('VENDOR_PATH', __DIR__ . '/../vendor');

if (!is_dir(VENDOR_PATH)) {
    mkdir(VENDOR_PATH, 0775, true);
}

$deps = array(
    array('Symfony/Component/ClassLoader', 'http://github.com/symfony/ClassLoader.git', 'master'),
    array('Symfony/Component/HttpFoundation', 'http://github.com/symfony/HttpFoundation.git', 'master'),
    array('Symfony/Component/Form', 'http://github.com/symfony/Form.git', 'master'),
    array('Symfony/Bridge/Doctrine', 'http://github.com/symfony/DoctrineBridge.git', 'master'),
    array('Doctrine/ORM', 'https://github.com/doctrine/doctrine2.git', 'master'),
    array('Doctrine/Common', 'https://github.com/doctrine/common.git', 'master'),
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