const crypto = require('crypto-js');

const config = {
    merchant: {
        id: "173859436515",
        key: "P1J2V8P2Q3D8",
    },
    developer: {
        id: "7SMmEF02WyC7H5TSdG1KssOQlwOOCagb",
        key: "wtC5Ns0jbtiNA8sP",
    },
    postbackUrl: "https://www.example.com/myHandler.php", // https://requestb.in is great for playing with this
    environment: "cert",
    amount: "1.00",
    preAuth: "false",
    requestType: "payment",
}

function getBaseRequest() {
    return {
        clientId: config.developer.id,
        postbackUrl: config.postbackUrl, // you get a copy of the response here
        merchantId: config.merchant.id,
        authKey: undefined,
        salt: undefined,
        requestType: config.requestType,
        orderNumber: undefined,
        amount: config.amount,
    };
}

function getPreppedBaseRequest() {
    const newRequest = getBaseRequest();
    const nonces = getSecureNonces();
    newRequest.orderNumber =  Date.now().toString();
    newRequest.salt = nonces.salt;
    newRequest.merchantKey = config.merchant.key;
    return [newRequest, nonces];
}

function getAuthedRequest() {
    const br = getPreppedBaseRequest();
    const newRequest = br[0];
    const nonces = br[1];
    newRequest.authKey = getAuthKey(JSON.stringify(newRequest), nonces, config.developer.key);
    delete newRequest.merchantKey;
    return newRequest;
}

function getCustomRequest(customValues) {
    const br = getPreppedBaseRequest();
    const newRequest = br[0];
    const nonces = br[1];
    Object.keys(customValues).map(key => newRequest[key] = customValues[key]);
    newRequest.authKey = getAuthKey(JSON.stringify(newRequest), nonces, config.developer.key);
    delete newRequest.merchantKey;
    return newRequest;
}

function getSecureNonces() {
    const iv = crypto.lib.WordArray.random(16)
    const salt = crypto.enc.Base64.stringify(crypto.enc.Utf8.parse(crypto.enc.Hex.stringify(iv)));
    return {
        iv: iv,
        salt: salt
    }
}

function getAuthKey(message, nonces, secret) {
    var derivedPassword = crypto.PBKDF2(secret, nonces.salt, { keySize: 256/32, iterations: 1500, hasher: crypto.algo.SHA1 });
    var encrypted = crypto.AES.encrypt(message, derivedPassword, { iv: nonces.iv });
    return encrypted.toString();
}

function getHmac(string, secret) {
    const hmac = crypto.HmacSHA512(string, secret);
    return crypto.enc.Base64.stringify(hmac);
}

module.exports = {
    getInitialization: () => getAuthedRequest(),
    getCustomInitialization: customValues => getCustomRequest(customValues),
    getResponseHashes: response => ({
        hash: getHmac(response, config.developer.key),
    })
}