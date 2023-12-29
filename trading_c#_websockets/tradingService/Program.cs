using System;
using System.Net;
using System.Net.WebSockets;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using Newtonsoft.Json;
using System.Net.Http;
using Newtonsoft.Json.Serialization;

namespace TradingService
{
    class Program
    {
        static readonly HttpClient httpClient = new HttpClient();
        static readonly string GraphQLServerEndpoint = "http://login-reg-graphql-container:4000/graphql";

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
            TradeMessage? tradeMessage;
            try
            {
                if (!string.IsNullOrEmpty(message))
                {
                    tradeMessage = JsonConvert.DeserializeObject<TradeMessage>(message);
                    if (tradeMessage == null)
                    {
                        Console.WriteLine("Received message is not a valid TradeMessage JSON.");
                        await SendResponseAsync("Invalid message format", webSocket);
                        return;
                    }
                }
                else
                {
                    Console.WriteLine("Received message is null or empty.");
                    await SendResponseAsync("Invalid message format", webSocket);
                    return;
                }
            }
            catch (JsonException ex)
            {
                Console.WriteLine("JSON parsing error: " + ex.Message);
                await SendResponseAsync("Invalid message format", webSocket);
                return;
            }

            await ProcessTradeDecisionAsync(tradeMessage, webSocket);
        }

        private static async Task ProcessTradeDecisionAsync(TradeMessage tradeMessage, WebSocket webSocket)
        {
            try
            {
                bool isSell = false;
                if (tradeMessage.Action.Equals("SELL")) isSell = true;
                var price = await GetCurrentPriceAsync(tradeMessage.Coin);
                if (!price.HasValue)
                {
                    await SendResponseAsync("Error fetching price", webSocket);
                    return;
                }

                var balanceData = await GetUserBalanceAsync(tradeMessage.UserId);
                if (balanceData == null)
                {
                    await SendResponseAsync("Error fetching balance", webSocket);
                    return;
                }

                double tradeCost = double.Parse(tradeMessage.Amount) * price.Value;
                if (isSell == false && tradeCost > balanceData.EurBalance)
                {
                    await SendResponseAsync("Insufficient balance for the trade", webSocket);
                    return;
                }
                else if (isSell == true) {
                    double coinBalance = tradeMessage.Coin.ToUpper() == "BTC" ? balanceData.BtcBalance : balanceData.EthBalance;
                    if (double.Parse(tradeMessage.Amount) > coinBalance)
                    {
                        await SendResponseAsync("Insufficient balance for the trade", webSocket);
                        return;
                    }
                }

                Console.WriteLine("Balance check complete, valid trade request. ");
                await ProcessBuySellRequest(tradeMessage, webSocket, isSell);
                await UpdateEuroBalance(tradeMessage.UserId, tradeCost, isSell);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Error in trade decision process: " + ex.Message);
                await SendResponseAsync("Error processing trade", webSocket);
            }
        }

        private static async Task ProcessBuySellRequest(TradeMessage tradeMessage, WebSocket webSocket, bool isSell)
        {
            double amount = double.Parse(tradeMessage.Amount);
            if (isSell) amount = -amount; // Invert for sell
            var mutation = CreateGraphQLMutation(tradeMessage, amount);
            var response = await ExecuteGraphQLRequest(mutation);
            await SendResponseAsync(response, webSocket);
        }

        private static string CreateGraphQLMutation(TradeMessage tradeMessage, double amount)
        {
            var mutation = new
            {
                query = $@"mutation {{ 
                            updateCoinBalance(userId: {tradeMessage.UserId}, amount: {amount}, coin: {tradeMessage.Coin.ToUpperInvariant()}) {{ 
                                success message 
                            }} 
                        }}"
            };

            return JsonConvert.SerializeObject(mutation);
        }

        private static async Task<string> ExecuteGraphQLRequest(string query)
        {
            Console.WriteLine("Executing GraphQL request: " + query);
            var content = new StringContent(query, Encoding.UTF8, "application/json");
            
            try
            {
                var response = await httpClient.PostAsync(GraphQLServerEndpoint, content);
                var responseString = await response.Content.ReadAsStringAsync();

                if (!response.IsSuccessStatusCode)
                {
                    Console.WriteLine($"Error response from GraphQL server: {response.StatusCode}");
                    return $"Error: {responseString}";
                }

                Console.WriteLine("GraphQL response: " + responseString);
                return responseString;
            }
            catch (HttpRequestException e)
            {
                Console.WriteLine("Request exception: " + e.Message);
                return $"Error: {e.Message}";
            }
        }

        private static async Task SendResponseAsync(string response, WebSocket webSocket)
        {
            Console.WriteLine($"Sending response: {response}");
            var responseBuffer = Encoding.UTF8.GetBytes(response);
            await webSocket.SendAsync(new ArraySegment<byte>(responseBuffer, 0, responseBuffer.Length), WebSocketMessageType.Text, true, CancellationToken.None);
        }

        private static async Task<double?> GetCurrentPriceAsync(string coin)
        {
            var urlMap = new Dictionary<string, string>
            {
                ["BTC"] = "https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT",
                ["ETH"] = "https://api.binance.com/api/v3/ticker/price?symbol=ETHUSDT"
            };

            if (!urlMap.TryGetValue(coin, out var url))
            {
                Console.Error.WriteLine($"Unsupported coin: {coin}");
                return null;
            }

            try
            {
                var response = await httpClient.GetAsync(url);
                response.EnsureSuccessStatusCode();
                var content = await response.Content.ReadAsStringAsync();
                var result = JsonConvert.DeserializeObject<dynamic>(content);
                Console.WriteLine($"GetCurrentPrice response: {coin}: {result?.price}");
                return result?.price != null ? (double)result.price : null;
            }
            catch (Exception ex)
            {
                Console.Error.WriteLine($"Error fetching price for {coin}: {ex.Message}");
                return null;
            }
        }

        private static async Task<BalanceData?> GetUserBalanceAsync(int userId)
        {
            try
            {
                string url = $"http://wallet-java-rest:4003/api/wallet/balance?userId={userId}";
                HttpResponseMessage response = await httpClient.GetAsync(url);

                string responseContent = await response.Content.ReadAsStringAsync();
                Console.WriteLine("GetBalance response: " + responseContent);

                if (response.IsSuccessStatusCode)
                {
                    BalanceData? balanceData = JsonConvert.DeserializeObject<BalanceData>(responseContent);
                    if (balanceData != null)
                    {
                        return balanceData;
                    }
                    else
                    {
                        Console.WriteLine("Error deserializing balance data.");
                        return null;
                    }
                }
                else
                {
                    Console.WriteLine($"Error fetching balance. Status code: {response.StatusCode}");
                    return null;
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Exception in GetUserBalanceAsync: {ex.Message}");
                return null;
            }
        }

        private static async Task<bool?> UpdateEuroBalance(int userId, double amount, bool isSell) 
        {
            try
            {
                var endpoint = isSell ? "deposit" : "withdraw";
                var walletRequest = new WalletRequest
                {
                    UserId = userId,
                    Amount = Math.Abs(amount) // Amount should always be positive because it's inverted in the java wallet service
                };

                var jsonContent = JsonConvert.SerializeObject(walletRequest, Formatting.None, new JsonSerializerSettings { ContractResolver = new CamelCasePropertyNamesContractResolver() });
                Console.WriteLine($"Request JSON Content: {jsonContent}"); // Log the serialized JSON content
    
                var content = new StringContent(jsonContent, Encoding.UTF8, "application/json");
                var contentString = await content.ReadAsStringAsync();
                Console.WriteLine("HTTP Content: " + contentString);

                var url = $"http://wallet-java-rest:4003/api/wallet/{endpoint}";

                Console.WriteLine($"Updating euro balance for user: {userId} with amount: {amount} using the endpoint: {endpoint}");

                HttpResponseMessage response = await httpClient.PostAsync(url, content);

                string responseContent = await response.Content.ReadAsStringAsync();

                Console.WriteLine("Raw Response Content: " + responseContent);

                if (response.IsSuccessStatusCode)
                {
                    BalanceUpdateResponse? balanceUpdateResponse = JsonConvert.DeserializeObject<BalanceUpdateResponse>(responseContent);
                    if (balanceUpdateResponse != null)
                    {
                        Console.WriteLine($"Success: {balanceUpdateResponse.Success}, Message: {balanceUpdateResponse.Message}");
                        return true;
                    }
                    else
                    {
                        Console.WriteLine("Error deserializing balance update response.");
                        return null;
                    }
                }
                else
                {
                    Console.WriteLine($"Error updating balance. Content: {response.Content}");
                    return null;
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Exception in UpdateEuroBalance: {ex.Message}");
                return null;
            }
        }

    }

    internal class BalanceUpdateResponse
    {
        public bool Success { get; set; }
        public string Message { get; set; } = string.Empty;
    }

    public class WalletRequest
    {
        [JsonProperty("userId")]
        public int UserId { get; set; }

        [JsonProperty("amount")]
        public double Amount { get; set; }
    }

    public class TradeMessage
    {
        public string Action { get; set; } = string.Empty;
        public string Amount { get; set; } = string.Empty;
        public string Coin { get; set; } = string.Empty;
        public int UserId { get; set; }
    }

    public class BalanceData
    {
        [JsonProperty("eur")]
        public double EurBalance { get; set; }

        [JsonProperty("btc")]
        public double BtcBalance { get; set; }

        [JsonProperty("eth")]
        public double EthBalance { get; set; }
    }

}
