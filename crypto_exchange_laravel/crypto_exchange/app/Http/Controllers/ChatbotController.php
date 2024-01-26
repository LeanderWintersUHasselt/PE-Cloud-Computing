<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        
        // Format the SOAP request (XML)
        $soapRequest = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/'>
                            <soapenv:Body>
                                <message>$message</message>
                            </soapenv:Body>
                        </soapenv:Envelope>";

        // Send the SOAP request to the Go chatbot
        $response = Http::withHeaders([
            'Content-Type' => 'text/xml; charset=utf8',
        ])->send('POST', 'http://chatbot-go-soap-container:4005/soap', [
            'body' => $soapRequest,
        ]);
        

        // Process the SOAP response from the Go chatbot
        $responseBody = $response->body();

        // Parse the SOAP XML response to get the chatbot's message
        $chatbotMessage = $this->parseChatbotResponse($responseBody);

        return response()->json([
            'message' => $chatbotMessage
        ]);
    }

    private function parseChatbotResponse($soapResponse)
    {
        // Load the SOAP response as XML
        $xml = simplexml_load_string($soapResponse, 'SimpleXMLElement', LIBXML_NOCDATA);
    
        // Register the SOAP namespace
        $xml->registerXPathNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
    
        // Use XPath to query the messageResponse element
        $result = $xml->xpath('//soapenv:Body/messageResponse');
    
        // Extract the chatbot message
        $chatbotMessage = $result ? (string)$result[0] : "Oopsie woopsie, no response from chatbot";
    
        // Log the extracted chatbot message
        \Log::info('Extracted Chatbot Message:', ['message' => $chatbotMessage]);
    
        return $chatbotMessage;
    }
    
}
