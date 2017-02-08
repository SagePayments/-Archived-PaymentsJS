const payjs = require('../services/sagePayments.js');
const express = require('express');
const router = express.Router();

router.get('/:page', (req, res, next) => {
  res.render(req.params.page, { initialization: JSON.stringify(payjs.getInitialization()) })
})

router.get('/', (req, res, next) => {
  res.render('index');
});

module.exports = router;
