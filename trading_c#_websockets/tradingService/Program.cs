using System;
using System.Net;
using System.Net.WebSockets;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using Newtonsoft.Json;


namespace TradingService
{
    class Program
    {
        static async Task Main(string[] args)
        {
            HttpListener httpListener = new HttpListener();
            httpListener.Prefixes.Add("http://*:4001/");
            httpListener.Start();
            Console.WriteLine("WebSocket server started at ws://localhost:4001/");

            while (true)
            {
                var context = await httpListener.GetContextAsync();

                if (context.Request.IsWebSocketRequest)
                {
                    var webSocketContext = await context.AcceptWebSocketAsync(null);
                    var webSocket = webSocketContext.WebSocket;

                    Console.WriteLine("WebSocket connection established");
                    await HandleClient(webSocket);
                }
                else
                {
                    context.Response.StatusCode = 400;
                    context.Response.Close();
                    Console.WriteLine("Non-WebSocket request received and rejected");
                }
            }
        }

        private static async Task HandleClient(WebSocket webSocket)
        {
            var buffer = new byte[1024 * 4];

            while (webSocket.State == WebSocketState.Open)
            {
                var result = await webSocket.ReceiveAsync(new ArraySegment<byte>(buffer), CancellationToken.None);

                if (result.MessageType == WebSocketMessageType.Close)
                {
                    Console.WriteLine("WebSocket connection closing");
                    await webSocket.CloseAsync(WebSocketCloseStatus.NormalClosure, string.Empty, CancellationToken.None);
                }
                else
                {
                    var message = Encoding.UTF8.GetString(buffer, 0, result.Count);
                    Console.WriteLine($"Received message: {message}");
                    await ProcessMessageAsync(message, webSocket);
                }
            }
            Console.WriteLine("WebSocket connection closed");
        }

        private static async Task ProcessMessageAsync(string message, WebSocket webSocket)
        {
            try
            {
                var tradeMessage = JsonConvert.DeserializeObject<TradeMessage>(message);

                if (tradeMessage == null)
                {
                    Console.WriteLine("Received message is not a valid TradeMessage JSON.");
                    return; 
                }

                if (tradeMessage.Action.ToUpper() == "BUY")
                {
                    Console.WriteLine($"Processing buy request for amount {tradeMessage.Amount}");
                    //TODO implement buy logic
                    // tradeMessage.Amount
                    // tradeMessage.Coin
                    await SendResponseAsync("Buy order processed", webSocket);
                }
                else if (tradeMessage.Action.ToUpper() == "SELL")
                {
                    Console.WriteLine($"Processing sell request for amount {tradeMessage.Amount}");
                    //TODO implement sell logic
                    // tradeMessage.Amount
                    // tradeMessage.Coin
                    await SendResponseAsync("Sell order processed", webSocket);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine("Error processing message: " + ex.Message);
            }
        }


        private static async Task SendResponseAsync(string response, WebSocket webSocket)
        {
            Console.WriteLine($"Sending response: {response}");
            var responseBuffer = Encoding.UTF8.GetBytes(response);
            await webSocket.SendAsync(new ArraySegment<byte>(responseBuffer, 0, responseBuffer.Length), WebSocketMessageType.Text, true, CancellationToken.None);
        }

    }
    public class TradeMessage
    {
        public string Action { get; set; } = string.Empty;
        public string Amount { get; set; } = string.Empty;
    }

}
