<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('pagetitle') - (c) Leander Winters</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap.min.css') }}">
        <link href="https://fonts.googleapis.com/css?family=Raleway:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&amp;display=swap">
        <!-- Styles -->
        <style>
        </style>
    </head>
    <body>
    <body>
        <nav class="navbar navbar-expand-md fixed-top navbar-shrink py-3 navbar-light" id="mainNav" style="border-style: none;box-shadow: 0px -2px 20px;">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="home"><span style="font-size: 30px;">TRADE<span style="color: rgb(255, 210, 2);">X</span></span></a>
                <button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-1"><span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navcol-1">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link active" href="home">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="trade">Trade</a></li>
                        <li class="nav-item"><a class="nav-link" href="alerts">Price alerts</a></li>
                        <li class="nav-item"><a class="nav-link" href="wallet">My wallet</a></li>
                    </ul>
                    @if (Session::has('user'))
                        <a class="btn btn-primary shadow" role="button" href="logout" style="color: #24285b;background: rgb(255,210,2);border-radius: 0;border-top-left-radius: 20px;border-style: none;border-top-right-radius: 20px;border-bottom-right-radius: 20px;border-bottom-left-radius: 20px;margin: 10px;margin-top: 0px;margin-bottom: 0px;">Log out</a>
                    @else
                        <a class="btn btn-primary shadow" role="button" href="login" style="color: #24285b;background: rgb(255,210,2);border-radius: 0;border-top-left-radius: 20px;border-style: none;border-top-right-radius: 20px;border-bottom-right-radius: 20px;border-bottom-left-radius: 20px;margin: 10px;margin-top: 0px;margin-bottom: 0px;">Log In</a>
                        <a class="btn btn-primary shadow" role="button" href="register" style="background: rgb(255,210,2);color: rgb(36,40,91);border-style: none;border-top-left-radius: 20px;border-top-right-radius: 20px;border-bottom-right-radius: 20px;border-bottom-left-radius: 20px;margin-right: 10px;margin-left: 10px;">Register</a>
                    @endif
                </div>
            </div>
        </nav>

        @yield('pageContents')

    </body>
    <footer>
            <div class="container py-4 py-lg-5">
                <hr>
                <div class="text-muted d-flex justify-content-between align-items-center pt-3">
                    <p class="mb-0">Copyright © 2023 Leander Winters</p>
                </div>
            </div>
    </footer>
</html>

<script src="{{ asset('js/price-alerts.js') }}"></script> 

