<?php
    require('../shared/shared.php');
    
    $nonce = getNonce();
    
    $requestType = "payment";
    $amount = "1.00";
    
    // some arbitrary values for this demo
    $requestId = "Invoice" . rand(0, 1000);
    $postbackUrl = "https://www.example.com/";
    
    $environment = "cert";
    $preAuth = "false";
    
    $req = [
        "merchantId" => $merchantCredentials['ID'],
        "merchantKey" => $merchantCredentials['KEY'],
        "requestType" => $requestType,
        "requestId" => $requestId,
        "postbackUrl" => $postbackUrl,
        "amount" => $amount,
        "nonce" => $nonce[1],
        "environment" => "$environment",
        "preAuth" => $preAuth
    ]; 
    $authKey = createHmac(json_encode($req), $developerCredentials["KEY"], $nonce[0]);
?>
<div class="wrapper text-center">
    <h1>Modal Dialog</h1>
    <p>When PayJS is initialized with a <code>&lt;button&gt;</code> or <code>&lt;a&gt;</code>, the UI appears over the page when that element is clicked:</p>
    <br />
    <div>
        <button class="btn btn-primary" id="paymentButton">Pay Now</button>
        <br /><br />
        <h5>Results:</h5>
        <p style="width:100%"><pre><code id="paymentResponse">The response will appear here as JSON, and in your browser console as a JavaScript object.</code></pre></p>
    </div>
</div>
<script type="text/javascript">
    PayJS(['PayJS/UI', 'jquery'],
    function($UI, $) {
        $UI.Initialize({
            apiKey: "<?php echo $developerCredentials['ID']; ?>",
            merchantId: "<?php echo $merchantCredentials['ID']; ?>",
            authKey: "<?php echo $authKey; ?>",
            requestType: "<?php echo $requestType; ?>",
            requestId: "<?php echo $requestId; ?>",
            amount: "<?php echo $amount; ?>",
            elementId: "paymentButton",
            debug: true,
            postbackUrl: "<?php echo $postbackUrl; ?>",
            phoneNumber: "1-800-555-1234",
            nonce: "<?php echo $nonce[1]; ?>",
            //suppressResultPage: true
        });
        $UI.setCallback(function(resp) {
            console.log(resp.getResponse());
            $("#paymentResponse").text(
                resp.getResponse({ "json": true })
            );
        });
    });
</script>

