@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Employee - {{ ucwords($deployment->employee->name)}} - {{ ucwords($deployment->client->name)}} Company</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('deployment.index')}}">Employees</a></li>
			  <li class="breadcrumb-item">Work Details</li>
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
								<h3 class="card-title">Work Details</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<div class="card card-success card-tabs">
								<div class="card-header p-0 pt-1">
									<ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Schedule</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Attendance</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Overtime</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill" href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Leaves</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="salary-tab" data-toggle="pill" href="#salary" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Salary</a>
										</li>
									</ul>
								</div>
								<div class="card-body">
									<div class="tab-content" id="custom-tabs-one-tabContent">
													<div class="tab-pane fade active show" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
														<form action="{{ route('schedule.update', $schedule->id)}}" method="POST" >
															@csrf
															@method('PATCH')
																<input type="hidden" id="deployment_id" name="deployment_id" value="{{ $deployment->id }}"/>
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
																<button type="submit" class="btn btn-success">Save <i class="icon-paperplane ml-2"></i></button>
															</div>
														</form>
													</div>
													<div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
														<form action="{{ route('attendance.store')}}" method="POST">
															@csrf
																			<input type="hidden" id="deployment_id" name="deployment_id" value="{{ $deployment->id }}"/>
																<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Attendance Date</label>
																				<div class="col-lg-9">	
																								<div class="input-group date" id="attendance_date" data-target-input="nearest">
																												<input type="text" name="attendance_date"  value="{{ old('attendance_date') }}" class="form-control datetimepicker-input" data-target="#attendance_date"/>
																												<div class="input-group-append" data-target="#attendance_date" data-toggle="datetimepicker">
																																<div class="input-group-text"><i class="fa fa-calendar"></i></div>
																												</div>
																								</div>
																				</div>
																</div>
												
																<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Attendance Time In</label>
																				<div class="col-lg-9">	
																								<div class="input-group date" id="attendance_time" data-target-input="nearest">
																												<input type="text" name="attendance_time"  value="{{ old('attendance_time') }}" class="form-control datetimepicker-input" data-target="#attendance_time">
																												<div class="input-group-append" data-target="#attendance_time" data-toggle="datetimepicker">
																																<div class="input-group-text"><i class="far fa-clock"></i></div>
																												</div>
																								</div>
																				</div>
																</div>

																<div class="form-group row">
																				<label class="col-lg-3 col-form-label">Attendance Time Out</label>
																				<div class="col-lg-9">	
																								<div class="input-group date" id="attendance_out" data-target-input="nearest">
																												<input type="text" name="attendance_out"  value="{{ old('attendance_out') }}" class="form-control datetimepicker-input" data-target="#attendance_out">
																												<div class="input-group-append" data-target="#attendance_out" data-toggle="datetimepicker">
																																<div class="input-group-text"><i class="far fa-clock"></i></div>
																												</div>
																								</div>
																				</div>
																</div>
																<div class="text-right">
																				<button type="submit" class="btn btn-success">Save <i class="icon-paperplane ml-2"></i></button>
																</div>
														</form>
														<br/>
														<div class="row">
															<div class="col-md-12">
																			<div id="accordion">
																							<div class="card card-success">
																											<div class="card-header">
																															<h4 class="card-title w-100">
																																			<a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseAttendance" aria-expanded="true">
																																						Attendance Log
																																			</a>
																															</h4>
																											</div>
																							
																											<div id="collapseAttendance" class="collapse show" data-parent="#accordion" style="">
																															<div class="card-body">
																																		<table class="table table-hover table-striped" id="employee_attendances">
																																							<thead>
																																											<tr style="text-align:center;">
																																															<th>ATTENDANCE DATE </th>
																																															<th>ATTENDANCE TIME IN</th>
																																															<th>ATTENDANCE TIME OUT</th>
																																															<th>ACTION</th>
																																											</tr>
																																							</thead>
																																							<tbody>
																																											@foreach ($deployment->attendances as $attendance)
																																															<tr style="text-align:center;">
																																																			<td>{{ $attendance->attendance_date }}</td>
																																																			<td>{{ $attendance->attendance_time }}</td>
																																																			<td>{{ $attendance->attendance_out }}</td>
																																																			<td>
																																																																															
																																																			</td>
																																															</tr>
																																											@endforeach
																																							</tbody>
																																			</table>       
																															</div>
																											</div>
																							</div>
																									<div class="card card-warning">
																											<div class="card-header">
																															<h4 class="card-title w-100">
																																			<a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseLate" aria-expanded="true">
																																						Tardiness Log
																																			</a>
																															</h4>
																											</div>
																							
																											<div id="collapseLate" class="collapse show" data-parent="#accordion" style="">
																															<div class="card-body">
																																		<table class="table table-hover table-striped" id="employee_late">
																																							<thead>
																																											<tr style="text-align:center;">
																																															<th>DATE</th>
																																															<th>DURATION </th>
																																											</tr>
																																							</thead>
																																							<tbody>
																																											@foreach ($lates as $late)
																																															<tr style="text-align:center;">
																																																			<td>{{ $late->latetime_date }}</td>
																																																			<td>{{ $late->duration }}</td>                                    
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
													</div>
													<div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
															<form action="{{ route('overtime.store')}}" method="POST">
															@csrf
																			<input type="hidden" id="deployment_id" name="deployment_id" value="{{ $deployment->id }}"/>
																	<div class="form-group row">
																					<label class="col-lg-3 col-form-label">Date</label>
																					<div class="col-lg-9">	
																									<div class="input-group date" id="overtime_date" data-target-input="nearest">
																													<input type="text" name="overtime_date"  value="{{ old('overtime_date') }}" class="form-control datetimepicker-input" data-target="#overtime_date"/>
																													<div class="input-group-append" data-target="#overtime_date" data-toggle="datetimepicker">
																																	<div class="input-group-text"><i class="fa fa-calendar"></i></div>
																													</div>
																									</div>
																					</div>
																	</div>
													
																	<div class="form-group row">
																					<label class="col-lg-3 col-form-label">Time In</label>
																					<div class="col-lg-9">	
																									<div class="input-group date" id="overtime_in" data-target-input="nearest">
																													<input type="text" name="overtime_in"  value="{{ old('overtime_in') }}" class="form-control datetimepicker-input" data-target="#overtime_in">
																													<div class="input-group-append" data-target="#overtime_in" data-toggle="datetimepicker">
																																	<div class="input-group-text"><i class="far fa-clock"></i></div>
																													</div>
																									</div>
																					</div>
																	</div>

																	<div class="form-group row">
																					<label class="col-lg-3 col-form-label">Time Out</label>
																					<div class="col-lg-9">	
																									<div class="input-group date" id="overtime_out" data-target-input="nearest">
																													<input type="text" name="overtime_out"  value="{{ old('overtime_out') }}" class="form-control datetimepicker-input" data-target="#overtime_out">
																													<div class="input-group-append" data-target="#overtime_out" data-toggle="datetimepicker">
																																	<div class="input-group-text"><i class="far fa-clock"></i></div>
																													</div>
																									</div>
																					</div>
																	</div>
																	<div class="text-right">
																					<button type="submit" class="btn btn-success">Save <i class="icon-paperplane ml-2"></i></button>
																	</div>
															</form> <br/>
															<div class="row">
																<div class="col-md-12">
																				<div id="accordion">
																								<div class="card card-success">
																												<div class="card-header">
																																<h4 class="card-title w-100">
																																				<a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseOverTime" aria-expanded="true">
																																								Overtime Log
																																				</a>
																																</h4>
																												</div>
																								
																												<div id="collapseOverTime" class="collapse show" data-parent="#accordion" style="">
																																<div class="card-body">
																																			<table class="table table-hover table-striped" id="employee_overtime">
																																												<thead>
																																												<tr style="text-align:center;">
																																																<th>DATE</th>
																																																<th>DURATION </th>
																																																	<th>ACTION</th>
																																												</tr>
																																								</thead>
																																								<tbody>
																																												
																																																@foreach ($deployment->overtimes as $time)
																																																<tr style="text-align:center;">
																																																				<td>{{ $time->overtime_date }}</td>
																																																				<td>{{ $time->duration }}</td>
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
													</div>
													<div class="tab-pane fade" id="custom-tabs-one-settings" role="tabpanel" aria-labelledby="custom-tabs-one-settings-tab">
														<form action="{{ route('leaves.store')}}" method="POST">
								@csrf
								<input type="hidden" id="deployment_id" name="deployment_id" value="{{ $deployment->id }}"/>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Leave Type:</label>
									<div class="col-lg-9">
									<select id="leave-type-id" name="leave_type_id" class="form-control select2">
											<option value="">Select Leave Type</option>
											@foreach ($leaveTypes as $leaveType)
												<option value="{{ $leaveType->id }}"{{ ($leaveType->id == old('leave_type_id')) ? 'selected' : '' }}>{{ ucwords($leaveType->name) }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Leave Date</label>
									<div class="col-lg-9">	
											<div class="input-group date" id="startdate" data-target-input="nearest">
													<input type="text" name="leave_date"  value="{{ old('leave_date') }}" class="form-control datetimepicker-input" data-target="#startdate"/>
														<div class="input-group-append" data-target="#startdate" data-toggle="datetimepicker">
															<div class="input-group-text"><i class="fa fa-calendar"></i></div>
														</div>
										</div>
									</div>
								</div>

                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Leave Time</label>
                                        <div class="col-lg-9">	
                                                    <div class="input-group date" id="leavetime" data-target-input="nearest">
                                                    <input type="text" name="leave_time"  value="{{ old('leave_time') }}" class="form-control datetimepicker-input" data-target="#leavetime">
                                                    <div class="input-group-append" data-target="#leavetime" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="far fa-clock"></i></div>
                                                    </div>
                                        </div>
                                    </div>

								</div>

								<div class="text-right">
									<button type="submit" class="btn btn-success">Save <i class="icon-paperplane ml-2"></i></button>
								</div>
							</form><br/>
							<div class="row">
                            <div class="col-md-12">
								<table class="table table-hover table-striped" id="employee_leaves">
									<thead>
                                        <tr style="text-align:center;">
                                            <th>LEAVE TYPE</th>
                                            <th>LEAVE DATE </th>
                                            <th>LEAVE TIME</th>
                                            <th>ACTION</th>
                                        </tr>
									</thead>
									<tbody>
										@foreach ($deployment->leaves as $leave)
                                            <tr style="text-align:center;">
                                                <td>{{ $leave->leave_type->name }}</td>
                                                <td>{{ $leave->leave_date }}</td>
                                                <td>{{ $leave->leave_time }}</td>
                                                <td>
                                                                            
                                                </td>
                                            </tr>
										@endforeach
									</tbody>
								</table>
                            </div>
						</div>
													</div>
													<div class="tab-pane fade" id="salary" role="tabpanel" aria-labelledby="salary-tab">
															  <form action="{{ route('salary.update', $salary->id)}}" method="POST">
                            @csrf
                            @method('PATCH')
                                <div id="accordion">
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseSalary" aria-expanded="true">
                                                    Compensation Information
                                                </a>
                                            </h4>
                                        </div>
                                    
                                        <div id="collapseSalary" class="collapse show" data-parent="#accordion" style="">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">BASIC SALARY:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="basic_salary" value="{{ old('sss',$salary->basic_salary) }}" class="@error('basic_salary') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">BASE RATE:</label>
                                                    <div class="col-lg-9">	
                                                        <select name="rate_base" class="form-control">
                                                            <option value="">Please select</option>
                                                            @foreach ($baseRate as $base)
                                                            <option value="{{ $base['value'] }}"{{ ($salary->rate_base == old('rate_base',$base['value'])) ? ' selected' : '' }}>
                                                                    {{ $base['label'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseOne" aria-expanded="true">
                                                    Government Mandated
                                                </a>
                                            </h4>
                                        </div>
                                        
                                        <div id="collapseOne" class="collapse show" data-parent="#accordion" style="">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">SSS:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="sss" value="{{ old('sss',$salary->sss) }}" class="@error('sss') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">TAX (%):</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="tax" value="{{ old('tax',$salary->tax) }}" class="@error('tax') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">PAG-IBIG:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="pagibig" value="{{ old('pagibig',$salary->pagibig) }}" class="@error('pagibig') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">PHILHEALTH:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="philhealth" value="{{ old('philhealth',$salary->philhealth) }}" class="@error('philhealth') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                            <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseTwo" aria-expanded="true">
                                                Allowances
                                            </a>
                                            </h4>
                                        </div>
                                        <div id="collapseTwo" class="collapse show" data-parent="#accordion" style="">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">MEAL ALLOWANCE:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="meal_allowance" value="{{ old('meal_allowance',$salary->meal_allowance) }}" class="@error('meal_allowance') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">LAUNDRY ALLOWANCE:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="laundry_allowance" value="{{ old('laundry_allowance',$salary->laundry_allowance) }}" class="@error('laundry_allowance') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">TRANSPORTATION ALLOWANCE:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="transportation_allowance" value="{{ old('transportation_allowance',$salary->transportation_allowance) }}" class="@error('transportation_allowance') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">COLA:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="cola" value="{{ old('cola',$salary->cola) }}" class="@error('cola') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseThree" aria-expanded="true">
                                                    Other Deduction
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseThree" class="collapse show" data-parent="#accordion" style="">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">UNIFORM:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="uniform" value="{{ old('uniform',$salary->uniform) }}" class="@error('uniform') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							
								<div class="text-right">
									<button type="submit" class="btn btn-success">Save <i class="icon-paperplane ml-2"></i></button>
								</div>
							</form>
													</div>
										</div>
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
								   <script>
			$(function () {
				$('.select2').select2()
				//Date picker
								$('#time_in').datetimepicker({
      format: 'LT'
    })

				$('#time_out').datetimepicker({
      format: 'LT'
    })
			});
		</script>
			<script>
            $(`#attendance_time`).datetimepicker({
                format: 'LT'
            })

             $(`#attendance_out`).datetimepicker({
                format: 'LT'
            })

            $(`#attendance_date`).datetimepicker({
                format: 'L',
                minDate: "<?= $deployment->start_date ?>",
                maxDate: "<?= $deployment->end_date ?>",
                daysOfWeekDisabled: [0, 6]
            });

var tableActiveAttendances = $('#employee_attendances').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeAttendance') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
                        "_token":"<?= csrf_token() ?>",
                        "deployment_id": "<?= $deployment->id ?>"
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
                                'title' : 'Employee Attendances-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : 'Employee Attendances-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : 'Employee Attendances-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"attendance_date"},
                    {"data":"attendance_time"},
                      {"data":"attendance_out"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [{
					"targets": [0,1,2],   // target column
					"className": "textCenter",
				}]
            });
            

            var tableActiveLate = $('#employee_late').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeLate') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
                        "_token":"<?= csrf_token() ?>",
                        "deployment_id": "<?= $deployment->id ?>"
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
                                'title' : 'Employee Late Log',
                                "exportOptions": {
                                    "columns": [0,1]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : 'Employee Late Log',
                                "exportOptions": {
                                    "columns": [0,1]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' :  'Employee Late Log',
                                "exportOptions": {
                                    "columns": [0,1]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"latetime_date"},
                    {"data":"duration"}
                ],
                "columnDefs": [{
					"targets": [0,1],   // target column
					"className": "textCenter",
				}]
            });

             var attendance_id;
            $(document).on('click', '#delete', function(){
                attendance_id = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function(){
                $.ajax({
                    url:"/attendance/destroy/"+attendance_id,
                    beforeSend:function(){
                        $('#ok_button').text('Deleting...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModal').modal('hide');
                            tableActiveAttendances.ajax.reload();
                            tableActiveLate.ajax.reload();
                        }, 2000);
                    }
                })
            });
            

            
            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                $('.table:visible').each( function(e) {
                    $(this).DataTable().columns.adjust().responsive.recalc();
                });
            });



            </script>

												<script>
            $(`#overtime_in`).datetimepicker({
                format: 'LT'
            })

             $(`#overtime_out`).datetimepicker({
                format: 'LT'
            })


            $(`#overtime_date`).datetimepicker({
                format: 'L',
                minDate: "<?= $deployment->start_date ?>",
                maxDate: "<?= $deployment->end_date ?>",
                daysOfWeekDisabled: [0, 6]
            });

var tableActiveOverTime = $('#employee_overtime').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeOverTime') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
                        "_token":"<?= csrf_token() ?>",
                        "deployment_id": "<?= $deployment->id ?>"
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
                                'title' : 'Employee OverTime-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : 'Employee OverTime-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : 'Employee OverTime-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"overtime_date"},
                    {"data":"duration"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [{
					"targets": [0,1,2],   // target column
					"className": "textCenter",
				}]
            });
            

        

             var overtime_id;
            $(document).on('click', '#delete', function(){
                overtime_id = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function(){
                $.ajax({
                    url:"/overtime/destroy/"+overtime_id,
                    beforeSend:function(){
                        $('#ok_button').text('Deleting...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModal').modal('hide');
                            tableActiveOverTime.ajax.reload();
                        }, 2000);
                    }
                })
            });
            

            
            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                $('.table:visible').each( function(e) {
                    $(this).DataTable().columns.adjust().responsive.recalc();
                });
            });



            </script>
													<script>
              $('#leavetime').datetimepicker({
                        format: 'LT'
                })

                $('#startdate').datetimepicker({
                format: 'L',
                minDate: "<?= $deployment->start_date ?>",
                maxDate: "<?= $deployment->end_date ?>",
                daysOfWeekDisabled: [0, 6]
            });

var tableActiveLeaves = $('#employee_leaves').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeLeaves') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
                        "_token":"<?= csrf_token() ?>",
                        "deployment_id": "<?= $deployment->id ?>"
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
                                'title' : 'Employee Leaves-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : 'Employee Leaves-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : 'Employee Leaves-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"leave_type"},
                    {"data":"leave_date"},
                    {"data":"leave_time"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [{
					"targets": [1,2],   // target column
					"className": "textCenter",
				}]
            });
            
   
            var leave_id;
            $(document).on('click', '#delete', function(){
                console.log
                leave_id = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function(){
                $.ajax({
                    url:"/leaves/destroy/"+leave_id,
                    beforeSend:function(){
                        $('#ok_button').text('Deleting...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModal').modal('hide');
                            tableActiveLeaves.ajax.reload();
                        }, 2000);
                    }
                })
            });

            
            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                $('.table:visible').each( function(e) {
                    $(this).DataTable().columns.adjust().responsive.recalc();
                });
            });
            </script>
        @endpush('scripts')
@endsection