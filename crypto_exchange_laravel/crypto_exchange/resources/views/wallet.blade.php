@extends('masterHome')
@section('pagetitle', 'TRADEX')
@section('pageContents')

<body>
    <section class="py-5 mt-5">
        <div class="container py-4 py-xl-5">
            <div class="col-md-12 text-center text-md-start mx-auto">
                <div class="text-center">
                    <h1 class="display-5 fw-bold mb-4">Make a deposit, a withdrawal or check your&nbsp;<span class="underline">balance</span>.</h1>
                </div>
            </div>
            <div class="row gy-4 gy-md-0">
                <div class="col-md-6 text-center text-md-start d-flex d-sm-flex d-md-flex justify-content-center align-items-center justify-content-md-start align-items-md-center justify-content-xl-center">
                    <div style="max-width: 350px;">
                        <p class="text-muted my-4">Tincidunt laoreet leo, adipiscing taciti tempor. Primis senectus sapien, risus donec ad fusce augue interdum.</p>
                        <input type="number" id="amount" class="form-control" placeholder="Amount">
                        <a class="btn btn-primary btn-lg me-2" role="button" onclick="sendWalletRequest('DEPOSIT')">Deposit</a>
                        <a class="btn btn-primary btn-lg me-2" role="button" onclick="sendWalletRequest('WITHDRAW')">Withdraw</a>
                        <div id="responseMessage"></div>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-start d-flex d-sm-flex d-md-flex justify-content-center align-items-center justify-content-md-start align-items-md-center justify-content-xl-center">
                    <div style="max-width: 350px;">
                        <div class="balance-item">
                            <img src="{{ asset('img/EUR.svg') }}" alt="EUR Icon" width="32" height="32">
                            <p id="eurBalance"></p>
                        </div>
                        <div class="balance-item">
                            <img src="{{ asset('img/BTC.svg') }}" alt="BTC Icon" width="32" height="32">
                            <p id="btcBalance"></p>
                        </div>
                        <div class="balance-item">
                            <img src="{{ asset('img/ETH.svg') }}" alt="ETH Icon" width="32" height="32">
                            <p id="ethBalance"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  
</body>
    <script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('js/startup-modern.js') }}"></script>
    <script src="{{ asset('js/wallet.js') }}"></script> 

@stop
