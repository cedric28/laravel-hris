@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Client - {{ ucwords($client->name)}} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('client.index')}}">Clients</a></li>
			           <li class="breadcrumb-item">Edit Details</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
      	<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							@include('partials.message')
							@include('partials.errors')
							<div class="row">
								<h3 class="card-title">Client Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('client.update', $client->id)}}" method="POST">
								@csrf
								@method('PATCH')
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Reference No:</label>
									<div class="col-lg-9">	
										<input type="text" disabled="disabled" name="reference_no" value="{{ old('reference_no', $client->reference_no) }}" class="@error('reference_no') is-invalid @enderror form-control" placeholder="Reference No" >
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Client Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="name" value="{{ old('name', $client->name) }}" class="@error('name') is-invalid @enderror form-control" placeholder="Client Name" >
									</div>
								</div>

        <div class="form-group row">
									<label class="col-lg-3 col-form-label">Short Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="short_name" value="{{ old('short_name',$client->short_name) }}" class="@error('short_name') is-invalid @enderror form-control" placeholder="Client Short Name" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-form-label col-lg-3">Company Address</label>
									<div class="col-lg-9">
										<textarea rows="3" cols="3" name="address" class="@error('address') is-invalid @enderror form-control" placeholder="Address">{{ old('address', $client->address) }}</textarea>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Phone:</label>
									<div class="col-lg-9">	
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">+63</span>
											</div>
											<input type="text" name="contact_number" value="{{ old('contact_number', $client->contact_number) }}" class="@error('contact_number') is-invalid @enderror form-control" placeholder="Phone" >
										</div>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Email:</label>
									<div class="col-lg-9">	
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fas fa-envelope"></i></span>
											</div>
											<input type="email" name="email" value="{{ old('email', $client->email) }}" class="@error('email') is-invalid @enderror form-control" placeholder="Email" >
										</div>
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
      
        <script src="{{ asset('vendors/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/jquery.scrollbar/jquery.scrollbar.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/jquery-scrollLock/jquery-scrollLock.min.js') }}"></script>
        @endpush('scripts')
@endsection