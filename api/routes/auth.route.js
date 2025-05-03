const express = require('express');
const auth = require('../services/auth');
const router = new express.Router();

router.post('/basic', async (req, res, next) => {
    console.log(res);
    let options = {
        email: atob(req.headers.authorization.split(' ')[1]).split(":")[0],
        password: atob(req.headers.authorization.split(' ')[1]).split(":")[1]
    };

    try {
        const result = await auth.postBasic(options);
        console.log(JSON.stringify(result));
        res.status(result.data.status || 200).json(result.data);
    } catch (err) {
        console.log(err.message);
        return res.status(500).send({
            error: 'Something went wrong'
        });
    }
});

router.get('/basic', async(req, res, next) => {
    console.log("nah get");
});

router.post('/refresh', async (req, res, next) => {

});

module.exports = router;