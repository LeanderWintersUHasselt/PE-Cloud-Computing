@extends('masterHome')
@section('pagetitle', 'TRADEX')
@section('pageContents')

<body>
    <section class="py-5 mt-5">
        <div class="container py-4 py-xl-5">
            <div class="row gy-4 gy-md-0">
                <div class="text-center text-md-start d-flex d-sm-flex d-md-flex justify-content-center align-items-center justify-content-md-start align-items-md-center justify-content-xl-center">
                    <h1 class="display-5 fw-bold mb-4">Never miss a trade, set an&nbsp;<span class="underline">Alert</span>.</h1>
                </div>            
            </div>
            <div class="row gy-4 gy-md-0">
                <div class="col-md-6 text-center text-md-start d-flex d-sm-flex d-md-flex justify-content-center align-items-center justify-content-md-start align-items-md-center justify-content-xl-center">
                    <div style="max-width: 350px;">
                        <div>
                            <p class="text-muted my-4">Tincidunt laoreet leo, adipiscing taciti tempor. Primis senectus sapien, risus donec ad fusce augue interdum.</p>
                            <input type="number" id="price" class="form-control" placeholder="Price">
                            <select name="coin" id="coin">
                                <option value="BTC">BTC</option>
                                <option value="ETH">ETH</option>
                            </select>
                            <a class="btn btn-primary btn-lg me-2" role="button" onclick="sendPriceAlert()">Set alert</a>
                        </div>    
                        <div id="responseMessage"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  
</body>
    <script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('js/startup-modern.js') }}"></script>

@stop
