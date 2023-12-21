<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

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
        $validatedData = $request->validate([
            'email' => 'required|email',
            'firstname' => 'required',
            'lastname' => 'required',
            'password' => 'required|confirmed'
        ]);

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
            // You can add logic here if you need to do anything after successful registration
            // For example, logging the user in immediately after registration

            // Redirect to the login page or any other appropriate page
            return redirect('/login')->with('success', 'Account created successfully. Please log in.');
        } else {
            // Redirect back with an error message
            $errorMessage = $responseData['data']['register']['message'] ?? 'Registration failed';
            return redirect('/register')->withErrors(['register' => $errorMessage]);
        }
    }


    public function logout()
    {
        Session::forget('user');
        return redirect('/home');
    }
}
