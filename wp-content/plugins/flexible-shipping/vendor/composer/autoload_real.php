<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitbafe01075cdfa6d6b3dc011e035c4bbe
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitbafe01075cdfa6d6b3dc011e035c4bbe', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitbafe01075cdfa6d6b3dc011e035c4bbe', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitbafe01075cdfa6d6b3dc011e035c4bbe::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
