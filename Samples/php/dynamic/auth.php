<?php

    require('../shared/shared.php');
    
    $nonces = getNonces();
    
    $requestType = "payment";
    $requestId = "Invoice" . rand(0, 1000);
    
    $amount = $_GET["amount"];
    
    $req = [
        "merchantId" => $merchant['ID'],
        "merchantKey" => $merchant['KEY'], // don't include the Merchant Key in the JavaScript initialization!
        "requestType" => $requestType,
        "requestId" => $requestId,
        "amount" => $amount,
        "nonce" => $nonces['salt'],
        // on the other hand, include these here even if you leave them out of the JS init
        "postbackUrl" => $request['postbackUrl'], // if not specified in the JS init, defaults to the empty string
        "environment" => $request['environment'], // defaults to "cert"
        "preAuth" => $request['preAuth'] // defaults to false
    ]; 
    
    $authKey = getAuthKey(json_encode($req), $developer['KEY'], $nonces['salt'], $nonces['iv']);
?>
{
    "authKey": "<?php echo $authKey; ?>",
    "invoice": "<?php echo $requestId; ?>",
    "nonce": "<?php echo $nonces['salt']; ?>",
    "merch": "<?php echo $merchant['ID']; ?>",
    "apiKey": "<?php echo $developer['ID'] ?>",
    "postback": "<?php echo $request['postbackUrl']; ?>"
}
