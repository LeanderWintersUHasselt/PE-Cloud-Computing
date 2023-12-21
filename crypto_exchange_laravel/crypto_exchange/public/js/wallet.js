window.onload = fetchAndDisplayBalances;

function sendWalletRequest(actionType) {
    const amount = document.getElementById('amount').value;
    const responseMessageDiv = document.getElementById('responseMessage');

    // Validation
    if (!amount) {
        responseMessageDiv.innerText = 'Please enter an amount.';
        return;
    }

    // Prepare data to send
    const requestData = {
        action: actionType,
        amount: amount
    };

    // Send data to Laravel backend
    fetch('/api/wallet/action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
        responseMessageDiv.innerText = data.message; // Display message from the server
    })
    .catch((error) => {
        console.error('Error:', error);
        responseMessageDiv.innerText = 'Error processing request.';
    });
}

function fetchAndDisplayBalances() {
    fetch('/api/wallet/balance')
        .then(response => response.json())
        .then(data => {
            if (data && 'eur' in data && 'btc' in data && 'eth' in data) {
                document.getElementById('eurBalance').innerText = `EUR: ${data.eur}`;
                document.getElementById('btcBalance').innerText = `BTC: ${data.btc}`;
                document.getElementById('ethBalance').innerText = `ETH: ${data.eth}`;
            } else {
                console.error('Invalid data received', data);
                // Handle the error case
            }
        })
        .catch(error => {
            console.error('Error fetching balances:', error);
            // Handle the error case
        });
}