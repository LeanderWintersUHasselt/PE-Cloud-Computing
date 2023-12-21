let ws;

document.addEventListener("DOMContentLoaded", function() {
    if (window.location.pathname === '/trade') {
        openWebSocket();
    }
});

function openWebSocket() {
    ws = new WebSocket("ws://localhost:4001/");

    ws.onopen = function() {
        console.log("Connected to WebSocket");
    };

    ws.onmessage = function(event) {
        console.log("Received message: " + event.data);
        document.getElementById('responseMessage').innerText = event.data;
    };

    ws.onerror = function(event) {
        console.error("WebSocket error observed:", event);
        document.getElementById('responseMessage').innerText = "WebSocket error occurred.";
    };

    ws.onclose = function(event) {
        console.log("WebSocket is closed now.");
    };

    window.addEventListener("beforeunload", function() {
        ws.close(); // Close WebSocket connection when leaving the page
    });
}

function sendTradeRequest(action) {
    const amount = document.getElementById('amount').value;
    const coin = document.getElementById('coin').value;
    if (amount && coin) {
        const message = JSON.stringify({ action: action, amount: amount, coin: coin });
        console.log("Sending message:", message); // Add this line
        ws.send(message);
    } else {
        console.log("No amount specified."); // Add this line
    }
}