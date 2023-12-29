package com.cloud.walletservice.controller;

import java.io.IOException;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;
import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.springframework.stereotype.Service;

@Service
public class WalletService {
    private static final String GRAPHQL_ENDPOINT = "http://login-reg-graphql-container:4000/graphql";
    private static final MediaType JSON = MediaType.get("application/json; charset=utf-8");
    private OkHttpClient client = new OkHttpClient();
    private ObjectMapper objectMapper = new ObjectMapper();

    public BalanceData getBalancesForUser(int userId) throws IOException {
        String query = String.format(
            "{\"query\":\"{ getUserBalances(userId: %d) { eur_balance btc_balance eth_balance } }\"}", 
            userId);
        String jsonResponse = executeGraphQLRequest(query);
        return parseBalanceData(jsonResponse);
    }

    public String deposit(int userId, double amount) throws IOException {
        String mutation = String.format(
            "{\"query\":\"mutation { updateEuroBalance(userId: %d, amount: %f) { success message } }\"}", 
            userId, amount);
        
        return executeGraphQLRequest(mutation);
    }
    
    public String withdraw(int userId, double amount) throws IOException {
        String mutation = String.format(
            "{\"query\":\"mutation { updateEuroBalance(userId: %d, amount: %f) { success message } }\"}", 
            userId, -amount); // Negate the amount for withdrawal
        return executeGraphQLRequest(mutation);
    }    


    private String executeGraphQLRequest(String query) throws IOException {
        RequestBody body = RequestBody.create(query, JSON);
        Request request = new Request.Builder()
                .url(GRAPHQL_ENDPOINT)
                .post(body)
                .build();
        try (Response response = client.newCall(request).execute()) {
            String responseBody = response.body().string();
            System.out.println(responseBody);
            return responseBody;
        }
    }
    

    private BalanceData parseBalanceData(String jsonResponse) throws IOException {
        JsonNode rootNode = objectMapper.readTree(jsonResponse);
        JsonNode dataNode = rootNode.path("data").path("getUserBalances");
        double eur = dataNode.path("eur_balance").asDouble();
        double btc = dataNode.path("btc_balance").asDouble();
        double eth = dataNode.path("eth_balance").asDouble();

        BalanceData balanceData = new BalanceData();
        balanceData.setEUR(eur);
        balanceData.setBTC(btc);
        balanceData.setETH(eth);
        return balanceData;
    }
}
