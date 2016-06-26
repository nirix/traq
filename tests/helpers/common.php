<?php

function mkRandomHash($length = null)
{
    $hash = sha1(microtime() . uniqid());

    return $length !== null ? substr($hash, 0, $length) : $hash;
}
