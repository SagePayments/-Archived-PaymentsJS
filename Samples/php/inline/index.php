<?php
    require('../shared/shared.php');
    
    $nonces = getNonces();
    
    $requestType = "payment";
    $requestId = "Invoice" . rand(0, 1000); // this'll be used as the order number
    
    $req = [
        "merchantId" => $merchant['ID'],
        "merchantKey" => $merchant['KEY'], // don't include the Merchant Key in the JavaScript initialization!
        "requestType" => $requestType,
        "requestId" => $requestId,
        "amount" => $request['amount'],
        "nonce" => $nonces['salt'],
        // on the other hand, include these here even if you leave them out of the JS init
        "postbackUrl" => $request['postbackUrl'], // if not specified in the JS init, defaults to the empty string
        "environment" => $request['environment'], // defaults to "cert"
        "preAuth" => $request['preAuth'] // defaults to false
    ]; 
    
    $authKey = getAuthKey(json_encode($req), $developer['KEY'], $nonces['salt'], $nonces['iv']);
?>

<style>
    #paymentDiv {
        width: 60%;
        margin-left:auto;
        margin-right:auto;
        padding: 30px;
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
    // full api reference is available at https://github.com/SagePayments/PaymentsJS
    
    // the entire library is accessed through the PayJS() function:
    
    PayJS(['PayJS/UI', 'jquery'], // name the modules you want to use...
    function($UI, $) { // ... and then assign them to variables.
        
        // we'll start by initializing the UI:
        $UI.Initialize({
            // developer:
            apiKey: "<?php echo $developer['ID']; ?>",
            environment: "<?php echo $request['environment']; ?>",
            postbackUrl: "<?php echo $request['postbackUrl']; ?>", // you get a copy of the response here
            
            // merchant:
            merchantId: "<?php echo $merchant['ID']; ?>",
            
            // security:
            authKey: "<?php echo $authKey; ?>",
            nonce: "<?php echo $nonces['salt']; ?>",
            
            // request:
            requestType: "<?php echo $requestType; ?>",
            requestId: "<?php echo $requestId; ?>",
            amount: "<?php echo $request['amount']; ?>",
            billing: {
                name: "PaymentsJS Sample",
                street: "123 Address St",
                city: "Denver",
                state: "CO",
                zip: "80205"
            },
            
            // ui:
            elementId: "paymentDiv", // the DOM that $UI should attach itself to,
            phoneNumber: "1-800-555-1234", // to provide users a support number
            
            // dev QoL:
            // debug: true, // verbose logging
            // show: true, // show the modal immediately, instead of waiting for a click
            // addFakeData: true // pre-fill the form with test values
        });
        
        // and then we'll set a callback function to execute after the user
        // has submitted their card data and received a respnonse back
        $UI.setCallback(function($RESP) { // the callback function receives an instance of the RESPONSE module
            console.log($RESP.getResponse());
            $("#paymentResponse").text(
                $RESP.getResponse({ "json": true })
            );
            // the response includes the gateway response, plus a SHA512 HMAC of the gateway response
            // the HMAC uses your developer key to sign the response payload
            // it's always a good idea to verify the hash, server-side, to ensure that the response is legitimate
            // this is especially important if you're changing an account balance, shipping a product, etc.
        });
    });
</script>

