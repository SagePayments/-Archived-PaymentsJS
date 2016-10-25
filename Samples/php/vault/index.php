<?php
    require('../shared/shared.php');
    
    // for the vault request:
    $vault_nonces = getNonces();
    $vault_req = [
        "merchantId" => $merchant['ID'],
        "merchantKey" => $merchant['KEY'], // don't include the Merchant Key in the JavaScript initialization!
        "requestType" => "vault",
        "orderNumber" => "Invoice" . rand(0, 1000),
        "salt" => $vault_nonces['salt'],
        "postbackUrl" => $request['postbackUrl']
    ]; 
    $vault_authKey = getAuthKey(json_encode($vault_req), $developer['KEY'], $vault_nonces['salt'], $vault_nonces['iv']);
    
    // for the payment request:
    $payment_nonces = getNonces();
    $payment_req = [
        "merchantId" => $merchant['ID'],
        "merchantKey" => $merchant['KEY'], // don't include the Merchant Key in the JavaScript initialization!
        "requestType" => "payment",
        "orderNumber" => "Invoice" . rand(0, 1000),
        "amount" => $request['amount'],
        "salt" => $payment_nonces['salt'],
        "postbackUrl" => $request['postbackUrl'],
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
            clientId: "<?php echo $developer['ID']; ?>",
            postbackUrl: "<?php echo $vault_req['postbackUrl']; ?>",
            merchantId: "<?php echo $vault_req['merchantId']; ?>",
            authKey: "<?php echo $vault_authKey; ?>",
            salt: "<?php echo $vault_req['salt']; ?>",
            requestType: "<?php echo $vault_req['requestType']; ?>",
            orderNumber: "<?php echo $vault_req['orderNumber']; ?>",
            elementId: "vaultButton",
            addFakeData: true
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
                        clientId: "<?php echo $developer['ID']; ?>",
                        postbackUrl: "<?php echo $payment_req['postbackUrl']; ?>",
                        merchantId: "<?php echo $payment_req['merchantId']; ?>",
                        authKey: "<?php echo $payment_authKey; ?>",
                        salt: "<?php echo $payment_req['salt']; ?>",
                        requestType: "<?php echo $payment_req['requestType']; ?>",
                        orderNumber: "<?php echo $payment_req['orderNumber']; ?>",
                        amount: "<?php echo $payment_req['amount']; ?>",
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

