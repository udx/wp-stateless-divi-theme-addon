<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7de2d9ecde922eca4977e90bfe680acb
{
    public static $files = array (
        '2dde58341f34764536d51f465356b368' => __DIR__ . '/../..' . '/class-divi.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit7de2d9ecde922eca4977e90bfe680acb::$classMap;

        }, null, ClassLoader::class);
    }
}
