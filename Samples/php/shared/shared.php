<?php

    $merchantCredentials = [
        "MID" => "417227771521",
        "MKEY" => "I5T2R2K6V1Q3"
    ];
    
    $developerId = "GvVtRUT9hIchmOO3j2ak4JgdGpIPYPG4";
    
    function createHmac($toBeHashed, $privateKey){
        $hmac = hash_hmac(
            "sha512", // use the SHA-512 algorithm...
            $toBeHashed, // ... to hash the combined string...
            $privateKey, // .. using your merchant key to sign it.
            true // (php returns hexits by default; override this)
        );
        // convert to base-64 for transport
        return base64_encode($hmac);
    }
    
?>