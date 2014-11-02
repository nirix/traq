<?php
if (!isset($_SERVER['HTTP_X_OVERLAY'])) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}
