@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Schedule - {{ ucwords($schedule->deployment->employee->name)}} to {{ ucwords($schedule->deployment->client->name)}} Company</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('schedule.index')}}">Schedules</a></li>
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
								<h3 class="card-title">User Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('schedule.update', $schedule->id)}}" method="POST" >
								@csrf
								@method('PATCH')
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Employee:</label>
									<div class="col-lg-9">
										<select id="deployment-id" name="deployment_id" class="form-control select2">
											<option value="">Select Employee</option>
											@foreach ($deployments as $deployment)
												<option value="{{ $deployment->id }}"{{ ($deployment->id == old('deployment_id', $deployment->id)) ? ' selected' : '' }}>{{ ucwords($deployment->employee->name) }} - {{ ucwords($deployment->client->name) }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Schedule:</label>
									<div class="col-lg-9">	
										<input type="text" name="slug" value="{{ old('slug',$schedule->slug) }}" class="@error('slug') is-invalid @enderror form-control" placeholder="Mon-Fri" >
									</div>
								</div>

								<div class="form-group row">
										<label class="col-lg-3 col-form-label">Time-in:</label>
										<div class="col-lg-9">
											<div class="input-group date" id="time_in" data-target-input="nearest">
												<input type="text" name="time_in"  value="{{ old('time_in',$schedule->time_in) }}" class="form-control datetimepicker-input" data-target="#time_in" />
												<div class="input-group-append" data-target="#time_in" data-toggle="datetimepicker">
													<div class="input-group-text"><i class="far fa-clock"></i></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Time-out:</label>
										<div class="col-lg-9">
											<div class="input-group date" id="time_out" data-target-input="nearest">
												<input type="text" name="time_out"  value="{{ old('time_out',$schedule->time_out) }}" class="form-control datetimepicker-input" data-target="#time_out" />
												<div class="input-group-append" data-target="#time_out" data-toggle="datetimepicker">
													<div class="input-group-text"><i class="far fa-clock"></i></div>
												</div>
											</div>
										</div>
									</div>

								<div class="text-right">
									<button type="submit" class="btn btn-success" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">Save <i class="icon-paperplane ml-2"></i></button>
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

								<script>
			$(function () {
				$('.select2').select2()

				$('#time_in').datetimepicker({
      format: 'LT'
    })

				$('#time_out').datetimepicker({
      format: 'LT'
    })

			});
		</script>
        @endpush('scripts')
@endsection