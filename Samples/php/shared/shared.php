<?php

    $merchantCredentials = [
        "ID" => "999999999997",
        "KEY" => "K3QD6YWyhfD"
    ];
    
    $developerCredentials = [
        "ID" => "7SMmEF02WyC7H5TSdG1KssOQlwOOCagb",
        "KEY" => "wtC5Ns0jbtiNA8sP"
    ];

    function createHmac($toBeHashed, $password, $salt, $iv){
        $encryptHash = hash_pbkdf2("sha1", $password, $salt, 1500, 32, true);
        $encrypted = openssl_encrypt($toBeHashed, "aes-256-cbc", $encryptHash, 0, $iv);
        return $encrypted;
    }
    
    function getNonce(){
        $iv = openssl_random_pseudo_bytes(16);
        return [$iv, base64_encode(bin2hex($iv))];
    }
    
?>