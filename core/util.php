<?php

function getToken($email) : string {
    $token = openssl_random_pseudo_bytes(16);
    return bin2hex($token . substr($email , 0 , 2) . substr($email , 4 , 5) . microtime(true));
}