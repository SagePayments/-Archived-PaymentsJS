<?php
    require('../shared/shared.php');
    
    $requestType = "payment";
    $amount = $_GET["amount"];
    
    // some arbitrary values for this demo
    $requestId = "Invoice" . rand(0, 1000);
    $nonce = uniqid();
    $postbackUrl = "https://www.example.com/";
    
    $combinedString = $requestType . $requestId . $merchantCredentials['MID'] . $postbackUrl . $nonce . $amount;
    $authKey = createHmac($combinedString, $merchantCredentials["MKEY"]);
?>
{
    "authKey": "<?php echo $authKey; ?>",
    "invoice": "<?php echo $requestId; ?>",
    "nonce": "<?php echo $nonce; ?>",
    "merch": "<?php echo $merchantCredentials['MID']; ?>",
    "apiKey": "<?php echo $developerId; ?>",
    "postback": "<?php echo $postbackUrl; ?>"
}
