const db = require('./database');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');


const resolvers = {
    Query: {
        getAlertsByIds: async (_, { ids }) => {
            try {
                const [alerts] = await db.query('SELECT * FROM alerts WHERE alert_id IN (?)', [ids]);
                return alerts;
            }   catch (error) {
                console.error('Error fetching alerts:', error);
                throw new Error('Error fetching alerts');
            }
        },
        getUserBalances: async (_, { userId }) => {
            try {
                // Query to fetch balance data for the given user ID
                const [userBalances] = await db.query('SELECT eur_balance, btc_balance, eth_balance FROM customers WHERE id = ?', [userId]);
                
                // Check if user balance data was found
                if (userBalances.length === 0) {
                    return null; // No user balance data found
                }
        
                // Return the balance data
                return {
                    eur_balance: userBalances[0].eur_balance,
                    btc_balance: userBalances[0].btc_balance,
                    eth_balance: userBalances[0].eth_balance
                };
            } catch (error) {
                console.error('Error fetching user balances:', error);
                throw new Error('Failed to fetch user balances');
            }
        },
              
    },
    Mutation: {
        login: async (_, { email, password }) => {
            try {
                const [users] = await db.query('SELECT * FROM customers WHERE email = ?', [email]);
                if (users.length === 0) {
                    return {
                        success: false,
                        message: 'User not found',
                        token: null,
                        userId: null
                    };
                }

                const user = users[0];
                const valid = await bcrypt.compare(password, user.password);
                if (!valid) {
                    return {
                        success: false,
                        message: 'Invalid credentials',
                        token: null,
                        userId: null
                    };
                }

                // Generate a token
                const token = generateToken(user);
                console.log(`User logged in. User id: ${user.id}`)
                return {
                    success: true,
                    message: 'Login successful',
                    token: token,
                    userId: user.id
                };
            } catch (error) {
                // Error handling
                return {
                    success: false,
                    message: 'An error occurred during login',
                    token: null,
                    userId: null
                };
            }
        },
        register: async (_, { email, password, firstName, lastName }) => {
            try {
                const [existingUsers] = await db.query('SELECT id FROM customers WHERE email = ?', [email]);
                if (existingUsers.length > 0) {
                    return {
                        success: false,
                        message: 'This email is already in use.'
                    };
                }

                const hashedPassword = await bcrypt.hash(password, 10);

                const result = await db.query('INSERT INTO customers (email, password, first_name, last_name) VALUES (?, ?, ?, ?)', [email, hashedPassword, firstName, lastName]);
                if (result.affectedRows === 0) {
                    return { success: false, message: 'Registration failed to insert in DB.' };
                }

                return {
                    success: true,
                    message: 'Registration successful.'
                };

            } catch (error) {
                console.error('Registration error:', error);
                return {
                    success: false,
                    message: error.message || 'An error occurred during registration.'
                };
            }
        },
        createAlert: async (_, { alertId, message}) => {
            try {
                const result = await db.query('INSERT INTO alerts (alert_id, message) VALUES (?, ?)', [alertId, message]);
                if (result.affectedRows === 0) {
                    return { success: false, message: 'Failed to create alert.' };
                }

                return { success: true, message: 'Alert created successfully.' };
            } catch (error) {
                console.error('Error creating alert:', error);
                return { success: false, message: 'An error occurred while creating the alert.' };
            }
        },
        deleteAlertsByIds: async (_, { ids }) => {
            try {
              await db.query('DELETE FROM alerts WHERE alert_id IN (?)', [ids]);
              return {
                success: true,
                message: 'Alerts deleted successfully'
              };
            } catch (error) {
              console.error('Error deleting alerts:', error);
              return {
                success: false,
                message: 'Error deleting alerts'
              };
            }
        },
        updateEuroBalance: async (_, { userId, amount }) => {
            try {
                // Fetch the current euro balance for the user
                const [users] = await db.query('SELECT eur_balance FROM customers WHERE id = ?', [userId]);
                if (users.length === 0) {
                    return {
                        success: false,
                        message: 'User not found',
                    };
                }
        
                let currentBalance = users[0].eur_balance;
        
                // Calculate the new balance
                let newBalance = currentBalance + amount;
        
                // Check for insufficient funds in case of withdrawal
                if (amount < 0) { // Check for withdrawals specifically
                    if (currentBalance < Math.abs(amount)) {
                        return {
                            success: false,
                            message: 'Insufficient funds for withdrawal',
                        };
                    } 
                }
                // Update the balance in the database
                await db.query('UPDATE customers SET eur_balance = ? WHERE id = ?', [newBalance, userId]);
        
                return {
                    success: true,
                    message: 'Balance updated successfully',
                };
            } catch (error) {
                console.error('Error updating balance:', error);
                return {
                    success: false,
                    message: 'Error updating balance',
                };
            }
        },
        
        
    }
};

const SECRET_KEY = 'test-token';

function generateToken(user) {
    // Payload data you want to include in the token (e.g., user ID)
    const payload = {
        id: user.id,
        email: user.email
        // You can add more user details here if needed
    };

    // Token expiration time (e.g., 1 hour)
    const expiresIn = '10m';

    // Generating the token
    return jwt.sign(payload, SECRET_KEY, { expiresIn: expiresIn });
}
// You may need to implement or import a function like generateToken here

module.exports = resolvers;
