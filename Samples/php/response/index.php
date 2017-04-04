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
    <h1>Response Integrity</h1>
    <p>The response headers returned from the API includes a hash of the body, which should be used to verify its integrity. This page runs a transaction upon loading; when you're ready, send it to the server for hash verification:</p>
    <br />
    <div>
        <h5>Result:</h5>
        <textarea id="paymentResponse" style="font-family: monospace; width:100%;" rows="6"></textarea>
        <br /><br />
        <button class="btn btn-primary" id="verifyButton" disabled>Verify</button>
        <br /><br />
        <h5>Hash Verification:</h5>
        <div class="compare">
            <input type="text" class="form-control disabled" id="clientHash" style="font-family: monospace; width:48%;" placeholder="">
            <input type="text" class="form-control disabled" id="serverHash" style="font-family: monospace; width:48%;" placeholder="">
        </div>
    </div>
</div>
<script type="text/javascript">

    PayJS(['PayJS/Core', 'PayJS/Request', 'PayJS/Response', 'jquery'],
    function($CORE, $REQ, $RESP, $) {

        $CORE.Initialize({
            clientId: "<?php echo $developer['ID']; ?>",
            postbackUrl: "<?php echo $req['postbackUrl']; ?>",
            merchantId: "<?php echo $req['merchantId']; ?>",
            authKey: "<?php echo $authKey; ?>",
            salt: "<?php echo $req['salt']; ?>",
            requestType: "<?php echo $req['requestType']; ?>",
            orderNumber: "<?php echo $req['orderNumber']; ?>",
            amount: "<?php echo $req['amount']; ?>",
        });
        $REQ.doPayment("5454545454545454", "1220", "123", function(data, status, jqxhr) {
            $("#verifyButton").prop('disabled', false);
            $RESP.tryParse(data, status, jqxhr);
            $("#paymentResponse").text($RESP.getApiResponse());
            $("#verifyButton").click(function() {
                $("#verifyButton").prop('disabled', true);
                var editedResponse = $("#paymentResponse").val();
                $('#clientHash').val($RESP.getResponseHash().hash);
                $.post(
                    "response/verify.php",
                    editedResponse,
                    function(r) {
                        $("#verifyButton").prop('disabled', false);
                        $('#serverHash').val(r.hash);
                        $('.compare').each(function(index, element) {
                            var kids = $(element).children();
                            var client = $(kids[0]).val();
                            var server = $(kids[1]).val();
                            kids.css("color", client === server ? 'green' : 'red');
                        })
                    },
                    "json"
                );
            })
        })

        function getEditedResponseText() {
            console.log($("#paymentResponse").text());
            console.log();
            return $("#paymentResponse").text();
        }
    });
</script>