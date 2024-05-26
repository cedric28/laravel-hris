@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Employee - New Record</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('deployment.index')}}">Employees</a></li>
			  <li class="breadcrumb-item">Add New Employee</li>
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
								<h3 class="card-title">Employee Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('deployment.store')}}" method="POST">
								@csrf

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Client:</label>
									<div class="col-lg-9">
										<select id="client-id" name="client_id" class="form-control select2">
											<option value="">Select Client</option>
											@foreach ($clients as $client)
												<option value="{{ $client->id }}"{{ ($client->id == old('client_id')) ? 'selected' : '' }}>{{ ucwords($client->name) }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Employee:</label>
									<div class="col-lg-9">
										<select id="employee-id" name="employee_id" class="form-control select2">
											<option value="">Select Employee</option>
											@foreach ($employees as $employee)
												<option value="{{ $employee->id }}"{{ ($employee->id == old('employee_id')) ? ' selected' : '' }}>{{ ucwords($employee->last_name) }}, {{ ucwords($employee->first_name) }} {{ ucwords($employee->middle_name) }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row">
											<label class="col-lg-3 col-form-label">Employment Type</label>
											<div class="col-lg-9">	
												<select name="employment_type_id" class="form-control">
													<option value="">Please select</option>
													@foreach ($employmentTypes as $employmentType)
													<option value="{{ $employmentType->id }}"{{ ($employmentType->id == old('employment_type_id')) ? ' selected' : '' }}>
															{{ $employmentType->name }}
														</option>
													@endforeach
												</select>
											</div>
										</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Position:</label>
									<div class="col-lg-9">	
										<input type="text" name="position" value="{{ old('position') }}" class="@error('position') is-invalid @enderror form-control" placeholder="Position" >
									</div>
								</div>


								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Start Date</label>
									<div class="col-lg-9">	
											<div class="input-group date" id="startdate" data-target-input="nearest">
													<input type="text" name="start_date"  value="{{ old('start_date') }}" class="form-control datetimepicker-input" data-target="#startdate"/>
														<div class="input-group-append" data-target="#startdate" data-toggle="datetimepicker">
															<div class="input-group-text"><i class="fa fa-calendar"></i></div>
														</div>
										</div>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">End Date</label>
									<div class="col-lg-9">	
											<div class="input-group date" id="enddate" data-target-input="nearest">
													<input type="text" name="end_date"  value="{{ old('end_date')}}" class="form-control datetimepicker-input" data-target="#enddate"/>
														<div class="input-group-append" data-target="#enddate" data-toggle="datetimepicker">
															<div class="input-group-text"><i class="fa fa-calendar"></i></div>
														</div>
										</div>
									</div>
								</div>

								<div class="text-right">
									<button type="submit" class="btn btn-success">Save <i class="icon-paperplane ml-2"></i></button>
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
				$('.select2').select2()
				//Date picker
				$('#startdate').datetimepicker({
						format: 'L',
					 daysOfWeekDisabled: [0, 6]
				});

				$('#enddate').datetimepicker({
						format: 'L',
					 daysOfWeekDisabled: [0, 6]
				});
			});
		</script>
        @endpush('scripts')
@endsection