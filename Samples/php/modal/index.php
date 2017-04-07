<?php
    require('../shared/shared.php');
    
    $nonces = getNonces();
    $environment = $request['environment'];

    $req = [
        "merchantId" => $merchant['ID'],
        "merchantKey" => $merchant['KEY'], // don't include the Merchant Key in the JavaScript initialization!
        "requestType" => "payment",
        "orderNumber" => "Invoice" . rand(0, 1000),
        "amount" => $request['amount'],
        "salt" => $nonces['salt'],
        "postbackUrl" => $request['postbackUrl'],
        "preAuth" => $request['preAuth']
    ]; 
    
    $authKey = getAuthKey(json_encode($req), $developer['KEY'], $nonces['salt'], $nonces['iv']);
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
    // full api reference is available at https://github.com/SagePayments/PaymentsJS
    
    // the entire library is accessed through the PayJS() function:
    
    PayJS(['PayJS/UI', 'jquery'], // name the modules you want to use...
    function($UI, $) { // ... and then assign them to variables.
        
        // we'll start by initializing the UI:
        $UI.Initialize({
            // developer:
            clientId: "<?php echo $developer['ID']; ?>",
            postbackUrl: "<?php echo $req['postbackUrl']; ?>", // you get a copy of the response here
            environment: "<?php echo $environment; ?>",
            
            // merchant:
            merchantId: "<?php echo $req['merchantId']; ?>",
            
            // security:
            authKey: "<?php echo $authKey; ?>",
            salt: "<?php echo $req['salt']; ?>",
            
            // request:
            requestType: "<?php echo $req['requestType']; ?>",
            orderNumber: "<?php echo $req['orderNumber']; ?>",
            amount: "<?php echo $req['amount']; ?>",
            preAuth: "<?php echo $req['preAuth']; ?>",

            // ui:
            elementId: "paymentButton", // the DOM that $UI should attach itself to,

            // dev QoL:
            // debug: true, // verbose logging
            // show: true, // show the modal immediately, instead of waiting for a click
            addFakeData: true // pre-fill the form with test values
        });
        
        // and then we'll set a callback function to execute after the user
        // has submitted their card data and received a respnonse back
        $UI.setCallback(function($RESP) { // the callback function receives an instance of the RESPONSE module
            console.log("Ajax Response:");
            console.log($RESP.getAjaxResponse());
            console.log("API Response:");
            console.log($RESP.getApiResponse());
            console.log("Gateway Response:");
            console.log($RESP.getGatewayResponse());
            console.log("API Response + Hash:");
            console.log($RESP.getResponseHash())
            $("#paymentResponse").text(
                $RESP.getApiResponse()
            );
            // the response includes the gateway response, plus a SHA512 HMAC of the gateway response
            // the HMAC uses your developer key to sign the response payload
            // it's always a good idea to verify the hash, server-side, to ensure that the response is legitimate
            // this is especially important if you're changing an account balance, shipping a product, etc.
        });
    });
</script>

