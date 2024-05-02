@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Applicant - {{ ucwords($employee->name)}} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('employee.index')}}">Applicants</a></li>
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
								<h3 class="card-title">Applicant Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
						
											<form action="{{ route('employee.update', $employee->id)}}" method="POST">
												@csrf
												@method('PATCH')
												<div class="card card-primary">
													<div class="card-header">
														Basic Information
													</div>
													<div class="card-body">
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Applicant No:</label>
															<div class="col-lg-9">	
																<input type="text" disabled="disabled" name="reference_no" value="{{ old('reference_no', $employee->reference_no) }}" class="@error('reference_no') is-invalid @enderror form-control" placeholder="Reference No" >
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Fullname:</label>
															<div class="col-lg-9">	
																<input type="text" name="name" value="{{ old('name', $employee->name) }}" class="@error('name') is-invalid @enderror form-control" placeholder="e.g Juan Dela Cruz" >
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Nickname:</label>
															<div class="col-lg-9">	
																<input type="text" name="nickname" value="{{ old('nickname', $employee->nickname) }}" class="@error('nickname') is-invalid @enderror form-control" placeholder="Nickname" >
															</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Date of Birth:</label>
																<div class="col-lg-9">
																	<div class="input-group date" id="birthdate" data-target-input="nearest">
																			<input type="text" name="birthdate" value="{{ old('birthdate',  $employee->birthdate) }}" placeholder="e.g 2022-08-20" onkeydown="return false;" class="@error('birthdate') is-invalid @enderror form-control datetimepicker-input" data-target="#reservationdate"/>
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
																		<option value="{{ $gend->id }}" {{ $gend->id == old('gender_id', $employee->gender_id )  ? ' selected' : '' }}>
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
																		<option value="{{ $civilStat->id }}"{{ $civilStat->id == old('civil_status_id', $employee->civil_status_id)  ? ' selected' : '' }}>
																			{{ $civilStat->name }}
																		</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-form-label col-lg-3">Address</label>
															<div class="col-lg-9">
																<textarea rows="3" cols="3" name="address" class="@error('address') is-invalid @enderror form-control" placeholder="Address">{{ old('address', $employee->address) }}</textarea>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Phone:</label>
															<div class="col-lg-9">	
																<div class="input-group mb-3">
																	<div class="input-group-prepend">
																		<span class="input-group-text">+63</span>
																	</div>
																	<input type="text" name="contact_number" value="{{ old('contact_number', $employee->contact_number) }}" class="@error('contact_number') is-invalid @enderror form-control" placeholder="Phone" >
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
																	<input type="email" name="email" value="{{ old('email', $employee->email) }}" class="@error('email') is-invalid @enderror form-control" placeholder="Email" >
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="card card-primary">
													<div class="card-header">
														In-Case of Emergency Information
													</div>
													<div class="card-body">
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Emergency Contact Name:</label>
															<div class="col-lg-9">	
																<input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}" class="@error('emergency_contact_name') is-invalid @enderror form-control" placeholder="Fullname" >
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Relationship:</label>
															<div class="col-lg-9">	
																<input type="text" name="emergency_relationship" value="{{ old('emergency_relationship', $employee->emergency_relationship) }}" class="@error('emergency_relationship') is-invalid @enderror form-control" placeholder="Relationship" >
															</div>
														</div>

														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Phone:</label>
															<div class="col-lg-9">	
																<div class="input-group mb-3">
																	<div class="input-group-prepend">
																		<span class="input-group-text">+63</span>
																	</div>
																	<input type="text" name="emergency_contact_number" value="{{ old('emergency_contact_number', $employee->emergency_contact_number) }}" class="@error('emergency_contact_number') is-invalid @enderror form-control" placeholder="Phone" >
																</div>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-form-label col-lg-3">Address</label>
															<div class="col-lg-9">
																<textarea rows="3" cols="3" name="emergency_address" class="@error('emergency_address') is-invalid @enderror form-control" placeholder="Address">{{ old('emergency_address', $employee->emergency_address) }}</textarea>
															</div>
														</div>
													</div>
												</div>
												<div class="card card-primary">
													<div class="card-header">
														Government Information
													</div>
													<div class="card-body">
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">SSS:</label>
															<div class="col-lg-9">	
																<input type="text" name="sss" value="{{ old('sss', $employee->sss) }}" class="@error('sss') is-invalid @enderror form-control" placeholder="SSS" >
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">PAG-IBIG:</label>
															<div class="col-lg-9">	
																<input type="text" name="pagibig" value="{{ old('pagibig', $employee->pagibig) }}" class="@error('pagibig') is-invalid @enderror form-control" placeholder="PAG-IBIG" >
															</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Philhealth:</label>
															
																<div class="col-lg-9">	
																<input type="text" name="philhealth" value="{{ old('philhealth', $employee->philhealth) }}" class="@error('philhealth') is-invalid @enderror form-control" placeholder="Philhealth" >
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">TIN:</label>
															<div class="col-lg-9">	
																<input type="text" name="tin" value="{{ old('tin', $employee->tin) }}" class="@error('tin') is-invalid @enderror form-control" placeholder="TIN" >
															</div>
														</div>
													</div>
												</div>

												<div class="card card-primary">
													<div class="card-header">
														Employment History
													</div>
													<div class="card-body">
														<table class="table" id="employment_history_table">
																@foreach (old('employment_histories',['']) as $index => $oldEmploymentType)
																	<tr id="employment_history{{ $index }}">
																	<td>
																		<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Title</label>
																				<div class="col-lg-9">	
																					<input type="text" name="title[]" class="form-control" value="{{ old('title.' . $index) ?? '' }}" />
																				</div>
																			</div>

																			<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Employment Type</label>
																				<div class="col-lg-9">	
																					<select name="employmentTypes[]" class="form-control">
																						<option value="">Please select</option>
																						@foreach ($employmentTypes as $employmentType)
																							<option value="{{ $employmentType->id }}"{{ $oldEmploymentType == $employmentType->id ? ' selected' : '' }}>
																								{{ $employmentType->name }}
																							</option>
																						@endforeach
																					</select>
																				</div>
																			</div>
																			
																			<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Company</label>
																				<div class="col-lg-9">	
																				<input type="text" name="company[]" class="form-control" value="{{ old('company.' . $index) ?? '' }}" />
																				</div>
																			</div>
																			<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Location</label>
																				<div class="col-lg-9">	
																				<input type="text" name="location[]" class="form-control" value="{{ old('location.' . $index) ?? '' }}" />
																				</div>
																			</div>
																			<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Start Date</label>
																				<div class="col-lg-9">	
																						<div class="input-group date" id="startdate" data-target-input="nearest">
																								<input type="text" name="startdate[]"  value="{{ old('startdate.' . $index) ?? '' }}" class="form-control datetimepicker-input" data-target="#startdate"/>
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
																				<input type="text" name="enddate[]" value="{{ old('enddate.' . $index) ?? '' }}" class="form-control datetimepicker-input" data-target="#enddate"/>
																				<div class="input-group-append" data-target="#enddate" data-toggle="datetimepicker">
																					<div class="input-group-text"><i class="fa fa-calendar"></i></div>
																				</div>
																			</div>
																				</div>
																			</div>
																			<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Nature of Work</label>
																				<div class="col-lg-9">	
																				<select name="industries[]" class="form-control">
																				<option value="">Please select</option>
																				@foreach ($industries as $industry)
																					<option value="{{ $industry->id }}"{{ $oldEmploymentType == $industry->id ? ' selected' : '' }}>
																						{{ $industry->name }}
																					</option>
																				@endforeach
																			</select>
																				</div>
																			</div>
																			<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Job Description</label>
																				<div class="col-lg-9">	
																				<textarea rows="3" cols="3" id="job_description" name="job_description[]" class="form-control" placeholder="Job Description">{{ old('description.' . $index) ?? '' }}</textarea>
																				</div>
																			</div>
																		</td>
																		<td>
																			<a id="delete_row" class="btn btn-danger">Delete</a>
																		</td>
																	</tr>
																@endforeach
																<tr id="employment_history{{ count(old('employment_histories', [''])) }}"></tr>
															</tbody>
														</table>
													

														<div class="row">
															<div class="col-md-12">
																<button id="add_row" class="btn btn-success">+ Add Row</button>
															</div>
														</div>
														<br/>
														<div class="row">
																<table class="table table-hover table-striped" id="employment_histories_list">
																	<thead>
																					<tr style="text-align:center;">
																						<th>TITLE</th>
																						<th>EMPLOYMENT TYPE</th>
																						<th>COMPANY</th>
																						<th>LOCATION</th>
																						<th>START DATE</th>
																						<th>END DATE</th>
																						<th>NATURE OF WORK</th>
																						<th>JOB DESCRIPTION</th>
																						<th>ACTION</th>
																					</tr>
																	</thead>
																	<tbody>
																					@foreach ($employment_histories as $employee)
																									<tr>
																										<td>{{ $employee->title }}</td>
																										<td>{{ $employee->employment_type->name }}</td>
																										<td>{{ $employee->company }}</td>
																										<td>{{ $employee->location }}</td>
																										<td>{{ $employee->start_date }}</td>
																										<td>{{ $employee->end_date }}</td>
																										<td>{{ $employee->industry->name }}</td>
																										<td>{{ $employee->job_description }}</td>
																										<td>
																																	
																										</td>
																									</tr>
																					@endforeach
																	</tbody>
																</table>
														</div>
													</div>
												</div>

												<div class="card card-primary">
													<div class="card-header">
													Educational Background
													</div>
													<div class="card-body">
														<table class="table" id="educ_history_table">
															<tbody>
																@foreach (old('educational_histories', ['']) as $index => $oldEmploymentType)
																	<tr id="educ_history{{ $index }}">
																	<td>
																		<div class="form-group row">
																				<label class="col-lg-3 col-form-label">School Name: </label>
																				<div class="col-lg-9">	
																					<input type="text" name="school_name[]" class="form-control" value="{{ old('school_name.' . $index) ?? '' }}" />
																				</div>
																			</div>
																	</td>
																	<td>
																			<div class="form-group row">
																				<label class="col-lg-3 col-form-label">From</label>
																				<div class="col-lg-9">	
																						<div class="input-group date" id="fromdate" data-target-input="nearest">
																								<input type="text" name="from[]" class="form-control datetimepicker-input" value="{{ old('from.' . $index) ?? '' }}" data-target="#fromdate"/>
																									<div class="input-group-append" data-target="#fromdate" data-toggle="datetimepicker">
																										<div class="input-group-text"><i class="fa fa-calendar"></i></div>
																									</div>
																					</div>
																				</div>
																			</div>
																		</td>
																		<td>
																			<div class="form-group row">
																					<label class="col-lg-3 col-form-label">To</label>
																						<div class="col-lg-9">	
																							<div class="input-group date" id="todate" data-target-input="nearest">
																							<input type="text" name="to[]" class="form-control datetimepicker-input"  value="{{ old('to.' . $index) ?? '' }}"  data-target="#todate"/>
																							<div class="input-group-append" data-target="#todate" data-toggle="datetimepicker">
																								<div class="input-group-text"><i class="fa fa-calendar"></i></div>
																							</div>
																						</div>
																				</div>
																			</div>
																		</td>
																		<td>
																			<a id="delete_row_educ" class="btn btn-danger">Delete</a>
																		</td>
																	</tr>
																@endforeach
																<tr id="educ_history{{ count(old('educational_histories', [''])) }}"></tr>
															</tbody>
														</table>

														<div class="row">
															<div class="col-md-12">
																<button id="add_row_educ" class="btn btn-success">+ Add Row</button>
															</div>
														</div>

														<div class="row">
														<div class="col-md-12">
																<table class="table table-hover table-striped" id="educational_backgrounds_list">
																	<thead>
																					<tr style="text-align:center;">
																						<th>SCHOOL NAME</th>
																						<th>FROM</th>
																						<th>TO</th>
																						<th>ACTION</th>
																					</tr>
																	</thead>
																	<tbody>
																				@foreach($educ_backgrounds as $education)
																							<tr style="text-align:center;">
																									<td>{{ $education->school_name }}</td>
																									<td>{{ $education->from }}</td>
																									<td>{{ $education->to }}</td>
																									<td>
																																
																									</td>
																								</tr> 
																				@endforeach
																	</tbody>
																</table>
																</div>
														</div>
													</div>
												</div>


													<div class="text-right">
														<button type="submit" class="btn btn-primary">SAVE <i class="icon-paperplane ml-2"></i></button>
													</div>
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
									$(document).ready(function(){
												let row_number = {{ count(old('employment_histories', [''])) }};
												$("#add_row").click(function(e){
													e.preventDefault();
													let new_row_number = row_number - 1;
												
													$('#employment_history' + row_number).html($('#employment_history' + new_row_number).html()).find('td:first-child');
													$('#employment_history_table').append('<tr id="employment_history' + (row_number + 1) + '"></tr>');
													row_number++;
												});
												$(document).on('click',"#delete_row", function(e){
													e.preventDefault();
													if(row_number > 1){	
														$(this).closest('tr').remove();
													}
												});
									});	



									$(document).ready(function(){
												let row_number = {{ count(old('educational_histories', [''])) }};
												$("#add_row_educ").click(function(e){
													e.preventDefault();
													let new_row_number = row_number - 1;
												
													$('#educ_history' + row_number).html($('#educ_history' + new_row_number).html()).find('td:first-child');
													$('#educ_history_table').append('<tr id="educ_history' + (row_number + 1) + '"></tr>');
													row_number++;
												});
												$(document).on('click',"#delete_row_educ", function(e){
													e.preventDefault();
													if(row_number > 1){	
														$(this).closest('tr').remove();
													}
												});

											
									});	


									$(function () {
											$('.select2').select2()

											CKEDITOR.replace( 'job_description', {
															filebrowserBrowseUrl: '/js/ckfinder/ckfinder.html',
															filebrowserImageBrowseUrl: '/js/ckfinder/ckfinder.html?Type=Images',
															filebrowserUploadUrl: '/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
															filebrowserImageUploadUrl: '/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
															filebrowserWindowWidth : '1000',
															filebrowserWindowHeight : '700'
											} );
        		//Date picker
										$('#startdate').datetimepicker({
											format: 'L'
										});
										$('#enddate').datetimepicker({
											format: 'L'
										});

										$('#fromdate').datetimepicker({
											format: 'L'
										});
										$('#todate').datetimepicker({
											format: 'L'
										});

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