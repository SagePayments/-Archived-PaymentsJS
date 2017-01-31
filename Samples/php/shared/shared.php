<?php

    // this is a shared test account; don't hesitate to ask us for one of your own!
    $merchant = [
        "ID" => "173859436515",
        "KEY" => "P1J2V8P2Q3D8" 
    ];
    
    // sign up at https://developer.sagepayments.com/ to get your own dev creds
    $developer = [
        "ID" => "7SMmEF02WyC7H5TSdG1KssOQlwOOCagb",
        "KEY" => "wtC5Ns0jbtiNA8sP"
    ];
    
    $request = [
        "postbackUrl" => "https://www.example.com/myHandler.php", // https://requestb.in is great for playing with this
        "environment" => "cert",
        "amount" => "1.00", // use 5.00 to simulate a decline
        "preAuth" => "false"
    ];

    function getAuthKey($toBeHashed, $password, $salt, $iv){
        $encryptHash = hash_pbkdf2("sha1", $password, $salt, 1500, 32, true);
        $encrypted = openssl_encrypt($toBeHashed, "aes-256-cbc", $encryptHash, 0, $iv);
        return $encrypted;
    }
    
    function getNonces(){
        $iv = openssl_random_pseudo_bytes(16);
        $salt = base64_encode(bin2hex($iv));
        return [
            "iv" => $iv,
            "salt" => $salt
        ];
    }
    
?>
