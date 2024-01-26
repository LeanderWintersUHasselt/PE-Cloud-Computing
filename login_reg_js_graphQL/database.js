const mysql = require('mysql2/promise');

const db = mysql.createPool({
  host: 'crypto_exchange-mysql-1',
  user: 'sail',
  password: 'password',
  database: 'laravel'
});

module.exports = db;
