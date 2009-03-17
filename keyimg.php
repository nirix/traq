<?php
/**
 * Traq
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

/**
 * Key Image
 * Used as an attempt to stop spammers.
 * Copyright (c) 2009 Jack Polgar
 */

/* Copyright (c) 2009, Jack Polgar
   All rights reserved.
   
   Redistribution and use in source and binary forms,
   with or without modification, are permitted provided
   that the following conditions are met:
   
   1. Redistributions of source code must retain the above
   copyright notice, this list of conditions and the following
   disclaimer.
   
   2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
   
   3. Neither the name of the Traq nor the names of its
   contributors may be used to endorse or promote products derived
   from this software without specific prior written permission.
   
   THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
   "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
   LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
   A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
   OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
   SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
   LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
   DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
   THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
   (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
   OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. */

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