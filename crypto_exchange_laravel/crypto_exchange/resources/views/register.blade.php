@extends('masterHome')
@section('pagetitle', 'TRADEX')
@section('pageContents')

<section class="py-4 py-md-5 my-5">
        <div class="container py-md-5">
            @if (session('debug'))
                <div class="alert alert-warning">
                    {{ session('debug') }}
                </div>
            @endif

            @if (session('grpcResponse'))
                <div class="alert alert-info">
                    {{ session('grpcResponse') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 text-center"><img class="img-fluid w-100" src="assets/img/illustrations/register.svg"></div>
                <div class="col-md-5 col-xl-4 text-center text-md-start">
                    <h2 class="display-6 fw-bold mb-5"><span class="underline pb-1"><strong>Sign up</strong></span></h2>
                    <form method="post" enctype="multipart/form-data" data-bs-theme="light">
                        @csrf
                        <div class="mb-3"><input class="shadow-sm form-control" type="email" name="email" placeholder="Email" required></div>
                        <div class="mb-3"><input class="shadow-sm form-control" type="text" name="firstname" placeholder="First name" required></div>
                        <div class="mb-3"><input class="shadow-sm form-control" type="text" name="lastname" placeholder="Last name" required></div>
                        <div class="mb-3"><input class="shadow-sm form-control" type="password" name="password" placeholder="Password" required></div>
                        <div class="mb-3"><input class="shadow-sm form-control" type="password" name="password_confirmation" placeholder="Repeat Password" required></div>
                        <input class="mb-3" type="file" name="document">
                        <div class="mb-5"><button class="btn btn-primary shadow" type="submit">Create account</button></div>
                    </form>

                    <p class="text-muted">Have an account? <a href="login">Log in&nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-arrow-narrow-right">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <line x1="15" y1="16" x2="19" y2="12"></line>
                                <line x1="15" y1="8" x2="19" y2="12"></line>
                            </svg></a>&nbsp;</p>
                </div>
            </div>
        </div>
    </section>

<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/startup-modern.js"></script>
@stop

