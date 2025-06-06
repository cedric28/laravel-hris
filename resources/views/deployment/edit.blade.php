@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Employee - {{ ucwords($deployment->employee->last_name).", ".ucwords($deployment->employee->first_name)." ".ucwords($deployment->employee->middle_name) }} - {{ ucwords($deployment->client->name)}} Company</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('deployment.index')}}">Employees</a></li>
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
								<h3 class="card-title">Employee Deployment Information Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('deployment.update', $deployment->id)}}" method="POST" >
								@csrf
								@method('PATCH')
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Client:</label>
									<div class="col-lg-9">
										<select id="client-id" name="client_id" class="form-control select2">
											<option value="">Select Client</option>
											@foreach ($clients as $client)
												<option value="{{ $client->id }}"{{ ($client->id == old('client_id', $deployment->client_id)) ? 'selected' : '' }}>{{ ucwords($client->name) }}</option>
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
													<option value="{{ $employmentType->id }}"{{ ($employmentType->id == old('employment_type_id',$deployment->employment_type_id)) ? ' selected' : '' }}>
															{{ $employmentType->name }}
														</option>
													@endforeach
												</select>
											</div>
										</div>

												<div class="form-group row">
											<label class="col-lg-3 col-form-label">Employment Status</label>
											<div class="col-lg-9">	
												<select name="status" class="form-control">
													<option value="">Please select</option>
													@foreach ($statuses as $status)
													<option value="{{ $status['name'] }}"{{ ($status['name']  == old('status',$deployment->status)) ? ' selected' : '' }}>
															{{ ucwords($status['name']) }}
														</option>
													@endforeach
												</select>
											</div>
										</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Position:</label>
									<div class="col-lg-9">	
										<input type="text" name="position" value="{{ old('position',$deployment->position) }}" class="@error('position') is-invalid @enderror form-control" placeholder="Position" >
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

								<div class="text-right">
									<button type="submit" class="btn btn-success" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">Save <i class="icon-paperplane ml-2"></i></button>
								</div>
							</form>
							
						</div>
						<div class="card-footer clearfix">
							
						</div>
					</div>
				</div>
					<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<div class="row">
								<h3 class="card-title">Employee Personal Information Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
											<form action="{{ route('employee.update', $employee->id)}}" method="POST" enctype="multipart/form-data">
												@csrf
												@method('PATCH')
												<div class="card card-success card-tabs">
													<div class="card-header p-0 pt-1">
														<ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
															<li class="nav-item">
																<a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="false">Basic Information</a>
															</li>
															<li class="nav-item">
																<a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Family / Emergency Contact</a>
															</li>
															<li class="nav-item">
																<a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="true">Government Information</a>
															</li>
															<li class="nav-item">
																<a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill" href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Employment History</a>
															</li>
															<li class="nav-item">
																<a class="nav-link" id="custom-tabs-educational-background-tab" data-toggle="pill" href="#custom-tabs-educational-background" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Educational Background</a>
															</li>
														</ul>
													</div>
													<div class="card-body">
														<div class="tab-content" id="custom-tabs-one-tabContent">
																<div class="tab-pane fade active show" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
										
																			<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Applicant No:</label>
																				<div class="col-lg-9">	
																					<input type="text" disabled="disabled" name="reference_no" value="{{ old('reference_no', $employee->reference_no) }}" class="@error('reference_no') is-invalid @enderror form-control" placeholder="Reference No" >
																				</div>
																			</div>
																						<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Firstname:</label>
																				<div class="col-lg-9">	
																					<input type="text" name="first_name" value="{{ old('first_name',$employee->first_name) }}" class="@error('first_name') is-invalid @enderror form-control" placeholder="e.g Juan" >
																				</div>
																			</div>
																			<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Middlename:</label>
																				<div class="col-lg-9">	
																					<input type="text" name="middle_name" value="{{ old('middle_name',$employee->middle_name) }}" class="@error('middle_name') is-invalid @enderror form-control" placeholder="e.g Santiago" >
																				</div>
																			</div>
																				<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Lastname:</label>
																				<div class="col-lg-9">	
																					<input type="text" name="last_name" value="{{ old('last_name',$employee->last_name) }}" class="@error('last_name') is-invalid @enderror form-control" placeholder="e.g Dela Cruz" >
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
																				<label class="col-lg-3 col-form-label">Sex:</label>
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
																				<label class="col-lg-3 col-form-label">Nationality:</label>
																				<div class="col-lg-9">	
																					<input type="text" name="nationality" value="{{ old('nationality', $employee->nationality) }}" class="@error('nationality') is-invalid @enderror form-control" placeholder="e.g Filipino" >
																				</div>
																			</div>
																			<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Religion:</label>
																				<div class="col-lg-9">	
																					<input type="text" name="religion" value="{{ old('religion', $employee->religion) }}" class="@error('religion') is-invalid @enderror form-control" placeholder="e.g Catholic" >
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
																								<div class="form-group row">
																					<label class="col-lg-3 col-form-label">Present Address:</label>
																			</div>
																			<div class="form-group row">
																					<label class="col-lg-3 col-form-label">Unit/Room No./Floor Building Name:</label>
																					<div class="col-lg-9">	
																						<input type="text" name="unit" value="{{ old('unit', $employee->unit) }}" class="@error('unit') is-invalid @enderror form-control" placeholder="e.g 3" >
																					</div>
																			</div>
																			<div class="form-group row">
																					<label class="col-lg-3 col-form-label">Lot/Block/House No.:</label>
																					<div class="col-lg-9">	
																						<input type="text" name="lot_block" value="{{ old('lot_block', $employee->lot_block) }}" class="@error('lot_block') is-invalid @enderror form-control" placeholder="e.g Lot 7 blk 2 293" >
																					</div>
																			</div>
																			<div class="form-group row">
																					<label class="col-lg-3 col-form-label">Street:</label>
																					<div class="col-lg-9">	
																						<input type="text" name="street" value="{{ old('street', $employee->street) }}" class="@error('street') is-invalid @enderror form-control" placeholder="e.g Camachile" >
																					</div>
																			</div>
																			<div class="form-group row">
																					<label class="col-lg-3 col-form-label">Subdivision/Village:</label>
																					<div class="col-lg-9">	
																						<input type="text" name="subdivision" value="{{ old('subdivision', $employee->subdivision) }}" class="@error('subdivision') is-invalid @enderror form-control" placeholder="e.g Almara" >
																					</div>
																			</div>
																			<div class="form-group row">
																					<label class="col-lg-3 col-form-label">City/Municipality:</label>
																					<div class="col-lg-9">	
																						<input type="text" name="municipality" value="{{ old('municipality', $employee->municipality) }}" class="@error('municipality') is-invalid @enderror form-control" placeholder="e.g Balanga" >
																					</div>
																			</div>
																			<div class="form-group row">
																					<label class="col-lg-3 col-form-label">Barangay:</label>
																					<div class="col-lg-9">	
																						<input type="text" name="barangay" value="{{ old('barangay', $employee->barangay) }}" class="@error('barangay') is-invalid @enderror form-control" placeholder="e.g San Jose" >
																					</div>
																			</div>
																			<div class="form-group row">
																					<label class="col-lg-3 col-form-label">Province:</label>
																					<div class="col-lg-9">	
																						<input type="text" name="province" value="{{ old('province', $employee->province) }}" class="@error('province') is-invalid @enderror form-control" placeholder="e.g Bataan" >
																					</div>
																			</div>
																			<div class="form-group row">
																					<label class="col-lg-3 col-form-label">Zip code:</label>
																					<div class="col-lg-9">	
																						<input type="text" name="zip" value="{{ old('zip', $employee->zip) }}" class="@error('zip') is-invalid @enderror form-control" placeholder="e.g 2100" >
																					</div>
																			</div>
													
																</div>
																<div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
																
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
																<div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
																		<div class="form-group row">
																			<label class="col-lg-3 col-form-label">SSS:</label>
																			<div class="col-lg-5">	
																				<input type="text" name="sss" value="{{ old('sss',$employee->sss) }}" class="@error('sss') is-invalid @enderror form-control" placeholder="SSS" >
																			</div>
																			<div class="col-lg-4">	
																					<input type="file" id="sss_file" name="sss_file"value="{{ old('sss_file') }}" class="@error('sss_file') is-invalid @enderror form-control" placeholder="SSS File" >
																				</div>
																		</div>
																		<div class="form-group row">
																			<label class="col-lg-3 col-form-label">PAG-IBIG:</label>
																			<div class="col-lg-5">	
																				<input type="text" name="pagibig" value="{{ old('pagibig', $employee->pagibig) }}" class="@error('pagibig') is-invalid @enderror form-control" placeholder="PAG-IBIG" >
																			</div>
																				<div class="col-lg-4">	
																					<input type="file" id="pagibig_file" name="pagibig_file"value="{{ old('pagibig_file') }}" class="@error('pagibig_file') is-invalid @enderror form-control" placeholder="Pagibig File" >
																				</div>
																		</div>
																		<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Philhealth:</label>
																			
																				<div class="col-lg-5">	
																				<input type="text" name="philhealth" value="{{ old('philhealth', $employee->philhealth) }}" class="@error('philhealth') is-invalid @enderror form-control" placeholder="Philhealth" >
																			</div>
																				<div class="col-lg-4">	
																					<input type="file" id="philhealth_file" name="philhealth_file"value="{{ old('philhealth_file') }}" class="@error('philhealth_file') is-invalid @enderror form-control" placeholder="Philhealth File" >
																				</div>
																		</div>
																		<div class="form-group row">
																			<label class="col-lg-3 col-form-label">TIN:</label>
																			<div class="col-lg-5">	
																				<input type="text" name="tin" value="{{ old('tin', $employee->tin) }}" class="@error('tin') is-invalid @enderror form-control" placeholder="TIN" >
																			</div>
																				<div class="col-lg-4">	
																					<input type="file" id="tin_file" name="tin_file"value="{{ old('tin_file') }}" class="@error('tin_file') is-invalid @enderror form-control" placeholder="TIN File" >
																				</div>
																		</div>
																		<div class="form-group row">
																			<label class="col-lg-3 col-form-label">NBI:</label>
																			<div class="col-lg-5">	
																				<input type="text" name="nbi" value="{{ old('nbi', $employee->nbi) }}" class="@error('nbi') is-invalid @enderror form-control" placeholder="NBI No." >
																			</div>
																				<div class="col-lg-4">	
																					<input type="file" id="nbi_file" name="nbi_file"value="{{ old('nbi_file') }}" class="@error('nbi_file') is-invalid @enderror form-control" placeholder="NBI File" >
																				</div>
																		</div>
										
																</div>
																<div class="tab-pane fade" id="custom-tabs-one-settings" role="tabpanel" aria-labelledby="custom-tabs-one-settings-tab">
																	<table class="table" id="employment_history_table">
																			@foreach (old('employment_histories',['']) as $index => $oldEmploymentType)
																				<tr id="employment_history{{ $index }}">
																				<td>
																					<div class="form-group row">
																							<label class="col-lg-3 col-form-label">Job Title</label>
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
																								<input type="date" name="startdate[]"  value="{{ old('startdate.' . $index) ?? '' }}" class="form-control"/>
																							</div>
																						</div>
																						<div class="form-group row">
																							<label class="col-lg-3 col-form-label">End Date</label>
																							<div class="col-lg-9">	
																							<input type="date" name="enddate[]" value="{{ old('enddate.' . $index) ?? '' }}" class="form-control"/>
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
																							<textarea rows="3" cols="3" id="job_description{{ $index }}" name="job_description[]" class="form-control" placeholder="Job Description">{{ old('description.' . $index) ?? '' }}</textarea>
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
																			<table class="table table-hover table-striped w-100" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);"id="employment_histories_list">
																				<thead>
																								<tr style="text-align:center;">
																									<th>JOB TITLE</th>
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
																												<tr style="text-align:center;">
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
																<div class="tab-pane fade" id="custom-tabs-educational-background" role="tabpanel" aria-labelledby="custom-tabs-educational-background-tab">					
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
																							<label class="col-lg-3 col-form-label">Date Graduated</label>
																							<div class="col-lg-9">	
																								
																											<input type="date" name="date_graduated[]" class="form-control" value="{{ old('date_graduated.' . $index) ?? '' }}"/>
																											
																							</div>
																						</div>
																					</td>
																					<td>
																						<div class="form-group row">
																							<label class="col-lg-3 col-form-label">Education Level</label>
																							<div class="col-lg-9">	
																								<select name="level[]" class="form-control">
																									<option value="">Please select</option>
																									@foreach ($educationLevel as $level)
																										<option value="{{ $level['name'] }}" {{ $level['name'] == old('level')  ? ' selected' : '' }}>
																											{{ $level['name'] }}
																										</option>
																									@endforeach
																								</select>
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
																	<br/>
																	<div class="row">
																		<div class="col-md-12">
																				<table class="table table-hover table-striped" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);"id="educational_backgrounds_list">
																					<thead>
																									<tr style="text-align:center;">
																										<th>SCHOOL NAME</th>
																										<th>DATE GRADUATED</th>
																										<th>EDUCATION LEVEL</th>
																										<th>ACTION</th>
																									</tr>
																					</thead>
																					<tbody>
																								@foreach($educ_backgrounds as $education)
																											<tr style="text-align:center;">
																													<td>{{ $education->school_name }}</td>
																													<td>{{ $education->date_graduated }}</td>
																													<td>{{ $education->level }}</td>
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
													</div>
												</div>
						
								
								

			
										


													<div class="text-right">
														<button type="submit" class="btn btn-success" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">SAVE <i class="icon-paperplane ml-2"></i></button>
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
	<div id="confirmModalHistory" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to move this data to archive?</h4>
                </div>
                <div class="modal-footer">
                 <button type="button" name="ok_button_emp_history" id="ok_button_emp_history" class="btn btn-danger">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

				<div id="confirmModalEducation" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to move this data to archive?</h4>
                </div>
                <div class="modal-footer">
                 <button type="button" name="ok_button_emp_education" id="ok_button_emp_education" class="btn btn-danger">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
	<!-- /page content -->
        @push('scripts')
        <!-- Javascript -->
        <!-- Vendors -->	
								<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
		<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
		<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
		<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
		<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
      
        <script src="{{ asset('vendors/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/jquery.scrollbar/jquery.scrollbar.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/jquery-scrollLock/jquery-scrollLock.min.js') }}"></script>
								   <script>
			$(function () {
				$('.select2').select2()
				//Date picker
				$('#startdate').datetimepicker({
						format: 'L',
						defaultDate: "<?= $deployment->start_date ?>",
					 daysOfWeekDisabled: [0, 6]
				});

				$('#enddate').datetimepicker({
						format: 'L',
						defaultDate:  "<?= $deployment->end_date ?>",
					 daysOfWeekDisabled: [0, 6]
				});
			});
		</script>
			<script>
			   var table = $('#employment_histories_list').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeEmployeeHistories') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
																					"_token":"<?= csrf_token() ?>",
																					"employee_id" : "<?= $deployment->employee_id ?>"
																					}
                },
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        "extend": 'collection',
                        "text": 'Export',
                        "buttons": [
                            {
                                "extend": 'csv',
                                'title' : 'Employment-Histories-List',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5,6]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : 'Employment-Histories-List',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5,6]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : 'Employment-Histories-List',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5,6]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"title"},
                    {"data":"employment_type"},
                    {"data":"company"},
                    {"data":"location"},
                    {"data":"start_date"},
                    {"data":"end_date"},
                    {"data":"industry"},
																				{"data":"job_description"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [
				{
					"targets": [0,1,2,3,4,5,6],   // target column
					"className": "textCenter",
				}]
            });


												 var employee_id;
            $(document).on('click', '#delete_emp_histories', function(e){
															e.preventDefault();
                employee_id = $(this).attr('data-id');
                $('#confirmModalHistory').modal('show');
            });

            $('#ok_button_emp_history').click(function(e){
															e.preventDefault();
                $.ajax({
                    url:"/employee-history/destroy/"+employee_id,
                    beforeSend:function(){
                        $('#ok_button_emp_history').text('Archiving...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModalHistory').modal('hide');
																												table.ajax.reload();
                            $('#ok_button_emp_history').text('OK');
                        }, 2000);
                    }
                })
            });

													var tableEducation = $('#educational_backgrounds_list').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeEmployeeEducations') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
																					"_token":"<?= csrf_token() ?>",
																					"employee_id" : "<?= $deployment->employee_id ?>"
																					}
                },
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        "extend": 'collection',
                        "text": 'Export',
                        "buttons": [
                            {
                                "extend": 'csv',
                                'title' : 'Employment-Educational-Background-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'pdf',
                                 'title' : 'Employment-Educational-Background-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : 'Employment-Educational-Background-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"school_name"},
                    {"data":"date_graduated"},
                    {"data":"level"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [
				{
					"targets": [0,1,2],   // target column
					"className": "textCenter",
				}]
            });


												 var educationId;
            $(document).on('click', '#delete_emp_education', function(e){
															e.preventDefault();
                educationId = $(this).attr('data-id');
                $('#confirmModalEducation').modal('show');
            });

            $('#ok_button_emp_education').click(function(e){
															e.preventDefault();
                $.ajax({
                    url:"/employee-education/destroy/"+educationId,
                    beforeSend:function(){
                        $('#ok_button_emp_education').text('Archiving...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModalEducation').modal('hide');
																												tableEducation.ajax.reload();
                            $('#ok_button_emp_education').text('OK');
                        }, 2000);
                    }
                })
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

	</script>
        @endpush('scripts')
@endsection