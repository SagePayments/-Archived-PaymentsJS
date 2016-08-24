<?php
    require('../shared/shared.php');
    
    $vault_nonces = getNonces();
    $vault_requestType = "vault";
    $vault_requestId = "Invoice" . rand(0, 1000);
    $vault_req = [
        "merchantId" => $merchant['ID'],
        "merchantKey" => $merchant['KEY'], // don't include the Merchant Key in the JavaScript initialization!
        "requestType" => $vault_requestType,
        "requestId" => $vault_requestId,
        "nonce" => $vault_nonces['salt'],
        // on the other hand, include these here even if you leave them out of the JS init
        "postbackUrl" => $request['postbackUrl'], // if not specified in the JS init, defaults to the empty string
        "environment" => $request['environment'], // defaults to "cert"
    ]; 
    $vault_authKey = getAuthKey(json_encode($vault_req), $developer['KEY'], $vault_nonces['salt'], $vault_nonces['iv']);
    
    $payment_nonces = getNonces();
    
    $payment_requestType = "payment";
    $payment_requestId = "Invoice" . rand(0, 1000); // this'll be used as the order number
    
    $payment_req = [
        "merchantId" => $merchant['ID'],
        "merchantKey" => $merchant['KEY'], // don't include the Merchant Key in the JavaScript initialization!
        "requestType" => $payment_requestType,
        "requestId" => $payment_requestId,
        "amount" => $request['amount'],
        "nonce" => $payment_nonces['salt'],
        // on the other hand, include these here even if you leave them out of the JS init
        "postbackUrl" => $request['postbackUrl'], // if not specified in the JS init, defaults to the empty string
        "environment" => $request['environment'], // defaults to "cert"
        "preAuth" => $request['preAuth'] // defaults to false
    ]; 
    
    $payment_authKey = getAuthKey(json_encode($payment_req), $developer['KEY'], $payment_nonces['salt'], $payment_nonces['iv']);
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
            apiKey: "<?php echo $developer['ID']; ?>",
            environment: "<?php echo $request['environment']; ?>",
            postbackUrl: "<?php echo $request['postbackUrl']; ?>",
            merchantId: "<?php echo $merchant['ID']; ?>",
            authKey: "<?php echo $vault_authKey; ?>",
            nonce: "<?php echo $vault_nonces['salt']; ?>",
            requestType: "<?php echo $vault_requestType; ?>",
            requestId: "<?php echo $vault_requestId; ?>",
            elementId: "vaultButton"
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
                    $("#paymentButton").prop('disabled', true);
                    $("#paymentResponse").text("The response will appear here as JSON, and in your browser console as a JavaScript object.");
                    $CORE.Initialize({
                        apiKey: "<?php echo $developer['ID']; ?>",
                        environment: "<?php echo $request['environment']; ?>",
                        postbackUrl: "<?php echo $request['postbackUrl']; ?>",
                        merchantId: "<?php echo $merchant['ID']; ?>",
                        authKey: "<?php echo $payment_authKey; ?>",
                        nonce: "<?php echo $payment_nonces['salt']; ?>",
                        requestType: "<?php echo $payment_requestType; ?>",
                        requestId: "<?php echo $payment_requestId; ?>",
                        amount: "<?php echo $request['amount']; ?>",
                    });
                    $REQ.doTokenPayment(vaultResponse.getVaultToken(), "123", function(paymentResponse) {
                        console.log(paymentResponse);
                        $RESP.tryParse(paymentResponse);
                        $("#paymentResponse").text(
                            $RESP.getResponse({"json": true})
                        );
                    });
                })
            } else {
                // ...
            }
        });
    });
</script>

