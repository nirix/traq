<?php
require dirname(__DIR__) . '/vendor/avalon/framework/libs/fishhook.php';
require dirname(__DIR__) . '/vendor/avalon/framework/helpers/time.php';
require dirname(__DIR__) . '/vendor/avalon/framework/helpers/html.php';
require dirname(__DIR__) . '/vendor/avalon/framework/helpers/form.php';
require dirname(__DIR__) . '/vendor/avalon/framework/helpers/string.php';

class_alias('avalon\helpers\Time', 'Time');
class_alias('Avalon\Http\Router', 'Router');
class_alias('Avalon\Http\Request', 'Request');
class_alias('Avalon\Output\View', 'View');
