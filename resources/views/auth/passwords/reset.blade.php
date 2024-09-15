@extends('layouts.app')

@section('login')
<!-- Main content -->
<div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-success" 
style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">
        <div class="card-header text-center">
        <a href="#" class="h1">
        <img src="{{ asset('dist/img/logo.png') }}" alt="Logo" class="" style="opacity: .8;height: 100px;max-height: none !important;">
        </a>
        </div>
        <div class="card-body">
        <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
        @include('partials.message')
        	@include('partials.errors')
        <form class="login-form" method="POST" action="{{ route('resetPassword') }}">
            @csrf
            <div class="input-group mb-3">
                <input type="text" class="form-control @error('email') is-invalid @enderror" placeholder="E-mail" value="{{ old('email') }}" name="email" required autocomplete="email" autofocus>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>

              <div class="input-group mb-3">
                <input type="text" class="form-control @error('hint') is-invalid @enderror" placeholder="Hint" value="{{ old('hint') }}" name="hint" required autofocus>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-eye"></span>
                    </div>
                </div>
            </div>

             <div class="input-group mb-3">
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="New Password"  autocomplete="new-password">
                <div class="input-group-append">
                    <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>

             <div class="input-group mb-3">
               <input type="password" class="form-control" name="confirm-password"  placeholder="Confirm New Password"  autocomplete="new-password">
                <div class="input-group-append">
                    <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
         
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block" 
style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">{{ __('Reset Password') }} <i class="icon-circle-right2 ml-2"></i></button>
                </div>
            <!-- /.col -->
            </div>
                 <div class="row mt-2">
                <div class="col-12">
                    <a href="/login" >{{ __('Back To Login') }} <i class="icon-circle-right2 ml-2"></i></a>
                </div>
            <!-- /.col -->
            </div>
        </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
@endsection
