@extends('layouts.app')

@push('styles')
<style>
body {
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-image: linear-gradient(180deg, #5e1d66 10%, #3a4973 100%);
    
}

.container {
    text-align: center;
}

.btn {
    display: inline-block;
    padding: 12px 30px;
    margin: 10px;
    font-size: 18px;
    font-weight: bold;
    color: #333;
    background-color: #fff;
    border-radius: 5px;
    text-decoration: none;
    transition: background 0.3s, transform 0.2s;
    font-family: 'Raleway', sans-serif;
}

.btn:hover {
    background-color: #f0f0f0;
    transform: translateY(-2px);
}

h1 {
    color: #fff;
    font-size: 52px;
    margin-bottom: 0px;
    font-family: 'Sacramento', cursive;
}

p {
    color: #fff;
    font-size: 22px;
    margin-bottom: 25px;
    margin-top: 0px;
    font-family: 'Dancing Script', cursive;
}
</style>
@endpush

@section('content')
<div class="container">
    <h1>Welcome to ParlourPOS.com</h1>
    <p>The ultimate beauty parlour POS.</p>

    @if (Route::has('login'))
    @auth
    <a href="{{ url('/dashboard') }}" class="btn">Home</a>
    @else
    <a href="{{ route('login') }}" class="btn">Log in</a>
    @endauth
    @endif
@endsection