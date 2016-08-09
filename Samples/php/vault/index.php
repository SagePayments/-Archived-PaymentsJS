<?php
    require('../shared/shared.php');
    
    $v_requestType = "vault";
    $v_amount = "";
    $v_requestId = "Invoice" . rand(0, 1000);
    $v_nonce = uniqid();
    $v_postbackUrl = "https://www.example.com/";
    $v_combinedString = $v_requestType . $v_requestId . $merchantCredentials['MID'] . $v_postbackUrl . $v_nonce . $v_amount;
    $v_authKey = createHmac($v_combinedString, $merchantCredentials["MKEY"]);
    
    $p_requestType = "payment";
    $p_amount = "1.00";
    $p_requestId = "Invoice" . rand(0, 1000);
    $p_nonce = uniqid();
    $p_postbackUrl = "https://www.example.com/";
    $p_combinedString = $p_requestType . $p_requestId . $merchantCredentials['MID'] . $p_postbackUrl . $p_nonce . $p_amount;
    $p_authKey = createHmac($p_combinedString, $merchantCredentials["MKEY"]);
?>
<div class="wrapper text-center">
    <h1>Tokenization</h1>
    <p>Not ready to charge a card, or expecting to charge it multiple times? Run a vault-only request to store the card, then charge it when you're ready.</p>
    <br />
    <div>
        <button class="btn btn-primary" id="vaultButton">Store Card</button>
        <button class="btn btn-warning" id="paymentButton" disabled>Charge Card</button>
        <br /><br />
        <h5>Results:</h5>
        <p style="width:100%"><pre><code id="paymentResponse">The response will appear here as JSON, and in your browser console as a JavaScript object.</code></pre></p>
    </div>
</div>
<script type="text/javascript">
    PayJS(['PayJS/Request', 'PayJS/Response', 'PayJS/Core', 'PayJS/UI', 'jquery'],
    function($REQ, $RESP, $CORE, $UI, $) {
        $UI.Initialize({
            apiKey: "<?php echo $developerId; ?>",
            merchantId: "<?php echo $merchantCredentials['MID']; ?>",
            authKey: "<?php echo $v_authKey; ?>",
            requestType: "<?php echo $v_requestType; ?>",
            requestId: "<?php echo $v_requestId; ?>",
            amount: "<?php echo $v_amount; ?>",
            elementId: "vaultButton",
            debug: true,
            postbackUrl: "<?php echo $v_postbackUrl; ?>",
            phoneNumber: "1-800-555-1234",
            nonce: "<?php echo $v_nonce; ?>",
            //suppressResultPage: true
        });
        $UI.setCallback(function(vaultResponse) {
            console.log(vaultResponse.getResponse());
            $("#paymentResponse").text(
                vaultResponse.getResponse({ "json": true })
            );
            if (vaultResponse.getVaultSuccess()) {
                $("#vaultButton").prop('disabled', true);
                $("#paymentButton").prop('disabled', false);
                $("#paymentButton").click(function() {
                    //$("#sps-holder").remove(); // remove initializations from previous samples
                    $CORE.Initialize({
                        apiKey: "<?php echo $developerId; ?>",
                        merchantId: "<?php echo $merchantCredentials['MID']; ?>",
                        authKey: "<?php echo $p_authKey; ?>",
                        requestType: "<?php echo $p_requestType; ?>",
                        requestId: "<?php echo $p_requestId; ?>",
                        amount: "<?php echo $p_amount; ?>",
                        debug: true,
                        postbackUrl: "<?php echo $p_postbackUrl; ?>",
                        nonce: "<?php echo $p_nonce; ?>",
                    });
                    $REQ.doTokenPayment(vaultResponse.getVaultToken(), "123", function(paymentResponse) {
                        console.log(paymentResponse);
                        $("#paymentResponse").text(
                            paymentResponse.Response.toString()
                        );
                    });
                })
            } else {
                // ...
            }
        });
    });
</script>

