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
        ws.close();
    });
}

function sendTradeRequest(action) {
    const amount = document.getElementById('amount').value;
    const coin = document.getElementById('coin').value;
    const userId = window.userId;
    console.log("Amount:", amount, "Coin:", coin, "User ID:", userId);
    if (amount && coin) {
        const message = JSON.stringify({ action: action, amount: amount, coin: coin, userId: userId });
        console.log("Sending message:", message);
        ws.send(message);
    } else {
        console.log("No amount specified.");
    }
}