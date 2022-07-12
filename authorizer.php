<?php
require __DIR__ . '/vendor/autoload.php';
require 'App.php';

/**
 * Initialize application
 */
$operations = '';
if (ftell(STDIN) !== false) {
    $operations = stream_get_contents(STDIN);
}
$app = new App();
echo $app->init($operations);

