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

<div class="wrapper text-center">
    <h1>RequireJS</h1>
    <p>The <code>pay.js</code> and <code>pay.min.js</code> files use a bundled version of <a href="http://requirejs.org/">RequireJS</a> to manage module dependencies. If you already use RequireJS on your site, reference PayJS directly via <code>config.paths</code>:</p>
</div>
<pre><code>        requirejs.config({
            paths: {
                "jquery": "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min",
                <span style="background-color: yellow">"PayJS": 'https://www.sagepayments.net/pay/1.0.0/js/build'</span>
            },
        });</code></pre>
<div class="wrapper text-center">
    <p>And then use it just like you would use any other module:</p>
</div>
<pre><code>        requirejs(['jquery', 'myAwesomeCode', <span style="background-color: yellow">'PayJS/UI'</span>],
        function($, $MAC, $UI) {
            $MAC.doBusiness();
            $UI.Initialize({
                <i>(...)</i>
            });
        });</code></pre>
<div class="wrapper text-center">
    <br />
    <div>
        <button class="btn btn-primary" id="paymentButton">Pay Now</button>
        <br /><br />
        <h5>Results:</h5>
        <p style="width:100%"><pre><code id="paymentResponse">The response will appear here as JSON, and in your browser console as a JavaScript object.</code></pre></p>
    </div>
</div>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/require.js/2.2.0/require.min.js"></script>
<script type="text/javascript">
    requirejs.config({
        baseUrl: "require/my/other/js",
        paths: {
            "jquery": "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min",
            "PayJS": 'https://www.sagepayments.net/pay/1.0.0/js/build',
        },
    });
</script>
<script type="text/javascript">
    // you guessed it -- PayJS() is just an alias of requirejs()
    requirejs(['myAwesomeCode', 'PayJS/UI', 'jquery'],
    function($MAC, $UI, $) {
        $MAC.doBusiness(); 
        $UI.Initialize({
            apiKey: "<?php echo $developer['ID']; ?>",
            environment: "<?php echo $request['environment']; ?>",
            postbackUrl: "<?php echo $request['postbackUrl']; ?>",
            merchantId: "<?php echo $merchant['ID']; ?>",
            authKey: "<?php echo $authKey; ?>",
            nonce: "<?php echo $nonces['salt']; ?>",
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
            elementId: "paymentButton",
            phoneNumber: "1-800-555-1234",
        });
        $UI.setCallback(function($RESP) {
            console.log($RESP.getResponse());
            $("#paymentResponse").text(
                $RESP.getResponse({ "json": true })
            );
        });
    });
</script>
