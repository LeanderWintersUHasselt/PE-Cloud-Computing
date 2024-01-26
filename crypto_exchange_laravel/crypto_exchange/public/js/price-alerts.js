function sendPriceAlert() {
    const price = document.getElementById('price').value;
    const coin = document.getElementById('coin').value;
    const responseMessageDiv = document.getElementById('responseMessage');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const alertId = generateUniqueId();

    if (!price || !coin) {
        console.error('Error:', error);
        responseMessageDiv.innerText = 'Please enter both amount and coin type.';
        return;
    }

    const alertData = {
        coin: coin,
        price: price,
        alertId: alertId
    };

    fetch('/api/send-alert', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify(alertData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
        responseMessageDiv.innerText = 'Alert set successfully!';
    })
    .catch((error) => {
        console.error('Error:', error);
        responseMessageDiv.innerText = 'Error setting alert.';
    });
}

function generateUniqueId() {
    const timestamp = Date.now();
    const randomNum = Math.floor(Math.random() * 1000);
    return `${timestamp}:${randomNum}`;
}

function checkAlerts() {
    fetch('/api/check-alerts')
        .then(response => response.json())
        .then(alerts => {
            if (alerts.length > 0) {
                alerts.forEach(alertMessage => {
                    console.log('New alert:', alertMessage);
                    alert('ALERT TRIGGERED: ' + alertMessage.message);
                });
            }
        })
        .catch(error => console.error('Error:', error));
}

setInterval(checkAlerts, 3000);