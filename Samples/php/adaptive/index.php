@using PayJS_Samples.Misc
@{
    string MerchantId = "417227771521";
    string MerchantKey = "I5T2R2K6V1Q3";
    string RequestId = "Invoice" + (new Random()).Next(100).ToString();
    string Nonce = Guid.NewGuid().ToString();
    string PostbackUrl = "https://www.example.com/";

    string RequestType = "payment";
    string Amount = "1.00";
    //string RequestType = "vault";
    //string Amount = String.Empty;

    string CombinedString = RequestType + RequestId + MerchantId + PostbackUrl + Nonce + Amount;
    string AuthKey = Hmac.GetHmac(CombinedString, MerchantKey);
}
<style>
    .resizeable {
        resize:horizontal;
        overflow: auto;
        margin-left: auto;
        margin-right: auto;
        padding: 15px;
        border-width: thin;
        border-style: dotted;
        border-color: #3c424f;
    }
</style>

<div class="wrapper text-center">
    <h1>Adaptive</h1>
    <p>The PayJS UI will adjust itself to the size of its container. Try stretching out this <code>&lt;div&gt;</code>:</p>
    <br />
    <div>
        <div class="resizeable" id="paymentDiv" style="width:35%"></div>
        <br /><br />
        <h5>Results:</h5>
        <p style="width:100%"><pre><code id="paymentResponse">The response will appear here as JSON, and in your browser console as a JavaScript object.</code></pre></p>
    </div>
</div>
<script type="text/javascript">
    PayJS(['PayJS/UI', 'jquery'],
    function($UI, $) {
        $UI.Initialize({
            apiKey: "GvVtRUT9hIchmOO3j2ak4JgdGpIPYPG4",
            merchantId: "@MerchantId",
            authKey: "@AuthKey",
            requestType: "@RequestType",
            requestId: "@RequestId",
            amount: "@Amount",
            elementId: "paymentDiv",
            debug: true,
            postbackUrl: "@PostbackUrl",
            phoneNumber: "1-800-555-1234",
            nonce: "@Nonce",
            //modalTitle: "Potatoes",
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

