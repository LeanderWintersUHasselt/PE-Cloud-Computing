<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class PriceAlertController extends Controller
{
    public function sendAlert(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'coin' => 'required|string',
            'price' => 'required|numeric',
            'alertId' => 'required|string'
        ]);
    
        // Extract fields from the request
        $coin = $request->input('coin');
        $price = $request->input('price');
        $alertId = $request->input('alertId');
    
        // Initialize the MQTT client
        $mqtt = new MqttClient('mosquitto-broker', 1883, 'laravel-publisher');
        $connectionSettings = new ConnectionSettings(); // Adjust settings if needed
    
        // Connect and publish the alert data
        $mqtt->connect($connectionSettings, true);
        $mqtt->publish('user-price-alerts', json_encode(['coin' => $coin, 'price' => $price, 'alertId' => $alertId]));
        $mqtt->disconnect();
    
        // Return a successful response
        session()->push('user_alerts', $alertId);
        return response()->json(['message' => 'Alert sent successfully']);
    }

    public function checkAlerts()
    {
        $alertIds = session('user_alerts', []);
        $alertIdsString = '"' . implode('", "', $alertIds) . '"';
        
        // GraphQL query to get all matching alerts
        $response = Http::post('http://login-reg-graphql-container:4000/graphql', [
            'query' => "
                query {
                    getAlertsByIds(ids: [{$alertIdsString}]) {
                        alert_id
                        message
                    }
                }
            "
        ]);
        $alerts = $response->json()['data']['getAlertsByIds'] ?? [];
        Log::info('Checking alerts');
        Log::info($alerts);
        // GraphQL mutation to delete the matching alerts from the database
        if (count($alerts) > 0) {
            Http::post('http://login-reg-graphql-container:4000/graphql', [
                'query' => "
                    mutation {
                        deleteAlertsByIds(ids: [{$alertIdsString}]) {
                            success
                            message
                        }
                    }
                "
            ]);
        }

        return response()->json($alerts);
    }

}
