<?php

    require('../shared/shared.php');
    
    $nonces = getNonces();

    $req = [
        "merchantId" => $merchant['ID'],
        "merchantKey" => $merchant['KEY'], // don't include the Merchant Key in the JavaScript initialization!
        "requestType" => "payment",
        "orderNumber" => "Invoice" . rand(0, 1000),
        "amount" => $request['amount'],
        "salt" => $nonces['salt'],
        "postbackUrl" => $request['postbackUrl'],
        "preAuth" => $request['preAuth'],
        "token" => $_GET['token']
    ]; 
    
    $authKey = getAuthKey(json_encode($req), $developer['KEY'], $nonces['salt'], $nonces['iv']);
?>{
    "authKey": "<?php echo $authKey; ?>",
    "invoice": "<?php echo $req['orderNumber']; ?>",
    "salt": "<?php echo $req['salt']; ?>",
    "merch": "<?php echo $req['merchantId']; ?>",
    "clientId": "<?php echo $developer['ID'] ?>",
    "postback": "<?php echo $req['postbackUrl']; ?>",
    "amount": "<?php echo $req['amount']; ?>"
}
