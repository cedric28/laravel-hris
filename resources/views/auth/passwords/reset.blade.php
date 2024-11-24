@extends('layouts.app')

@section('login')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row w-100 shadow rounded bg-white overflow-hidden" style="max-width: 1000px;">
        <!-- Left Side: Reset Password Form -->
        <div class="col-md-6 p-5 d-flex flex-column justify-content-center">
            <div class="text-center mb-4">
                <img src="{{ asset('dist/img/logo.png') }}" alt="Logo" style="height: 80px;">
            </div>
            <h2 class="mb-3 text-center">Reset Your Password</h2>
            <p class="text-muted mb-4 text-center">Forgot your password? No worries! Reset it below.</p>

            @include('partials.message')
            @include('partials.errors')

            <form method="POST" action="{{ route('resetPassword') }}">
                @csrf

                <div class="form-group mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email">
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="hint" class="form-label">Hint</label>
                    <input id="hint" type="text" class="form-control @error('hint') is-invalid @enderror" name="hint" value="{{ old('hint') }}" required autofocus placeholder="Enter your password hint">
                    @error('hint')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Enter your new password">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="confirm-password" class="form-label">Confirm New Password</label>
                    <input id="confirm-password" type="password" class="form-control" name="confirm-password" required autocomplete="new-password" placeholder="Confirm your new password">
                </div>

                <button type="submit" class="btn btn-primary btn-block mb-3" style="border-radius: 30px;">Reset Password</button>

                <p class="text-center text-muted mb-0">
                    <a href="/login" class="text-primary">Back to Login</a>
                </p>
            </form>
        </div>

        <!-- Right Side: Illustration or Image -->
        <div class="col-md-6 p-0">
            <div class="d-flex align-items-center justify-content-center h-100 bg-primary text-white">
                <img src="{{ asset('dist/img/samplepoeple.gif') }}" alt="Illustration" class="img-fluid h-100" style="object-fit: cover; border-top-right-radius: 10px; border-bottom-right-radius: 10px;">
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
    }
</style>
@endsection
