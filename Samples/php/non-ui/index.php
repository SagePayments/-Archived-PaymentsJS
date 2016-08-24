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
<style>
    .form-control{
        width: 250px;
    }
    #myCustomForm{
        font-family: "Comic Sans MS", cursive, sans-serif;

    }
    #paymentButton.not-disabled{
        background-color: hotpink;
        border-color: deeppink;
    }
</style>
<div class="wrapper text-center">
    <h1>Non-UI</h1>
    <p>If you need more flexibility than the PayJS UI offers, use the other modules to power your own payment form:</p>
</div>
<pre><code>    PayJS(['jquery', 'PayJS/Core', 'PayJS/Request', 'PayJS/Response', 'PayJS/Formatting'],
    function($, $CORE, $REQUEST, $RESPONSE, $FORMATTING) {
        $CORE.Initialize({
            <i>(...)</i>
        });
        $("#paymentButton").click(function() {    
            $REQUEST.doPayment(cc, exp, cvv, function(resp) {
                $RESPONSE.tryParse(resp);
                var isApproved = $RESPONSE.getTransactionSuccess();
                alert(isApproved ? "ka-ching!" : "womp womp...");
            })
        });
        $("#cc_number").blur(function() {
            var cc = $("#cc_number").val();
            cc = $FORMATTING.formatCardNumberInput(cc, '-');
            $("#cc_number").val(cc);
            if ($VALIDATION.isValidCreditCard(cc, cc[0])) {
                $("#cc-grp").addClass("has-success").removeClass("has-error");
            } else {
                $("#cc-grp").removeClass("has-success").addClass("has-error");
            }
        })
    });</code></pre>
<div class="wrapper text-center">
    <div>
        <form class="form" id="myCustomForm">
            <div class="form-group" id="cc-group">
                <label class="control-label">Credit Card Number</label>
                <input type="text" class="form-control" id="cc_number" value="" placeholder="eg, 54545454...">
                <span class="help-block"></span>
            </div>
            <div class="form-group" id="exp-group">
                <label class="control-label">Expiration Date</label>
                <input type="text" class="form-control" id="cc_expiration" value="" placeholder="eg, 12/20">
                <span class="help-block"></span>
            </div>
            <div class="form-group" id="cvv-group">
                <label class="control-label">CVV</label>
                <input type="text" class="form-control" id="cc_cvv" value="" placeholder="eg, 123">
                <span class="help-block"></span>
            </div>
            <button class="btn btn-primary" id="paymentButton">Pay Now</button>
        </form>
        <br /><br />
        <h5>Results:</h5>
        <p style="width:100%"><pre><code id="paymentResponse">The response will appear here as JSON, and in your browser console as a JavaScript object.</code></pre></p>
    </div>
</div>
<script type="text/javascript">
    PayJS(['jquery', 'PayJS/Core', 'PayJS/Request', 'PayJS/Response', 'PayJS/Formatting', 'PayJS/Validation'],
    function($, $CORE, $REQUEST, $RESPONSE, $FORMATTING, $VALIDATION) {

        $("#paymentButton").prop('disabled', true);

        var isValidCC = false,
            isValidExp = false,
            isValidCVV = false;

        $CORE.Initialize({
            apiKey: "<?php echo $developerId; ?>",
            merchantId: "<?php echo $merchantCredentials['MID']; ?>",
            authKey: "<?php echo $authKey; ?>",
            requestType: "<?php echo $requestType; ?>",
            requestId: "<?php echo $requestId; ?>",
            amount: "<?php echo $amount; ?>",
            debug: true,
            postbackUrl: "<?php echo $postbackUrl; ?>",
            nonce: "<?php echo $nonce; ?>",
        });

        $("#paymentButton").click(function() {
            $(this).prop('disabled', true).removeClass("not-disabled");
            $("#myCustomForm :input").prop('disabled', true);

            var cc = $("#cc_number").val();
            var exp = $("#cc_expiration").val();
            var cvv = $("#cc_cvv").val();

            $REQUEST.doPayment(cc, exp, cvv, function(resp) {
                $RESPONSE.tryParse(resp);
                console.log($RESPONSE.getResponse());
                $("#paymentResponse").text(
                    $RESPONSE.getResponse({ "json": true })
                );
                $("#myCustomForm").hide("slow");
            })
        })

        $("#cc_number").blur(function() {
            var cc = $("#cc_number").val();
            cc = $FORMATTING.formatCardNumberInput(cc, '-');
            $("#cc_number").val(cc);
            isValidCC = $VALIDATION.isValidCreditCard(cc, cc[0]);
            toggleClasses(isValidCC, $("#cc-group"));
            checkForCompleteAndValidForm();
        })


        $("#cc_expiration").blur(function() {
            var exp = $("#cc_expiration").val();
            exp = $FORMATTING.formatExpirationDateInput(exp, '/');
            $("#cc_expiration").val(exp);
            isValidExp = $VALIDATION.isValidExpirationDate(exp);
            toggleClasses(isValidExp, $("#exp-group"));
            checkForCompleteAndValidForm();
        })

        $("#cc_cvv").blur(function() {
            var cvv = $("#cc_cvv").val();
            $("#cc_cvv").val(cvv);
            isValidCVV = $VALIDATION.isValidCvv(cvv, $("#cc_number").val()[0]);
            toggleClasses(isValidCVV, $("#cvv-group"));
            checkForCompleteAndValidForm();
        })

        function toggleClasses(bool, obj) {
            if (bool) {
                obj.addClass("has-success").removeClass("has-error");
                obj.children(".help-block").text("valid entry");
            } else {
                obj.removeClass("has-success").addClass("has-error");
                obj.children(".help-block").text("invalid entry");
            }
        }

        function checkForCompleteAndValidForm() {
            // assuming most people fill out the form from top-to-bottom,
            // checking it from bottom-to-top takes advantage of short-circuiting
            if (isValidCVV && isValidExp && isValidCC) {
                $("#paymentButton").prop('disabled', false).addClass("not-disabled");
            }
        }
    });
</script>

