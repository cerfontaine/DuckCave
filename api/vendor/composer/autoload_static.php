<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit91b280e34516e2769dc8c117ea744380
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'W131810\\Api\\' => 12,
        ),
        'O' => 
        array (
            'OnlinePayments\\Sdk\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'W131810\\Api\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'OnlinePayments\\Sdk\\' => 
        array (
            0 => __DIR__ . '/..' . '/wl-online-payments-direct/sdk-php/src/OnlinePayments/Sdk',
            1 => __DIR__ . '/..' . '/wl-online-payments-direct/sdk-php/lib/OnlinePayments/Sdk',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit91b280e34516e2769dc8c117ea744380::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit91b280e34516e2769dc8c117ea744380::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit91b280e34516e2769dc8c117ea744380::$classMap;

        }, null, ClassLoader::class);
    }
}
