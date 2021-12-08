@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Stock In - New Record</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('stock.index')}}">Stock In</a></li>
			  <li class="breadcrumb-item">Add New Stock</li>
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
								<h3 class="card-title">Stock Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('stock.store')}}" method="POST">
								@csrf
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Reference No:</label>
									<div class="col-lg-9">	
										<input type="text" name="reference_no" value="{{ old('reference_no') }}" class="@error('reference_no') is-invalid @enderror form-control" placeholder="Reference No" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Vehicle:</label>
									<div class="col-lg-9">	
										<input type="text" name="vehicle" value="{{ old('vehicle') }}" class="@error('vehicle') is-invalid @enderror form-control" placeholder="Vehicle" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Vehicle Plate Number:</label>
									<div class="col-lg-9">	
										<input type="text" name="vehicle_plate" value="{{ old('vehicle_plate') }}" class="@error('vehicle_plate') is-invalid @enderror form-control" placeholder="Vehicle Plate Number" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Driver Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="driver_name" value="{{ old('driver_name') }}" class="@error('driver_name') is-invalid @enderror form-control" placeholder="Driver Name" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Driver Phone No:</label>
									<div class="col-lg-9">	
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">+63</span>
											</div>
											<input type="text" name="contact_number" value="{{ old('contact_number') }}" class="@error('contact_number') is-invalid @enderror form-control" placeholder="Phone" >
										</div>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Received By:</label>
									<div class="col-lg-9">	
										<input type="text" name="received_by" value="{{ old('received_by') }}" class="@error('received_by') is-invalid @enderror form-control" placeholder="Received By" >
									</div>
								</div>

                                <!-- Date -->
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Date Received:</label>
									<div class="col-lg-9">	
										<div class="input-group date" id="reservationdate" data-target-input="nearest">
											<input type="text" name="received_at" class="@error('received_at') is-invalid @enderror form-control datetimepicker-input" data-target="#reservationdate"/>
											<div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fa fa-calendar"></i></div>
											</div>
										</div>
									</div>
                                </div>

								<div class="text-right">
									<button type="submit" class="btn btn-primary">NEXT <i class="icon-paperplane ml-2"></i></button>
								</div>
							</div>
						</div>
						<div class="card-footer clearfix">
							
						</div>
					</div>
				</div>
			</div>
		</div>	
	</section>
	@push('scripts')
	<!-- Javascript -->
	<!-- Vendors -->
	<script>
		$(function () {
			
        	//Date picker
			$('#reservationdate').datetimepicker({
				format: 'L'
			});
		});
	</script>
	@endpush('scripts')
@endsection