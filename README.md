![SagePaymentsJSLogoGreen](https://raw.githubusercontent.com/SagePayments/PaymentsJS/master/Samples/dotnet/shared/img/logo-sage-paymentsjs-@2x.png)
![SagePaymentsJSUI](https://developer.sagepayments.com/sites/default/files/payjs_v1.png)
---

PaymentsJS is a JavaScript library that enables developers to quickly start processing credit cards on their website. The library includes a pre-built user interface, while also exposing the underlying methods for use in applications with more strict UI/UX requirements. And whichever route you go, the credit card data never touches your server.

1. [Quick Start](#QuickStart)
1. [Authentication & Verification](#Authentication)
1. [Modules](#Modules)
1. [RequireJS](#RequireJS)
1. [API Reference](#Reference)

---
## <a name="QuickStart"></a>Quick Start

Add the script to your page:

```html
<script type="text/javascript" src="https://www.sagepayments.net/pay/js/pay.min.js"></script>
```

And, just for the sake of this sample, add a button:

```html
<button id="paymentButton">Pay Now</button>
```

Then, in a separate `<script>` tag, initialize the library:

```javascript
PayJS(['PayJS/UI'], // the name of the module we want to use
function($UI) { // assigning the module to a variable
    $UI.Initialize({ // configuring the UI
        apiKey: "GvVtRUT9hIchmOO3j2ak4JgdGpIPYPG4", // your developer ID
        merchantId: "417227771521", // your 12-digit account identifier
        authKey: "H1x4ECB6TkeTSfkABNQXHNs5=", // covered in the next section!
        requestType: "payment", // use can use "vault" to tokenize a card without charging it
        requestId: "Invoice12345", // an order number, customer ID, etc.
        amount: "1.00", // the amount to charge the card. in test mode, different amounts produce different results.
        elementId: "paymentButton", // the page element that will trigger the UI
        nonce: "ThisIsTotallyUnique", // a unique value; duplicates will be rejected
        debug: true // enables verbose console logging
    });
    $UI.setCallback(function(result) { // custom code that will execute when the UI receives a response
        console.log(result.getResponse()); // log the result to the console
        var wasApproved = result.getTransactionSuccess();
        alert(wasApproved ? "ka-ching!" : "bummer");
    });
});

```
At this point, clicking on `paymentButton` will make the payment form pop up! You can attempt a transaction, but it will be rejected... so our next step is to calculate the `authKey`.

---
## <a name="Authentication"></a>Authentication & Verification

#### <a name="authKey"></a>authKey

Credit card data moves directly between the user's browser and Sage Payment Solutions' secure payment gateway. This is great news for your server, which doesn't have to touch any sensitive data! But, as with any client-side code, it means we have to take seriously the possibility of malicious users making changes to the request.

The `authKey` protects your request from tampering by using your secret Merchant Key to create a hash of the configuration settings. We'll calculate one on our end; if it doesn't match the one we received in the request, we'll slam the door.

Here's how to do the calculation, in pseudocode:

```javascript
var combinedString = requestType + requestId + merchantId + postbackUrl + nonce + amount;
var hmac = SHA512(combinedString, merchantKey);
var authKey = Base64(hmac);
```

Samples in specific server-side languages are available in the `samples` folder of this repository; look for a file named `Hmac` in the `shared` folder of your preferred language's folder.

#### <a name="respHash"></a>Response Hash

Similarly, when we send the response back to the client, it will include a hash of the response using your Merchant Key as the private key. *Always calculate & compare this server-side before updating any orders, databases, etc.*

---
## <a name="Modules"></a>Modules

PaymentsJS uses [RequireJS](http://requirejs.org/) to manage its components. Your page will only load the modules that you specify (plus any unspecified dependencies).

The following modules contain methods that you may want to use in your project:

Name | Description
---- | -----------
"jquery" | Version 2.0 of [the common JavaScript library](https://jquery.com/).
"PayJS/Core" | Manages configuration settings when _not_ using the UI.
"PayJS/UI" | Manages configuration settings when using the UI.
"PayJS/Request" | Sends transaction and vault requests to the gateway. 
"PayJS/Response" | Reads information out of responses from the gateway.
"PayJS/Formatting" | Converts credit card data into standardized strings.
"PayJS/Validation" | Checks credit card data for acceptable values.

Additionally, the following dependency-modules will probably *not* be particularly useful, but are listed here for the sake of completeness:

Name | Description
---- | -----------
"PayJS/Extensions" | Extends the `jquery` module with certain [Bootstrap](http://getbootstrap.com/javascript/) components.
"PayJS/UI.html" | Provides the `UI` module with the HTML that it uses to build the payment form.

---
## <a name="RequireJS"></a>RequireJS

If you're already using RequireJS on your page, add a path to PaymentsJS --
```
requirejs.config({
    paths: {
        "PayJS": 'https://www.sagepayments.net/pay/js/build'
    },
});
```
-- and then use it like you would any other module.

Please keep in mind that you'll also need to [provide your own jQuery dependency](http://requirejs.org/docs/jquery.html) if you don't already have one.

---
## <a name="Reference"></a>API Reference

- [Module Loading - PayJS()](#ref.Intro)
- [PayJS/Core](#ref.Core)
  - [Initialize()](#ref.Core.Initialize)
  - [isInitialized()](#ref.Core.isInitialized)
  - [getters](#ref.Core.getters)
- [PayJS/UI](#ref.UI)
  - [Initialize()](#ref.UI.Initialize)
  - [isInitialized()](#ref.UI.isInitialized)
  - [setCallback()](#ref.UI.setCallback)
- [PayJS/Request](#ref.Request)
  - [doPayment()](#ref.Request.doPayment)
  - [doVault()](#ref.Request.doVault)
  - [doTokenPayment()](#ref.Request.doTokenPayment)
- [PayJS/Response](#ref.Response)
  - [tryParse()](#ref.Response.tryParse)
  - [getResponse()](#ref.Response.getResponse)
  - [getters](#ref.Response.getters)
- [PayJS/Formatting](#ref.Formatting)
  - [formatCardNumberInput()](#ref.Formatting.formatCardNumberInput)
  - [formatExpirationDateInput()](#ref.Formatting.formatExpirationDateInput)
  - [stripNonNumeric()](#ref.Formatting.stripNonNumeric)
  - [stripNonAlphanumeric()](#ref.Formatting.stripNonAlphanumeric)
- [PayJS/Validation](#ref.Validation)
  - [isValidCreditCard()](#ref.Validation.isValidCreditCard)
  - [isValidExpirationDate()](#ref.Validation.isValidExpirationDate)
  - [isValidCvv()](#ref.Validation.isValidCvv)
  - [getExpArray()](#ref.Validation.getExpArray)
    
### <a name="ref.Intro"></a>Loading Modules - PayJS()
The entire PaymentsJS library is accessed through the `PayJS` function, which takes two arguments:

```javascript
PayJS(
    // first, a string array containing the names of the modules you want to use
    ['moduleOne', 'moduleTwo'],
    // and second a function, inside which you can use those modules
    function(m1, m2){ // moduleOne is assigned to m1, moduleTwo to m2
        m1.doSomething();
        m2.doSomethingElse();
    }
);
```
Or, less generically:
```javascript
PayJS(['PayJS/Core', 'PayJS/Request'],
function(CORE, REQ){
    CORE.Initialize(/*...*/);
    REQ.doPayment(/*...*/);
});

```
---
## <a name="ref.Core"></a>PayJS/Core
The Core module's main role is to share common settings among the other modules.

#### <a name="ref.Core.Initialize"></a>Initialize
Configures the library for a gateway request. If you're running payments using the [`PayJS/Request`](#ref.Request) module, use this instead of [`UI.Initialize()`](#ref.UI.Initialize).

This method takes a single argument:

```javascript
// pass this method an object literal containing your configuration settings
CORE.Initialize({
    someValue: "123456",
    mySetting: true
});
```

The configuration object can contain:

Name | Description | Values | Length | Required | Default
---- | ----------- | ------ | ------ | -------- | -------
debug | toggles verbose logging to browser console | boolean | N/A | no | false
environment | chooses between the certification and production environments | "cert" or "prod" | 4 | no | cert
apiKey | your developer id | alphanumeric string | 32 | yes | N/A
merchantId | identifies your gateway account | numeric string | 12 | yes | N/A
authKey | verifies request integrity | base-64 string | 88 | yes | N/A
requestId | an identifier of your choosing | string | 1+ | yes | N/A
requestType | chooses between charging or tokenizing a card | "payment" or "vault" | N/A | yes | N/A
nonce | any unique value; duplicates will be rejected | string | any | yes | N/A
amount | the amount to charge the card | 1, "1", 1.00, "1.00", etc. | 1-4 | when requestType = "payment" | N/A
preAuth | toggles between authorization-only and authorization & capture | boolean | N/A | no | false (auth & cap)
postbackUrl | a URL that will receive a copy of the gateway response | valid URI with https scheme | any | no | ""
billing | add billing information (address/etc.) to the transaction request | see [`CORE.setBilling()`](#ref.Core.setBilling) | N/A | no | none/empty


#### <a name="ref.Core.isInitialized"></a>isInitialized
Returns a boolean that represents whether the module has been successfully initialized.

This method does not take any arguments:

```javascript
CORE.isInitialized();
// => false
CORE.Initialize(validSettings)
CORE.isInitialized();
// => true
```

#### <a name="ref.Core.setBilling"></a>setBilling
Adds billing information to a transaction request.

This takes a single argument:

```javascript
CORE.setBilling({
    name: "John Smith",
    street: "123 Address St",
    city: "Denver",
    state: "CO",
    postalCode: "12345",
    country: "USA"
});
```
Notes:

- Billing information can also be set during initialization.


#### <a name="ref.Core.getters"></a>getters
These methods return information about the current configuration:

```javascript
CORE.getMerchantId();
// => "123456789123"
CORE.getAuthKey();
// => "H1x4ECB6TkeTSfkABNQXHNs5="
CORE.getRequestId();
// => "Invoice123"
CORE.getAmount();
// => "1.00"
CORE.getRequestType();
// => "payment"
CORE.getPreAuth();
// => false
CORE.getPostbackUrl();
// => "https://www.example.com/myHandler.php"
CORE.getNonce();
// => "NoncesAreCool"
CORE.getPhonenumber();
// => "800-555-1234"
CORE.getBilling();
// => Object {name: "John Smith", street: "123 Address St", state: "CO", postalCode: "12345", country: "USA"}
```
---
### <a name="ref.UI"></a>PayJS/UI


The UI module adds a pre-built payment form to your website.

#### <a name="ref.UI.Initialize"></a>Initialize
Configures the library for a gateway request. If you're using the pre-built payment form, use this instead of `CORE.Initialize()`.

This method takes a single argument:

```javascript
// pass this method an object literal containing your configuration settings
UI.Initialize({
    someValue: "123456",
    mySetting: true
});
```

In addition to the information outlined in [`CORE.Initialize()`](#ref.Core.Initialize), this configuration object can contain:

Name | Description | Values | Length | Required | Default
---- | ----------- | ------ | ------ | -------- | -------
targetElement | the id of the html element to which the UI will attach | string | any | yes | N/A
suppressResultPage | hide the approved/declined pages that show after a gateway request | boolean | N/A | no | false
restrictInput | limits user entry to acceptable characters | boolean | N/A | no | true
formatting | after the user enters their credit card number, the form will remove invalid characters and add dashes | boolean | N/A | no | true
phoneNumber | displayed as a support number for declined transactions | string | any | no | none
show | automatically show the modal UI when ready | boolean | N/A | no | false
addFakeData | adds fake credit card data to the form, for testing | boolean | N/A | no | false

Notes:

- If `targetElement` refers to a `<button>`, `<a>`, or `<input>`, the UI will appear as a [modal dialog](http://getbootstrap.com/javascript/#modals) when that element is clicked. If it refers to a `<div>`, the UI will be put *inside* the element. Other element types will probably just do something weird.
- If `suppressResultPage` is enabled, the UI will never move past the processing bar. This can be used in conjunction with [`UI.setCallback()`](#ref.UI.setCallback) to customize response handling (eg, redirecting to another page).
- We do not recommend changing `restrictInput` or `formatting` to `false`. (These options may be deprecated in a future release.)

#### <a name="ref.UI.isInitialized"></a>isInitialized
Returns a boolean that represents whether the module has been successfully initialized.

This method does not take any arguments:

```javascript
UI.isInitialized();
// => false
UI.Initialize(validSettings)
UI.isInitialized();
// => true
```

#### <a name="ref.UI.setCallback"></a>setCallback
Sets a function that executes after a request completes.

This method takes a single argument:

```javascript
var myCallback = function(RESP){
    SendResultToServer(RESP.getResponse({ json: true }))
    var wasApproved = RESP.getTransactionSuccess();
    RedirectUser(wasApproved ? "approved.html" : "declined.html");
};
UI.setCallback(myCallback);
```

Notes:

- The argument to your callback function is an object with all the methods of a [`PayJS/Response`](#ref.Response) module.
  - This object will have already had [`RESPONSE.tryParse()`](#ref.Response.tryParse) called.
- Always check [the response hash](#respHash) server-side to verify the integrity of the response.


---
### <a name="ref.Request"></a>PayJS/Request
The Request module sends transaction and vault requests to the payment gateway.

#### <a name="ref.Request.doPayment"></a>doPayment
Charges a credit card.

This method takes four arguments:

```javascript
REQUEST.doPayment(cardNumber, expirationDate, cvv, callbackFunction);
```

Notes:

- The argument to your callback function is a JSON string.
  - Pass the string into [`RESPONSE.tryParse()`](#ref.Response.tryParse) to initialize the [`PayJS/Response`](#ref.Response) module's [getters](#ref.Response.getters).
- Always check [the response hash](#respHash) server-side to verify the integrity of the response.


#### <a name="ref.Request.doVault"></a>doVault
Tokenizes a credit card without charging it. The token can be used later to charge the card.

This method takes three arguments (CVVs can not be stored):

```javascript
REQUEST.doVault(cardNumber, expirationDate, callbackFunction);
```

Notes:

- The argument to your callback function is a JSON string.
  - Pass the string into [`RESPONSE.tryParse()`](#ref.Response.tryParse) to initialize the [`PayJS/Response`](#ref.Response) module's [getters](#ref.Response.getters).
- Always check [the response hash](#respHash) server-side to verify the integrity of the response.

#### <a name="ref.Request.doTokenPayment"></a>doTokenPayment
Charges a credit card using a vault token.

This method takes three arguments:

```javascript
REQUEST.doTokenPayment(vaultToken, cvv, callbackFunction);
```

Notes:

- An empty string is an acceptable CVV value; however, to maximize the chances of the cardholder's bank approving the transaction, it is always preferable to collect and include a CVV whenever possible.
- The argument to your callback function is a JSON string.
  - Pass the string into [`RESPONSE.tryParse()`](#ref.Response.tryParse) to initialize the [`PayJS/Response`](#ref.Response) module's [getters](#ref.Response.getters).
- Always check [the response hash](#respHash) server-side to verify the integrity of the response.

---
### <a name="ref.Response"></a>PayJS/Response
The Response module exposes methods for traversing transaction results.

#### <a name="ref.Response.tryParse"></a>tryParse
Attempts to initialize this module's [getters](#ref.Response.getters) from a JSON string.

This method takes a single argument:

```javascript
RESPONSE.tryParse(gatewayResponse);
// => true
```

Notes:

- This method should be used in the callback functions of the [`PayJS/Request`](#ref.Request) module's methods.
  - Pass the string into [`RESPONSE.tryParse()`](#ref.Response.tryParse) to initialize the [`PayJS/Response`](#ref.Response) module's [getters](#ref.Response.getters).
  - If this method returns `false`, use [`RESPONSE.getResponse()`](#ref.Response.getResponse) to view the unparsed result.


#### <a name="ref.Response.getResponse"></a>getResponse
Returns the result of the gateway request.

This method takes a single *optional* argument:

```javascript
RESPONSE.getResponse(); // without an argment, the method returns an object
// => Object {Response: Object, Hash: undefined}

RESPONSE.getResponse({ json: true }); // pass a configuration object to retrieve a json string instead 
// => "{ "Response": {"status":"Approved", ... }, "Hash": "eJ5QcIL3nOE9exJnns9WPGso8xQEMoho4335jCteDdjL2NJfROMxmbbxAg87yW9dAI20QXi7s3jDDllJEF/mJA==" }"
```

Notes:

- When using the [`PayJS/Request`](#ref.Request) module's methods, you must call [`RESPONSE.tryParse()`](#ref.Response.tryParse) before this method is available. The [`PayJS/UI`](#ref.UI) module does this for you.
  - If [`RESPONSE.tryParse()`](#ref.Response.tryParse) succeeded, the module's [getters](#ref.Response.getters) will be available.
  - If [`RESPONSE.tryParse()`](#ref.Response.tryParse) failed, this method returns a [jqXHR](https://api.jquery.com/jQuery.ajax/#jqXHR) object. Use the base [XMLHttpRequest properties](https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest) to check the `status`, `responseText`, etc.
- Always check [the response hash](#respHash) server-side to verify the integrity of the response.

#### <a name="ref.Response.getters"></a>getters
This module has various getters that return information about the gateway request.

These methods do not take any arguments:

```javascript
// returns true if the payment request was approved; otherwise, false
RESPONSE.getTransactionSuccess();
// => true

// returns true if the vault request was approved; otherwise, false
RESPONSE.getVaultSuccess();
// => true

// returns the token representing the credit card in the vault
RESPONSE.getVaultToken();
// => "d01a3475-42ad-4d7e-b0a6-76e4ea1abec6"

// returns the approval or decline code
RESPONSE.getCode();
// => "123456"

// returns the approval or decline message
RESPONSE.getMessage();
// => "APPROVED"

// returns the unique gateway identifier for the payment
RESPONSE.getReference();
// => "123456789123"

// returns the payment's order number
RESPONSE.getOrderNumber();
// => "123456789123"
```

These methods can take a configuration object as a single *optional* argument:

```javascript
// without any configuration, this method returns a single character representing the result of an AVS check
// (see: https://en.wikipedia.org/wiki/Address_Verification_System)
RESPONSE.getAVS();
// => "Z"

// use the "require" option to test whether the actual AVS result meets a certain level (or higher)
RESPONSE.getAVS({ require: "none" }); // no match
// => true
RESPONSE.getAVS({ require: "partial" }); // partial match (address OR zip)
// => true
RESPONSE.getAVS({ require: "exact" }); // exact match (address OR zip)
// => false

// use the "require" option with the "test" option to test the given code for a certain match-level, irrespective of the actual AVS result
RESPONSE.getAVS({ require: "none", test: "M" });
// => true
RESPONSE.getAVS({ require: "partial",  test: "M" });
// => true
RESPONSE.getAVS({ require: "exact", test: "M" });
// => true
```
```javascript
// without any configuration, this method returns a single character representing the result of a CVV check
RESPONSE.getCVV();
// => "M"

// use the "require" option to determine whether it was a match or not
RESPONSE.getCVV({ require: "match" }); // no match
// => true
```
---
### <a name="ref.Formatting"></a>PayJS/Formatting
The Formatting module converts strings into default or specified formats.
#### <a name="ref.Formatting.formatCardNumberInput"></a>formatCardNumberInput
Converts a string into a formatted credit card number.

This method can take one or two arguments:

```javascript
// pass the string to format
FORMATTING.formatCardNumberInput("5454545454545454");
// => "5454-5454-5454-5454"
FORMATTING.formatCardNumberInput("371449635392376");
// => "3714-496353-92376"

// pass a delimiter to use instead of a dash
FORMATTING.formatCardNumberInput("4111111111111111", "$");
// => "4111$1111$1111$1111"

// non-numeric characters are removed:
FORMATTING.formatCardNumberInput("4111-1111_1111a1111", "");
// => "4111111111111111"
````
  
#### <a name="ref.Formatting.formatExpirationDateInput"></a>formatExpirationDateInput
Converts a string into a formatted expiration date.

This method can take one or two arguments:

```javascript
// pass the string to format
FORMATTING.formatExpirationDateInput("1216");
// => "12/16"

// pass a delimiter to use instead of a slash
FORMATTING.formatExpirationDateInput("1216", "~");
// => "12~16"
````
Notes:
- See [`VALIDATION.getExpArray()`](#ref.Validation.getExpArray) for more on expiration date string parsing.

#### <a name="ref.Formatting.stripNonNumeric"></a>stripNonNumeric
Removes from a string any characters other than digits.

This method takes a single argument:

```javascript
FORMATTING.stripNonNumeric("abcd1234!@#$");
// => "1234"
```

#### <a name="ref.Formatting.stripNonAlphanumeric"></a>stripNonAlphanumeric
Removes from a string any characters other than digits, letters, and underscores.

This method takes a single argument:

```javascript
FORMATTING.stripNonAlphanumeric("abcd1234!@#$");
// => "abcd1234"
```

---
### <a name="ref.Validation"></a>PayJS/Validation
The Validation module tests that strings meet certain validity criteria.
#### <a name="ref.Validation.isValidCreditCard"></a>isValidCreditCard
Tests a credit card string for invalid characters, appropriate length, and [mod10](https://en.wikipedia.org/wiki/Luhn_algorithm).

This method can take one or two arguments:

```javascript
// pass the string to validate
VALIDATION.isValidCreditCard("5454545454545454");
// => true

// pass a card type to check validity for that particular type
VALIDATION.isValidCreditCard("5454545454545454", "3");
// => false
````
Notes:
- This method will allow dashes; all other non-numeric characters will result in `false`.
- This method expects American Express cards to have 15-digit cardnumbers; all other cardnumbers are expected to be 16 digits.
- The card type is represented by the first digit of that card type.
  - `3` -- American Express
  - `4` -- Visa
  - `5` -- MasterCard
  - `6` -- Discover


#### <a name="ref.Validation.isValidExpirationDate"></a>isValidExpirationDate
Tests an expiration date string for invalid characters, impossible month, or past date.

This method takes a single argument:

```javascript
VALIDATION.isValidExpirationDate("1220");
// => true

// expired
VALIDATION.isValidExpirationDate("1215");
// => false

// impossible date
VALIDATION.isValidExpirationDate("1320");
// => false
```

Notes:
- This method will allow slashes; all other non-numeric characters will result in `false`.
- See [`VALIDATION.getExpArray()`](#ref.Validation.getExpArray) for more on expiration date string parsing.


#### <a name="ref.Validation.isValidCvv"></a>isValidCvv
Tests a CVV string for invalid characters and appropriate length.

This method takes two arguments:

```javascript
// pass the string to validate, and a card type to check validity for that particular type
VALIDATION.isValidCvv("123", "4");
// => true
VALIDATION.isValidCvv("1234", "4");
// => false
VALIDATION.isValidCvv("1234", "3");
// => true
````

Notes:
- This method expects American Express cards to have 4-digit CVVs ("CIDs"); all other CVVs are expected to be 3 digits.
- The card type is represented by the first digit of that card type.
  - `3` -- American Express
  - `4` -- Visa
  - `5` -- MasterCard
  - `6` -- Discover
  
  
#### <a name="ref.Validation.getExpArray"></a>getExpArray
Returns a string array containing an expiration date as `["MM", "YY"]`.

This method takes a single argument:

```javascript
// with a slash
VALIDATION.getExpArray("01/18"); // MM/YY
// => ["01", "18"];
VALIDATION.getExpArray("1/18"); // M/YY
// => ["01", "18"];
VALIDATION.getExpArray("01/2018"); // MM/YYYY
// => ["01", "18"];
VALIDATION.getExpArray("1/2018"); // M/YYYY
// => ["01", "18"];

// without a slash
VALIDATION.getExpArray("0118"); // MMYY
// => ["01", "18"];
VALIDATION.getExpArray("118"); // MYY
// => ["01", "18"];
VALIDATION.getExpArray("012018"); // MMYYYY
// => ["01", "18"];
VALIDATION.getExpArray("12018"); // MYYYY
// => ["01", "18"];
```
Notes:
- Despite its parent module, this method does *not* validate the string.
  - [`VALIDATION.isValidExpirationDate()`](#ref.Validation.isValidExpirationDate) calls this method before validating.