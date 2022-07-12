<?php
/**
 * Register autoloader
 */
spl_autoload_register(function ($className) {
    $class = str_replace('\\', '/', $className);
    $fileName = "src/{$class}.php";

    if (file_exists($fileName)) {
        require_once($fileName);
    } else {
        throw new Exception("Class {$fileName} not found!");
    }
});
