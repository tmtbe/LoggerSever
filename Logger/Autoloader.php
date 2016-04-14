<?php
namespace Log;

/**
 * Autoload.
 */
class Autoloader
{
    /**
     * Load files by namespace.
     *
     * @param string $name
     * @return boolean
     */
    public static function loadByNamespace($name)
    {
    	
        $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $name);
        $class_file = __DIR__.DIRECTORY_SEPARATOR.$class_path.'.php';
        if (is_file($class_file)) {
            require_once($class_file);
            if (class_exists($name, false)) {
                return true;
            }
        }
        return false;
    }
}

spl_autoload_register('\Log\Autoloader::loadByNamespace');