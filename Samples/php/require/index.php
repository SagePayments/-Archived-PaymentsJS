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
<div class="wrapper text-center">
    <h1>RequireJS</h1>
    <p>The <code>pay.js</code> and <code>pay.min.js</code> files use a bundled version of <a href="http://requirejs.org/">RequireJS</a> to manage module dependencies. If you already use RequireJS on your site, reference PayJS directly via <code>config.paths</code>:</p>
</div>
<pre><code>        requirejs.config({
            paths: {
                "jquery": "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min",
                <span style="background-color: yellow">"PayJS": 'https://www.sagepayments.net/pay/js/build'</span>
            },
        });</code></pre>
<div class="wrapper text-center">
    <p>And then use it as you normally would:</p>
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
            "PayJS": 'https://www.sagepayments.net/pay/js/build',
        },
    });

    requirejs(['jquery', 'myAwesomeCode', 'PayJS/UI'],
    function($, $MAC, $UI) {
        $MAC.doBusiness();
        $UI.Initialize({
            apiKey: "<?php echo $developerId; ?>",
            merchantId: "<?php echo $merchantCredentials['MID']; ?>",
            authKey: "<?php echo $authKey; ?>",
            requestType: "<?php echo $requestType; ?>",
            requestId: "<?php echo $requestId; ?>",
            amount: "<?php echo $amount; ?>",
            elementId: "paymentButton",
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
