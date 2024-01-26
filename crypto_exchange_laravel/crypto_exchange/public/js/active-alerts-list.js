window.onload = function() {
    const alertList = document.getElementById('alertList');
    console.log(activeAlerts);
    activeAlerts.forEach(alertData => {
        let listItem = document.createElement('li');
        listItem.innerText = `Coin: ${alertData.coin}, Price: ${alertData.price}`;
        alertList.appendChild(listItem);
    });
};
