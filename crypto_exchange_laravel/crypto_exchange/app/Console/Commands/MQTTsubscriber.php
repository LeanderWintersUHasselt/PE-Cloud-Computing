<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Http;

class MQTTSubscriber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mqtt-subscriber';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribes to MQTT topics and handles incoming messages.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mqtt = new MqttClient('mosquitto-broker', 1883, 'laravel-subscriber');
        $connectionSettings = new ConnectionSettings();

        $this->info("Attempting to connect to MQTT broker...");
        $mqtt->connect($connectionSettings, true);
        $this->info("Connected to MQTT broker.");

        $mqtt->subscribe('btc-price-alert', function ($topic, $message) {
            $this->handleIncomingMessage($topic, $message);
        }, 0);

        $mqtt->loop(true);
    }

    /**
     * Handle incoming MQTT messages.
     *
     * @param string $topic
     * @param string $message
     */
    protected function handleIncomingMessage(string $topic, string $message)
    {
        $this->info("Received raw message on topic [$topic]: $message");
        $alertData = json_decode($message, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid JSON received: " . json_last_error_msg());
            return;
        }
    
        $this->info("Decoded JSON: " . print_r($alertData, true));
    
        $alertId = $alertData['alertId'] ?? '';
        $message = $alertData['message'] ?? '';
    
        if ($alertId && $message) {
            $this->sendAlertToGraphql($alertId, $message);
        }
    }
    

    /**
     * Send alert data to the GraphQL server.
     *
     * @param string $alertId
     * @param string $message
     */
    protected function sendAlertToGraphql(string $alertId, string $message)
    {
        $response = Http::post('http://login-reg-graphql-container:4000/graphql', [
            'query' => "
                mutation {
                    createAlert(alertId: \"{$alertId}\", message: \"{$message}\") {
                        success
                        message
                    }
                }
            "
        ]);

        if ($response->failed()) {
            $this->error("Failed to send alert data to GraphQL server: " . $response->body());
        } else {
            $this->info("Alert data sent successfully to GraphQL server.");
        }
    }
}
