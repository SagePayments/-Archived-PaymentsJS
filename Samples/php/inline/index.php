<?php
    require('../shared/shared.php');
    
    $requestType = "payment";
    $amount = "1.00";
    
    // some arbitrary values for this demo
    $requestId = "Invoice" . rand(0, 1000);
    $nonce = uniqid();
    $postbackUrl = "https://www.example.com/";
    
    $combinedString = $requestType . $requestId . $merchantCredentials['MID'] . $postbackUrl . $nonce . $amount;
    $authKey = createHmac($combinedString, $merchantCredentials["MKEY"]);

?>
<style>
    #paymentDiv {
        width: 60%;
        margin-left:auto;
        margin-right:auto;
        padding: 15px;
        border-width: thin;
        border-style: dotted;
        border-color: #3c424f;
    }
</style>
<div class="wrapper text-center">
    <h1>Modal Dialog</h1>
    <p>When PayJS is initialized with a <code>&lt;div&gt;</code>, the UI appears within that element:</p>
    <br />
    <div>
        <div id="paymentDiv"></div>
        <br /><br />
        <h5>Results:</h5>
        <p style="width:100%"><pre><code id="paymentResponse">The response will appear here as JSON, and in your browser console as a JavaScript object.</code></pre></p>
    </div>
</div>
<script type="text/javascript">
    PayJS(['PayJS/UI', 'jquery'],
    function($UI, $) {
        $UI.Initialize({
            apiKey: "<?php echo $developerId; ?>",
            merchantId: "<?php echo $merchantCredentials['MID']; ?>",
            authKey: "<?php echo $authKey; ?>",
            requestType: "<?php echo $requestType; ?>",
            requestId: "<?php echo $requestId; ?>",
            amount: "<?php echo $amount; ?>",
            elementId: "paymentDiv",
            debug: true,
            postbackUrl: "<?php echo $postbackUrl; ?>",
            phoneNumber: "1-800-555-1234",
            nonce: "<?php echo $nonce; ?>",
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

