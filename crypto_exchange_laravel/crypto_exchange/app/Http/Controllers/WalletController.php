<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WalletController extends Controller 
{
    public function handleAction(Request $request) 
    {
        $actionType = $request->input('action');
        $amount = $request->input('amount');
        //$userId = session('user')['id'];
        $userId = 1;

        // URL of the Java microservice endpoint
        $url = 'http://wallet-java-rest:4003/api/wallet/' . strtolower($actionType);

        // Send a request to the Java microservice
        $response = Http::post($url, [
            'userId' => $userId,
            'amount' => $amount
        ]);

        // Handle the response
        if ($response->successful()) {
            $responseText = $response->body();
            \Log::info('Response Data: ', ['data' => $responseText]);
            return response()->json(['message' => $responseText]);
        } else {
            // Log error details for debugging
            \Log::error('Error response from Java service: ', ['response' => $response->body()]);
            return response()->json(['message' => 'Service unavailable'], 503);
        }
    }

    public function getBalances() {
        //$userId = session('user')['id'];
        $userId = 1;

        // URL of the Java microservice endpoint
        $url = 'http://wallet-java-rest:4003/api/wallet/balance';

        // Send a request to the Java microservice
        $response = Http::get($url, [
            'userId' => $userId
        ]);

        // Handle the response
        if ($response->successful()) {
            $responseData = json_decode($response->body(), true);
            if (is_array($responseData)) {
                \Log::info('Response Data: ', ['data' => $responseData]);
                return response()->json($responseData);
            } else {
                \Log::error('Invalid response format from Java service', ['response' => $response->body()]);
                return response()->json(['message' => 'Invalid response format'], 500);
            }
        } else {
            // Log error details for debugging
            \Log::error('Error response from Java service: ', ['response' => $response->body()]);
            return response()->json(['message' => 'Service unavailable'], 503);
        }
    }
}
