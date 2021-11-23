@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Point - {{ ucwords($point->point_name)}} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('point.index')}}">Points</a></li>
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
								<h3 class="card-title">Point Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('point.update', $point->id)}}" method="POST" >
								@csrf
								@method('PATCH')
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Point Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="point_name" value="{{ old('point_name', $point->point_name) }}" class="@error('point_name') is-invalid @enderror form-control" placeholder="Point Name" >
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Point Discount(In Decimal):</label>
									<div class="col-lg-9">	
										<input type="text" name="discount_rate" value="{{ old('discount_rate', $point->discount_rate) }}" class="@error('discount_rate') is-invalid @enderror form-control" placeholder="Point Rate" >
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Point To Be Earn:</label>
									<div class="col-lg-9">	
										<input type="text" name="point" value="{{ old('point', $point->point) }}" class="@error('discount_rate') is-invalid @enderror form-control" placeholder="Point" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Price Per Point:</label>
									<div class="col-lg-9">	
										<input type="text" name="price_per_point" value="{{ old('price_per_point', $point->price_per_point) }}" class="@error('price_per_point') is-invalid @enderror form-control" placeholder="Price Per Point" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Total Needed Point:</label>
									<div class="col-lg-9">	
										<input type="text" name="total_needed_point" value="{{ old('total_needed_point', $point->total_needed_point) }}" class="@error('total_needed_point') is-invalid @enderror form-control" placeholder="Total Needed Point" >
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