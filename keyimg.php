<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

/**
 * Key Image
 * Used as an attempt to stop spammers.
 * Copyright (c) 2009 Jack Polgar
 */
session_start();
$_SESSION['key'] = rand(00000,99999);
header("Content-type: image/png");
$im = imagecreate(45, 20);
$background_color = imagecolorallocate($im, 0, 0, 0);
$text_color = imagecolorallocate($im, 255, 255, 255);
imagestring($im, 4, 2, 2,  $_SESSION['key'], $text_color);
imagepng($im);
imagedestroy($im);
?>