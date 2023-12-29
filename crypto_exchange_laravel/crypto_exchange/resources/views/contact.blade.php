@extends('masterHome')
@section('pagetitle', 'TRADEX')
@section('pageContents')

<header class="pt-5">
    <div class="container pt-4 pt-xl-5">
        <div class="row pt-5">
            <div class="col-md-8 text-center text-md-start mx-auto">
                <div class="text-center">
                    <h1 class="display-4 fw-bold mb-5">Ask our&nbsp;<span class="underline">chatbot</span> anything you want.</h1>
                </div>
            </div>
            <div class="col-12 col-lg-10 mx-auto">
                <div class="text-center position-relative"></div>
            </div>
        </div>
    </div>
</header>
<section >
    <div class="card border-light border-0 d-flex justify-content-center p-4">
        <div class="card-body">
            <div class="chatbot">
                <header>
                    <h2>Chatbot</h2>
                    <span class="close-btn material-symbols-outlined">close</span>
                </header>
                <ul class="chatbox">
                    <li class="chat incoming">
                    <span class="material-symbols-outlined">smart_toy</span>
                    <p>Hi there 👋<br>How can I help you today?</p>
                    </li>
                </ul>
                <div class="chat-input">
                    <textarea placeholder="Enter a message..." spellcheck="false" required></textarea>
                    <span id="send-btn" class="material-symbols-rounded">send</span>
                </div>
            </div>
        </div>
    </div>
</section>
    
<script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('js/startup-modern.js') }}"></script>
<script src="{{ URL::asset('js/chatbot.js') }}"></script>


@stop