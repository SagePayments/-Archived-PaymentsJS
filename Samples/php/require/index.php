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
        "preAuth" => $request['preAuth']
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
                <span style="background-color: yellow">"PayJS": 'https://www.sagepayments.net/pay/1.0.1/js/build'</span>
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
            "PayJS": 'https://www.sagepayments.net/pay/1.0.1/js/build',
        },
    });
</script>

<script type="text/javascript">
    // you guessed it -- PayJS() is just an alias of requirejs()
    requirejs(['myAwesomeCode', 'PayJS/UI', 'jquery'],
    function($MAC, $UI, $) {
        $MAC.doBusiness(); 
        $UI.Initialize({
            clientId: "<?php echo $developer['ID']; ?>",
            postbackUrl: "<?php echo $req['postbackUrl']; ?>",
            merchantId: "<?php echo $req['merchantId']; ?>",
            authKey: "<?php echo $authKey; ?>",
            salt: "<?php echo $req['salt']; ?>",
            requestType: "<?php echo $req['requestType']; ?>",
            orderNumber: "<?php echo $req['orderNumber']; ?>",
            amount: "<?php echo $req['amount']; ?>",
            elementId: "paymentButton",
            addFakeData: true
        });
        $UI.setCallback(function($RESP) {
            console.log($RESP.getResponse());
            $("#paymentResponse").text(
                $RESP.getRawResponse()
            );
        });
    });
</script>
