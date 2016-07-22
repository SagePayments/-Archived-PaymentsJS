@using PayJS_Samples.Misc
@{
    string MerchantId = "417227771521";
    string MerchantKey = "I5T2R2K6V1Q3";
    string RequestId = "Invoice" + (new Random()).Next(100).ToString();
    string Nonce = Guid.NewGuid().ToString();
    string PostbackUrl = "https://www.example.com";

    string RequestType = "payment";
    string Amount = HttpContext.Current.Request.QueryString["amount"];

    string CombinedString = RequestType + RequestId + MerchantId + PostbackUrl + Nonce + Amount;
    string AuthKey = Hmac.GetHmac(CombinedString, MerchantKey);
    HttpContext.Current.Response.ContentType = "text/json";
}
{
    "authKey": "@AuthKey",
    "invoice": "@RequestId",
    "nonce": "@Nonce",
    "merch": "@MerchantId"
}
