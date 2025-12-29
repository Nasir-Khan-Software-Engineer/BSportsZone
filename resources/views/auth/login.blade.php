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
.thm-btn-bg{
    background-color: #5e1d66;
    color: #ffffff;
}
.thm-btn-bg:hover{
    background-color: #992fa7ff;
    color: #ffffff;
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            @if(session('show_captcha'))
                                <div class="col-md-8 offset-md-4 mb-3 text-center">
                                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.key') }}"></div>
                                </div>
                                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                            @endif
                        </div>


                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm w-100">
                                    {{ __('Login') }}
                                </button>
                            </div>

                            @if ($errors->has('account'))
                                <div class="alert alert-danger mt-3">
                                    {{ $errors->first('account') }}
                                </div>
                            @endif

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
