const crypto = require('crypto-js');

const config = {
    merchant: {
        id: "999999999997",
        key: "K3QD6YWyhfD",
    },
    developer: {
        id: "GTq2h4mXxLIBtzbOWLO2GwqZfOgK8BbT",//"7SMmEF02WyC7H5TSdG1KssOQlwOOCagb",
        key: "ICkrA2n6HIleJ663",//"wtC5Ns0jbtiNA8sP",
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

function getAuthedRequest(){
    const newRequest = getBaseRequest();
    const nonces = getSecureNonces();
    newRequest.orderNumber =  Date.now().toString();
    newRequest.salt = nonces.salt;
    newRequest.merchantKey = config.merchant.key;
    newRequest.authKey = getAuthKey(JSON.stringify(newRequest), nonces, config.developer.key);
    delete newRequest.merchantKey;
    return newRequest;
}

function getSecureNonces(){
    const iv = crypto.lib.WordArray.random(16)
    const salt = crypto.enc.Base64.stringify(crypto.enc.Utf8.parse(crypto.enc.Hex.stringify(iv)));
    return {
        iv: iv,
        salt: salt
    }
}

function getAuthKey(message, nonces, secret){
    var derivedPassword = crypto.PBKDF2(secret, nonces.salt, { keySize: 256/32, iterations: 1500, hasher: crypto.algo.SHA1 });
    var encrypted = crypto.AES.encrypt(message, derivedPassword, { iv: nonces.iv });
    return encrypted.toString();
}

module.exports = {
    getInitialization: () => {
        return getAuthedRequest();
    }
}