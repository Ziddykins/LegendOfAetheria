const mysql = require('mysql');
const bcrypt = require('bcrypt');
const { randomBytes } = require('node:crypto');
const jwt = require('jsonwebtoken');
const e = require('dotenv');
e.config();

module.exports = {
    postBasic: async (options) => {
        let con = null;
        try {
            // Generate secret first
            const secret = randomBytes(64).toString('hex');
            
            // Create connection
            con = mysql.createConnection({
                host: process.env.SQLHOST,
                user: process.env.SQLUSER,
                password: process.env.SQLPASS,
                database: process.env.SQLDB
            });

            // Promisify connection
            await new Promise((resolve, reject) => {
                con.connect(err => {
                    if (err) reject(err);
                    resolve();
                });
            });

            // Get user data
            const results = await new Promise((resolve, reject) => {
                con.query("SELECT * FROM tbl_accounts WHERE `email` = ?", 
                    [options.email], 
                    (err, results) => {
                        if (err) reject(err);
                        resolve(results);
                    }
                );
            });

            // Check user exists
            if (!results || results.length === 0) {
                throw new Error('User not found');
            }

            // Fix bcrypt hash issue with 2y->2b
            const pw = results[0].password.replace('2y', '2b');
            
            // Compare password
            const passwordMatch = await bcrypt.compare(options.password, pw);
            if (!passwordMatch) {
                throw new Error('Invalid password');
            }

            await new Promise((resolve, reject) => {
                const sql = `UPDATE ${process.env.SQL_ACCT_TBL} SET jwt_secret = ? WHERE email = ?`;
                con.query(sql, [secret, options.email], err => {
                    if (err) reject(err);
                    resolve();
                });
            });

            // Generate token
            const token = jwt.sign(
                { options },
                secret,
                { expiresIn: '1h' }
            );

            return {
                data: {
                    token,
                    account: results[0],
                    status: 200
                }
            };

        } catch (err) {
            console.error('Auth error:', err.message);
            return {
                data: {
                    error: err.message,
                    status: err.message === 'User not found' || err.message === 'Invalid password' ? 401 : 500
                }
            };
        } finally {
            if (con) {
                con.end();
            }            console.log("Woooooooo pass");
        }
    }
};
console.log("Woooooooo pass");