@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Feedback - New Record</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('feedback.index')}}">Feedbacks</a></li>
			  									<li class="breadcrumb-item">Add New Feedback</li>
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
								<h3 class="card-title">Feedback Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('feedback.store')}}" method="POST">
								@csrf

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Employee:</label>
									<div class="col-lg-9">
										<select id="deployment-id" name="deployment_id" class="form-control select2">
											<option value="">Select Employee</option>
											@foreach ($deployments as $deployment)
												<option value="{{ $deployment->id }}"{{ ($deployment->id === old('deployment_id')) ? ' selected' : '' }}>{{  ucwords($deployment->employee->last_name).", ".ucwords($deployment->employee->first_name)." ".ucwords($deployment->employee->middle_name) }} - {{ ucwords($deployment->client->name) }}</option>
											@endforeach
										</select>
									</div>
								</div>

							<div class="form-group row">
							<label class="col-lg-3 col-form-label">1. Always on time (or even early) for meetings and conferences:</label>
							<div class="col-lg-9">	
								<select name="always_on_time" class="form-control">
									<option value="">Please select</option>
									@foreach ($always_on_time as $always)
										<option value="{{ $always['name'] }}" {{ $always['name'] == old('always_on_time')  ? ' selected' : '' }}>
											{{ $always['name'] }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-lg-3 col-form-label">2. Prompt and on time for the start of each workday.</label>
							<div class="col-lg-9">	
								<select name="prompt_and_on_time" class="form-control">
									<option value="">Please select</option>
									@foreach ($prompt_and_on_time as $always)
										<option value="{{ $always['name'] }}" {{ $always['name'] == old('prompt_and_on_time')  ? ' selected' : '' }}>
											{{ $always['name'] }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-lg-3 col-form-label">3. Adheres to the schedule whenever possible.</label>
							<div class="col-lg-9">	
								<select name="adheres_to_the_schedule" class="form-control">
									<option value="">Please select</option>
									@foreach ($adheres_to_the_schedule as $always)
										<option value="{{ $always['name'] }}" {{ $always['name'] == old('adheres_to_the_schedule')  ? ' selected' : '' }}>
											{{ $always['name'] }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-lg-3 col-form-label">4. Very reliable about being at work on time.</label>
							<div class="col-lg-9">	
								<select name="very_reliable_at_work" class="form-control">
									<option value="">Please select</option>
									@foreach ($very_reliable_at_work as $always)
										<option value="{{ $always['name'] }}" {{ $always['name'] == old('very_reliable_at_work')  ? ' selected' : '' }}>
											{{ $always['name'] }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

							<div class="form-group row">
							<label class="col-lg-3 col-form-label">5. Inspires others to improve their attendance.</label>
							<div class="col-lg-9">	
								<select name="inspires_others_to_improve_attendance" class="form-control">
									<option value="">Please select</option>
									@foreach ($inspires_others_to_improve_attendance as $always)
										<option value="{{ $always['name'] }}" {{ $always['name'] == old('inspires_others_to_improve_attendance')  ? ' selected' : '' }}>
											{{ $always['name'] }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

							<div class="form-group row">
							<label class="col-lg-3 col-form-label">6. Is frequently late to work.</label>
							<div class="col-lg-9">	
								<select name="is_frequently_late_to_work" class="form-control">
									<option value="">Please select</option>
									@foreach ($is_frequently_late_to_work as $always)
										<option value="{{ $always['name'] }}" {{ $always['name'] == old('is_frequently_late_to_work')  ? ' selected' : '' }}>
											{{ $always['name'] }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-lg-3 col-form-label">7. Unreliable about reporting to work on time.</label>
							<div class="col-lg-9">	
								<select name="unreliable_about_reporting" class="form-control">
									<option value="">Please select</option>
									@foreach ($unreliable_about_reporting as $always)
										<option value="{{ $always['name'] }}" {{ $always['name'] == old('unreliable_about_reporting')  ? ' selected' : '' }}>
											{{ $always['name'] }}
										</option>
									@endforeach
								</select>
							</div>
						</div>


						<div class="form-group row">
							<label class="col-lg-3 col-form-label">8. Unwilling to work beyond scheduled hours.</label>
							<div class="col-lg-9">	
								<select name="unwilling_to_work_beyond_scheduled_hours" class="form-control">
									<option value="">Please select</option>
									@foreach ($unwilling_to_work_beyond_scheduled_hours as $always)
										<option value="{{ $always['name'] }}" {{ $always['name'] == old('unwilling_to_work_beyond_scheduled_hours')  ? ' selected' : '' }}>
											{{ $always['name'] }}
										</option>
									@endforeach
								</select>
							</div>
						</div>


						<div class="form-group row">
							<label class="col-lg-3 col-form-label">9. Not a dependable employee.</label>
							<div class="col-lg-9">	
								<select name="not_a_dependable_employee" class="form-control">
									<option value="">Please select</option>
									@foreach ($not_a_dependable_employee as $always)
										<option value="{{ $always['name'] }}" {{ $always['name'] == old('not_a_dependable_employee')  ? ' selected' : '' }}>
											{{ $always['name'] }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-lg-3 col-form-label">10. Work results are inconsistent and frequently need to be reviewed.</label>
							<div class="col-lg-9">	
								<select name="work_results_are_inconsistent" class="form-control">
									<option value="">Please select</option>
									@foreach ($work_results_are_inconsistent as $always)
										<option value="{{ $always['name'] }}" {{ $always['name'] == old('work_results_are_inconsistent')  ? ' selected' : '' }}>
											{{ $always['name'] }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-lg-3 col-form-label">11. Rate</label>
							<div class="col-lg-9">	
								<select name="rate" class="form-control">
									<option value="">Please select</option>
									@foreach ($rate as $always)
										<option value="{{ $always['name'] }}" {{ $always['name'] == old('rate')  ? ' selected' : '' }}>
											{{ $always['name'] }}
										</option>
									@endforeach
								</select>
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
					format: 'L'
				});

				$('#enddate').datetimepicker({
					format: 'L'
				});

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