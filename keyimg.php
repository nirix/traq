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
session_start(); // Start the session
$_SESSION['key'] = rand(00000,99999);// Set the key
header("Content-type: image/png"); // Set the Content-Type to image/png
$im = imagecreate(45, 20); // Create the image
$background_color = imagecolorallocate($im, 0, 0, 0); // Set the background color to black
$text_color = imagecolorallocate($im, 255, 255, 255); // Set the text color to white
imagestring($im, 4, 2, 2,  $_SESSION['key'], $text_color); // Make the image
imagepng($im); // Display the image
imagedestroy($im); // Destroy our beautiful image =(
?>