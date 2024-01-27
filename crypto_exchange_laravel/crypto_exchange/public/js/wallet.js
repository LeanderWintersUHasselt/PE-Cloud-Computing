window.onload = fetchAndDisplayBalances;

function sendWalletRequest(actionType) {
    const amount = document.getElementById('amount').value;
    const responseMessageDiv = document.getElementById('responseMessage');

    if (!amount) {
        responseMessageDiv.innerText = 'Please enter an amount.';
        return;
    }
    const requestData = {
        action: actionType,
        amount: amount
    };

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
        fetchAndDisplayBalances();
        responseMessageDiv.innerText = data.message;
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
                const eurFormatter = new Intl.NumberFormat('nl-NL', {
                    style: 'currency',
                    currency: 'EUR'
                });

                document.getElementById('eurBalance').innerText = eurFormatter.format(data.eur);
                document.getElementById('btcBalance').innerText = Number(data.btc).toFixed(2);
                document.getElementById('ethBalance').innerText = Number(data.eth).toFixed(2);
            } else {
                console.error('Invalid data received', data);
            }
        })
        .catch(error => {
            console.error('Error fetching balances:', error);
        });
}