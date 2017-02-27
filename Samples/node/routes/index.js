const payjs = require('../services/sagePayments.js');
const express = require('express');
const router = express.Router();

router.get('/custom', (req, res, next) => {
  res.send(JSON.stringify(payjs.getCustomInitialization(req.query)));
})

router.get('/vault', (req, res, next) => {
  res.render('vault', { initialization: JSON.stringify(payjs.getCustomInitialization({ requestType: "vault" })) })
})

router.post('/hashes', (req, res, next) => {
  res.send(payjs.getResponseHashes(JSON.parse(req.body.resp)));
})

router.get('/:page', (req, res, next) => {
  res.render(req.params.page, { initialization: JSON.stringify(payjs.getInitialization()) })
})

router.get('/', (req, res, next) => {
  res.render('index');
});

module.exports = router;
