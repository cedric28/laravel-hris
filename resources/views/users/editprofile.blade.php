@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Profile - {{ ucwords($user->first_name) }} {{ ucfirst($user->last_name) }}</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
			  									<li class="breadcrumb-item">Update Profile Details</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
				<section class="content">
      	<div class="container-fluid">
		  				@include('partials.message')
								@include('partials.errors')
		  
			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<div class="row">
								<h3 class="card-title">Profile Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('update-profile')}}" method="POST" >
								@csrf
								@method('PATCH')
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Firstname:</label>
                                    <div class="col-lg-9">
                                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="@error('first_name') is-invalid @enderror form-control" placeholder="Firstname" >
                                    </div>
								</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Lastname:</label>
										<div class="col-lg-9">
											<input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="@error('last_name') is-invalid @enderror form-control" placeholder="Lastname">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Email:</label>
										<div class="col-lg-9">
											<input type="email" name="email" value="{{ old('email', $user->email) }}" class="@error('email') is-invalid @enderror form-control" placeholder="Email">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Password:</label>
										<div class="col-lg-9">
											<input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" name="password" autocomplete="new-password">
										</div>
									</div>


									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Confirm Password:</label>
										<div class="col-lg-9">
											<input type="password" class="form-control" name="confirm-password"  placeholder="Confirm Password"  autocomplete="new-password">
										</div>
									</div>
					
							
								<div class="text-right">
									<button type="submit" class="btn btn-primary">Save <i class="icon-paperplane ml-2"></i></button>
								</div>
							</form>
						</div>
						<div class="card-footer clearfix">
							
						</div>
					</div>
				</div>
			</div>
		</div>	
	</section>
	<!-- /page content -->
        @push('scripts')
        <!-- Javascript -->
        <!-- Vendors -->
		<script>

		</script>
        @endpush('scripts')
@endsection