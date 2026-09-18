import mysql from 'mysql2';
import bcrypt from 'bcrypt';
import { randomBytes } from 'node:crypto';
import jwt from 'jsonwebtoken';
import dotenv from 'dotenv';

dotenv.config({ path: '.env' });

export default {
    postAuthorization: async (options) => {
        let con = null;

        try {
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
                    if (err) {
                        reject(err);
                    }
                    resolve();
                });
            });
        } catch {
            throw new Error('Cannot connect to database');
        }

        try {
            // Get user data
            const results = await new Promise((resolve, reject) => {
                con.query("SELECT * FROM tbl_accounts WHERE `email` = ?",
                    [options.email],
                    (err, results) => {
                        if (err) {
                            reject(err);
                        }
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
                return { 
                    data: { 
                        status: 403, 
                        message: "Incorrect password" 
                    }
                }
            }

            if (options.token) {
                const secret = results[0].jwt_secret;
                jwt.verify(options.token, secret);
            }

            const secret = randomBytes(64).toString('hex');

            await new Promise((resolve, reject) => {
                const sql = `UPDATE ${process.env.SQL_ACCT_TBL} SET jwt_secret = ? WHERE email = ?`;
                con.query(sql, [secret, options.email], err => {
                    if (err) {
                        reject(err);
                    }

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
                    token: token,
                    account: results[0],
                    status: 200
                }
            };

        } catch (err) {
            console.error('Auth error caught:', err);
            console.error('Error message:', err.message);
            console.error('Error:', err);
            return {
                data: {
                    error: err.message || 'Authentication failed',
                    status: err.message === 'User not found' || err.message === 'Invalid password' ? 401 : 500
                }
            };
        } finally {
            if (con) {
                con.end();
            }
        }
    }
};
