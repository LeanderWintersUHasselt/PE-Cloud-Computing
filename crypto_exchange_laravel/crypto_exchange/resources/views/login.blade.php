@extends('masterHome')
@section('pagetitle', 'TRADEX')
@section('pageContents')

<section class="py-4 py-md-5 my-5">
    <div class="container py-md-5">
        @if (Session::has('error'))
            <div class="alert alert-danger">
                {{ Session::get('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-5 col-xl-4 text-center text-md-start">
                <h2 class="display-6 fw-bold mb-5"><span class="underline pb-1"><strong>Login</strong><br></span></h2>
                <form id="loginForm" method="post" data-bs-theme="light">
                    @csrf
                    <div class="mb-3"><input class="shadow form-control" type="email" name="email" placeholder="Email"></div>
                    <div class="mb-3"><input class="shadow form-control" type="password" name="password" placeholder="Password"></div>
                    <div class="mb-5"><button class="btn btn-primary shadow" type="submit">Log in</button></div>
                </form>
                <p class="text-muted"><a href="forgotten-password.html">Forgot your password?</a></p>
            </div>
        </div>
    </div>
</section>

<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/startup-modern.js"></script>
@stop
