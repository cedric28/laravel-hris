@extends('layouts.app')

@section('login')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row w-100 shadow rounded bg-white overflow-hidden" style="max-width: 1000px;">
        <!-- Left Side: Login Form -->
        <div class="col-md-6 p-5 d-flex flex-column justify-content-center">
            <div class="text-center mb-4">
                <img src="{{ asset('dist/img/logo.png') }}" alt="Logo" style="height: 80px;">
            </div>
            <h2 class="mb-3 text-center">Log in to your Account</h2>
            <p class="text-muted mb-4 text-center">Welcome back! Please log in to your account.</p>
            
            <form class="login-form" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" name="email" required autocomplete="email" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" name="password" required autocomplete="current-password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3 d-flex justify-content-between align-items-center">
                    
                    <a href="/reset" class="text-muted">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block mb-3" style="border-radius: 30px;">Log in</button>
                
            </form>
        </div>

        <!-- Right Side: GIF -->
        <div class="col-md-6 p-0">
            <div class="d-flex align-items-center justify-content-center h-100 bg-primary text-white" style="position: relative;">
                <img src="{{ asset('dist/img/samplepoeple.gif') }}" alt="GIF" class="img-fluid h-100" style="object-fit: cover; border-top-right-radius: 10px; border-bottom-right-radius: 10px;">
                <div class="text-overlay text-center p-3" style="position: absolute; bottom: 10%; width: 90%; background: rgba(0, 0, 0, 0.5); border-radius: 10px;">
                    <p>Connect with every application. Everything you need in one place.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f5f5f5; /* Light background for better contrast */
    }

    .container {
        padding: 20px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .form-control {
        border-radius: 30px;
        padding: 10px 15px;
    }

    .text-overlay p {
        color: #fff;
        font-size: 14px;
        margin: 0;
    }

    @media (max-width: 768px) {
        .row {
            flex-direction: column;
            border-radius: 0;
        }
        .col-md-6 {
            width: 100%;
        }
        .bg-primary {
            border-radius: 0;
        }
        .gif-container {
            height: auto;
        }
    }
</style>
@endsection
