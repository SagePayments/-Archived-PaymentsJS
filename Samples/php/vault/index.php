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
            $("#paymentResponse").text(
                vaultResponse.getApiResponse()
            );
            if (vaultResponse.getVaultSuccess()) {
                $("#vaultButton").prop('disabled', true);
                $.get(
                    "vault/auth.php",
                    {
                        token: vaultResponse.getVaultToken(),
                    },
                    function(authResp) {
                        $CORE.Initialize({
                            clientId: authResp.clientId,
                            merchantId: authResp.merch,
                            authKey: authResp.authKey,
                            requestType: "payment",
                            orderNumber: authResp.invoice,
                            amount: authResp.amount,
                            postbackUrl: authResp.postback,
                            salt: authResp.salt
                        });
                        $("#paymentButton").prop('disabled', false);
                        $("#paymentButton").click(function() {
                            $("#paymentButton").prop('disabled', true);
                            $("#paymentResponse").text("The response will appear here as JSON, and in your browser console as a JavaScript object.");
                            $REQ.doTokenPayment(vaultResponse.getVaultToken(), "123", function(paymentResponse) {
                                $RESP.tryParse(paymentResponse);
                                $("#paymentResponse").text(
                                    $RESP.getApiResponse()
                                );
                            });
                        })
                    },
                    "json"
                );
            } else {
                // ...
            }
        });
    });
</script>

