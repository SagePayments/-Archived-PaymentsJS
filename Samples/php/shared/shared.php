<?php

    $merchantCredentials = [
        "MID" => "417227771521",
        "MKEY" => "I5T2R2K6V1Q3"
    ];
    
    $developerId = "GvVtRUT9hIchmOO3j2ak4JgdGpIPYPG4";
    
    function createHmac($toBeHashed, $privateKey){
        echo $toBeHashed;
        echo "<br>";
        echo $privateKey;
        echo "<br>";
        echo "<br>";
        echo mb_internal_encoding();
        mb_internal_encoding("UTF-8");
        echo mb_internal_encoding();
        echo mb_detect_encoding($toBeHashed);
        //$x = mb_convert_encoding($toBeHashed, "UTF-8", "ASCII");
        //echo mb_detect_encoding($x);
        echo mb_detect_encoding($privateKey);
        //echo "<br>";
        //echo "combinedstring:";
        //$toBeHashed = mb_convert_encoding($toBeHashed, "UTF-8");
        //echo mb_detect_encoding($toBeHashed);
        //echo "<br>";
        //echo "mkey:";
        //echo mb_detect_encoding($privateKey);
        
        $hmac = hash_hmac(
            "sha512", // use the SHA-512 algorithm...
            //mb_convert_encoding($toBeHashed, "UTF-8"),
            $toBeHashed, // ... to hash the combined string...
            //mb_convert_encoding($privateKey, "UTF-8"),
            $privateKey, // .. using your merchant key to sign it.
            true // (php returns hexits by default; override this)
        );
        // -----------------------------------------

        // echo "<br>";
        // echo "hmac:";
        // echo mb_detect_encoding($hmac);
        // echo "<br>";
        // echo "b64hmac:";
        // echo mb_detect_encoding(base64_encode($hmac));
        


        // -----------------------------------------
        // convert to base-64 for transport
        return base64_encode($hmac);
    }
    
?>