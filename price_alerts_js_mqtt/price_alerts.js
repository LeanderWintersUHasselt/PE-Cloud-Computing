const mqtt = require('mqtt');
const axios = require('axios');

// MQTT broker connection
const mqttClient = mqtt.connect('mqtt://mosquitto-broker');
const btcPriceTopic = 'btc-price-alert';
const userAlertsTopic = 'user-price-alerts';
const sentAlerts = new Set(); // Set to track sent alert IDs

// Structure to store user-set price points
let userPriceAlerts = {}; // { 'alertId': { coin: 'BTC', pricePoint: value }, ... }
const priceTolerance = 50;

// Function to get current BTC price
async function checkPriceAndAlert() {
    for (const [alertId, alertDetails] of Object.entries(userPriceAlerts)) {
        const currentPrice = await getCurrentPrice(alertDetails.coin);     

        if (currentPrice && priceConditionMet(currentPrice, alertDetails.price) && !sentAlerts.has(alertId)) {
            console.log(`Alert ${alertId} triggered: ${alertDetails.coin} price is at ${currentPrice}`);
            const alertMessage = JSON.stringify({
                alertId: alertId,
                message: `${alertDetails.coin} price is at ${currentPrice}`
            });
            
            mqttClient.publish(btcPriceTopic, alertMessage);
            sentAlerts.add(alertId); // Mark this alert as sent
        }
    }
}

function priceConditionMet(currentPrice, pricePoint) {
    const conditionMet = Math.abs(parseFloat(currentPrice) - parseFloat(pricePoint)) <= priceTolerance;
    console.log(`Price condition met: ${conditionMet}, Current: ${currentPrice}, Target: ${pricePoint}`);
    return conditionMet;
}

async function getCurrentPrice(coin) {
    const urlMap = {
        'BTC': 'https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT',
        'ETH': 'https://api.binance.com/api/v3/ticker/price?symbol=ETHUSDT'
    };
    try {
        if (urlMap[coin]) {
            const response = await axios.get(urlMap[coin]);
            return response.data.price;
        }
        console.error(`Unsupported coin: ${coin}`);
        return null;
    } catch (error) {
        console.error(`Error fetching price for ${coin}:`, error);
        return null;
    }
}

function priceConditionMet(currentPrice, price) {
    // Check if the current price is equal to or has crossed the alert price
    return Math.abs(parseFloat(currentPrice) - parseFloat(price)) <= priceTolerance;
}

// MQTT Client setup
mqttClient.on('connect', () => {
    console.log('Connected to MQTT broker');
    mqttClient.subscribe(userAlertsTopic);
    setInterval(checkPriceAndAlert, 3000); // Check BTC price every 3 seconds
});

mqttClient.on('message', (topic, message) => {
    if (topic === userAlertsTopic) {
        try {
            const alertRequest = JSON.parse(message.toString());
            // Store coin type and price point
            userPriceAlerts[alertRequest.alertId] = { 
                coin: alertRequest.coin, 
                price: alertRequest.price 
            };
            console.log(`Received new price alert request: ${message.toString()}`);
        } catch (error) {
            console.error('Error processing alert request:', error);
        }
    }
});

mqttClient.on('error', (error) => {
    console.error('MQTT Client Error:', error);
});
