<?php

    $merchantCredentials = [
        "ID" => "417227771521",
        "KEY" => "I5T2R2K6V1Q3"
    ];
    
    $developerCredentials = [
        "ID" => "GvVtRUT9hIchmOO3j2ak4JgdGpIPYPG4",
        "KEY" => "ABCDABCDABCDABCD"
    ];

    function createHmac($toBeHashed, $privateKey, $iv){
        $encryptHash = hash_pbkdf2("sha256", "0000", $privateKey, 1000, 32, true);
        $encrypted = openssl_encrypt($toBeHashed, "aes-256-cbc", $encryptHash, 0, $iv);
        return base64_encode($encrypted);
    }
    
    function getNonce(){
        $iv = openssl_random_pseudo_bytes(16);
        return [$iv, base64_encode(bin2hex($iv))];
    }
    
?>