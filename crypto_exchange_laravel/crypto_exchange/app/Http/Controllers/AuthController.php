<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Kyc\KYCRequest;
use Kyc\KYCServiceClient;
use Grpc\ChannelCredentials;
use Grpc\STATUS_OK;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Send the request to the GraphQL server

        // TODO return userID
        $response = Http::post('http://login-reg-graphql-container:4000/graphql', [
            'query' => "
                mutation {
                    login(email: \"{$request->email}\", password: \"{$request->password}\") {
                        success
                        message
                    }
                }
            "
        ]);
        
        $responseData = $response->json();

        // Check if login was successful and start the session
        if ($responseData && $responseData['data']['login']['success']) {
            // Store user info in session
            Session::put('user', $responseData['data']['login']);
    
            // Redirect to the home page or another appropriate page
            Session::flash('success', 'Logged in successfully');
            return redirect('/home');
        } else {
            // Redirect back with an error message
            $errorMessage = $responseData['data']['login']['message'] ?? 'Invalid credentials';
            Session::flash('error', $errorMessage);
    
            return redirect('/login');
        }
    }

    public function register(Request $request)
    {
        // Validation for request data can be added here
        \Log::info($request->all());

        $validatedData = $request->validate([
            'email' => 'required|email',
            'firstname' => 'required',
            'lastname' => 'required',
            'password' => 'required|confirmed',
            'document' => 'required|file'
        ], [
            'document.required' => 'The document field is required.',
        ]);

        if (!$this->checkKYC($request)) {
            return redirect('/register')->withErrors(['register' => 'KYC verification failed']);
        }

        // Send the request to the GraphQL server
        $response = Http::post('http://login-reg-graphql-container:4000/graphql', [
            'query' => "
                mutation {
                    register(email: \"{$request->email}\", password: \"{$request->password}\", firstName: \"{$request->firstname}\", lastName: \"{$request->lastname}\") {
                        success
                        message
                    }
                }
            "
        ]);

        $responseData = $response->json();

        // Check if registration was successful
        if ($responseData && $responseData['data']['register']['success']) {
            return redirect('/login')->with('success', 'Account created successfully. Please log in.');
        } else {
            $errorMessage = $responseData['data']['register']['message'] ?? 'Registration failed';
            return redirect('/register')->withErrors(['register' => $errorMessage]);
        }
    }


    public function logout()
    {
        Session::forget('user');
        return redirect('/home');
    }


    public function checkKYC(Request $request)
    {
        try {
            $documentContent = file_get_contents($request->file('document'));
            Session::flash('debug', 'Received document content'); // Debug statement
    
            // Set up the gRPC client and make the call
            $client = new KYCServiceClient('kyc-python-grpc-container:4004', [
                'credentials' => \Grpc\ChannelCredentials::createInsecure(),
            ]);
            Session::flash('debug', 'gRPC client set up'); // Debug statement
    
            $grpcRequest = new KYCRequest();
            $grpcRequest->setDocumentContent($documentContent);
    
            list($response, $status) = $client->CheckKYC($grpcRequest)->wait();
            Session::flash('debug', 'gRPC call made'); // Debug statement
    
            if ($status->code !== \grpc\STATUS_OK) {
                Session::flash('grpcResponse', 'gRPC Error: ' . $status->details);
                return false;
            }
    
            if ($response->getVerified()) {
                Session::flash('grpcResponse', 'KYC Verified');
                return true;
            } else {
                Session::flash('grpcResponse', 'KYC Failed');
                return false;
            }
        } catch (\Exception $e) {
            Session::flash('grpcResponse', 'Exception: ' . $e->getMessage());
            return false;
        }
    }
    
    
}