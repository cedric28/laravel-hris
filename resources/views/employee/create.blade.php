@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Applicant - New Record</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('employee.index')}}">Applicants</a></li>
			           <li class="breadcrumb-item">Add New Applicant</li>
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
								<h3 class="card-title">Applicant Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('employee.store')}}" method="POST" enctype="multipart/form-data">
								@csrf
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Fullname:</label>
									<div class="col-lg-9">	
										<input type="text" name="name" value="{{ old('name') }}" class="@error('name') is-invalid @enderror form-control" placeholder="e.g Juan Dela Cruz" >
									</div>
								</div>
        <div class="form-group row">
									<label class="col-lg-3 col-form-label">Nickname:</label>
									<div class="col-lg-9">	
										<input type="text" name="nickname" value="{{ old('nickname') }}" class="@error('nickname') is-invalid @enderror form-control" placeholder="Nickname" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Date of Birth:</label>
									<div class="col-lg-9">
										<div class="input-group date" id="birthdate" data-target-input="nearest">
										<input type="text" name="birthdate" value="{{ old('birthdate') }}" placeholder="e.g 2022-08-20" onkeydown="return false;" class="@error('birthdate') is-invalid @enderror form-control datetimepicker-input" data-target="#reservationdate"/>
										<div class="input-group-append" data-target="#birthdate" data-toggle="datetimepicker">
											<div class="input-group-text"><i class="fa fa-calendar"></i></div>
										</div>
									</div>
								</div>
						</div>

						<div class="form-group row">
							<label class="col-lg-3 col-form-label">Gender:</label>
							<div class="col-lg-9">	
								<select name="gender_id" class="form-control">
									<option value="">Please select</option>
									@foreach ($gender as $gend)
										<option value="{{ $gend->id }}" {{ $gend->id == old('gender_id')  ? ' selected' : '' }}>
											{{ $gend->name }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

							<div class="form-group row">
								<label class="col-lg-3 col-form-label">Civil Status:</label>
								<div class="col-lg-9">	
									<select name="civil_status_id" class="form-control">
										<option value="">Please select</option>
										@foreach ($civilStatus as $civilStat)
											<option value="{{ $civilStat->id }}"{{ $civilStat->id == old('civil_status_id')  ? ' selected' : '' }}>
												{{ $civilStat->name }}
											</option>
										@endforeach
									</select>
								</div>
							</div>

								<div class="form-group row">
									<label class="col-form-label col-lg-3">Address</label>
									<div class="col-lg-9">
										<textarea rows="3" cols="3" name="address" class="@error('address') is-invalid @enderror form-control" placeholder="Address">{{ old('address') }}</textarea>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Phone:</label>
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
									<label class="col-lg-3 col-form-label">Email:</label>
									<div class="col-lg-9">	
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fas fa-envelope"></i></span>
											</div>
											<input type="email" name="email" value="{{ old('email') }}" class="@error('email') is-invalid @enderror form-control" placeholder="Email" >
										</div>
									</div>
								</div>

								<div class="text-right">
									<button type="submit" class="btn btn-primary">Next <i class="icon-paperplane ml-2"></i></button>
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

								<script>
									$(function () {
									var bindDatePicker = function() {
											$("#birthdate").datetimepicker({
												showClear: true,
												showClose: true,
												allowInputToggle: true,
												useCurrent: false,
												ignoreReadonly: true,
												format:'YYYY-MM-DD',
												icons: {
													time: "fas fa-clock",
													date: "fas fa-calendar",
													up: "fas fa-chevron-up",
													down: "fas fa-chevron-down"
												}
											});
									}
								
									bindDatePicker();
								});
								</script>

        @endpush('scripts')
@endsection