@extends('layouts.app')
<style>
.hover-image{
	cursor: pointer !important;
}

 .modal-image {
        width: 70%;
        height: auto;
        margin: 0 auto;
        display: block;
    }
    .modal-lg {
        max-width: 70%;
    }
    .modal-content {
        padding: 0;
    }
</style>
@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Employee - {{ ucwords($deployment->employee->last_name).", ".ucwords($deployment->employee->first_name)." ".ucwords($deployment->employee->middle_name)}} - {{ ucwords($deployment->client->name)}} Company</h1>
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
											<a class="nav-link {{ request()->get('parent_index') == 1 ? 'active' : '' }}" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Schedule</a>
										</li>
										<li class="nav-item">
											<a class="nav-link {{ request()->get('parent_index') == 2 ? 'active' : '' }}" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Attendance</a>
										</li>
										<li class="nav-item">
											<a class="nav-link {{ request()->get('parent_index') == 3 ? 'active' : '' }}" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Overtime</a>
										</li>
										<li class="nav-item">
											<a class="nav-link {{ request()->get('parent_index') == 4 ? 'active' : '' }}" id="custom-tabs-one-settings-tab" data-toggle="pill" href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Leaves</a>
										</li>
										<li class="nav-item">
											<a class="nav-link {{ request()->get('parent_index') == 5 ? 'active' : '' }}" id="salary-tab" data-toggle="pill" href="#salary" role="tab" aria-controls="salary" aria-selected="false">Salary</a>
										</li>
                                         <li class="nav-item">
											<a class="nav-link {{ request()->get('parent_index') == 6 ? 'active' : '' }}" data-toggle="pill" id="payslip-tab" href="#payslip" role="tab" aria-controls="payslip" aria-selected="false">Payslip</a>
										</li>
                                        <li class="nav-item">
											<a class="nav-link {{ request()->get('parent_index') == 7 ? 'active' : '' }}" data-toggle="pill" id="employee-info-tab" href="#employee-info" role="tab" aria-controls="employee-info" aria-selected="false">Employee Information</a>
										</li>
									</ul>
								</div>
								<div class="card-body">
									<div class="tab-content" id="custom-tabs-one-tabContent">
                                        <div class="tab-pane fade {{ request()->get('parent_index') == 1 ? 'active show' : '' }}" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
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
                                                    <button type="submit" class="btn btn-success" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">Save <i class="icon-paperplane ml-2"></i></button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane fade {{ request()->get('parent_index') == 2 ? 'active show' : '' }}" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
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
                                                                    <button type="submit" class="btn btn-success" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">Save <i class="icon-paperplane ml-2"></i></button>
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
                                                                                                                            <table class="table table-hover table-striped" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);"id="employee_attendances">
                                                                                                                                                <thead>
                                                                                                                                                                <tr style="text-align:center;">
                                                                                                                                                                                <th>ATTENDANCE DATE </th>
                                                                                                                                                                                <th>ATTENDANCE TIME IN</th>
                                                                                                                                                                                <th>ATTENDANCE TIME OUT</th>
                                                                                                                                                                                <th>STATUS</th>
                                                                                                                                                                                <th>ACTION</th>
                                                                                                                                                                </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>
                                                                                                                                                                @foreach ($deployment->attendances as $attendance)
                                                                                                                                                                                <tr style="text-align:center;">
                                                                                                                                                                                                <td>{{ $attendance->attendance_date }}</td>
                                                                                                                                                                                                <td>{{ $attendance->attendance_time }}</td>
                                                                                                                                                                                                <td>{{ $attendance->attendance_out }}</td>
                                                                                                                                                                                                 <td>{{ $attendance->status }}</td>
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
                                                                                                                            <table class="table table-hover table-striped" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);"id="employee_late">
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
                                        <div class="tab-pane fade {{ request()->get('parent_index') == 3 ? 'active show' : '' }}" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
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
                                                                        <button type="submit" class="btn btn-success" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">Save <i class="icon-paperplane ml-2"></i></button>
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
                                                                                                                                <table class="table table-hover table-striped" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);"id="employee_overtime">
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
                                        <div class="tab-pane fade {{ request()->get('parent_index') == 4 ? 'active show' : '' }}" id="custom-tabs-one-settings" role="tabpanel" aria-labelledby="custom-tabs-one-settings-tab">
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
                                                    <button type="submit" class="btn btn-success" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">Save <i class="icon-paperplane ml-2"></i></button>
                                                </div>
                                            </form><br/>
                                            <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-hover table-striped" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);"id="employee_leaves">
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
                                        <div class="tab-pane fade {{ request()->get('parent_index') == 5 ? 'active show' : '' }}" id="salary" role="tabpanel" aria-labelledby="salary-tab">
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
                                                                    <label class="col-lg-3 col-form-label">TAX SALARY RANGE:</label>
                                                                    <div class="col-lg-9">	
                                                                        <input type="text" name="tax_salary_range" value="{{ old('tax',$salary->tax_salary_range) }}" class="@error('tax_salary_range') is-invalid @enderror form-control" placeholder="0.00" >
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
                                                                @if (!empty($generalDeductions))
                                                                    @foreach ($generalDeductions as $deduction)
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-3 col-form-label">{{ strtoupper($deduction['name']) }}:</label>
                                                                            <div class="col-lg-9">    
                                                                                <input type="text" name="{{ $deduction['name'] }}" value="{{ $deduction['amount'] }}" class="form-control" readonly> 
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @endif

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
                                                    <button type="submit" class="btn btn-success" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">Save <i class="icon-paperplane ml-2"></i></button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane fade {{ request()->get('parent_index') == 6 ? 'active show' : '' }}" id="payslip" role="tabpanel" aria-labelledby="payslip-tab">
                                            <div class="row">
                                                <div class="col-md-6 col-lg-6">
                                                    <div class="card card-primary">
                                                        <div class="card-header">
                                                            COMPENSATION
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-bordered w-100">
                                                                <tr>
                                                                    <th>BASIC SALARY</th>
                                                                    <td>P {{  $salary->basic_salary  }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>DE MINIMIS BENEFITS</th>
                                                                    <td>P {{ number_format($salary->meal_allowance + $salary->laundry_allowance + $salary->transportation_allowance + $salary->cola,2) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>RATE PER HOUR</th>
                                                                    <td>P {{  $salary->basic_salary / 8  }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>TOTAL HOURS WORKED</th>
                                                                    <td id="total_hours_worked"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>TOTAL HOURS OVERTIME</th>
                                                                    <td id="total_hours_overtime"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>TOTAL HOURS LATE</th>
                                                                    <td id="total_hours_late"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>TOTAL OVERTIME PAY</th>
                                                                    <td id="total_over_time_pay"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>HOLIDAY PAY</th>
                                                                    <td id="holiday_pay"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>TOTAL COMPENSATION</th>
                                                                    <td id="total_compensation"></td>
                                                                </tr>
                                                            </table>	
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-6">
                                                    <div class="card card-primary">
                                                        <div class="card-header">
                                                           DEDUCTIONS
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-bordered w-100">
                                                                <tr>
                                                                    <th>SSS</th>
                                                                    <td>P {{ $salary->sss / 2 }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>PHILHEALTH</th>
                                                                    <td>P {{ $salary->philhealth / 2 }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>HDMF</th>
                                                                    <td>P {{ $salary->pagibig / 2 }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>TAX</th>
                                                                    <td id="total_tax"></td>
                                                                </tr>
                                                                @if (!empty($generalDeductions))
                                                                    @foreach ($generalDeductions as $deduction)
                                                                        <th>{{ strtoupper($deduction['name']) }}</th>
                                                                        <td>P {{ $deduction['amount'] / 2 }}</td>
                                                                    @endforeach
                                                                @endif
                                                                <tr>
                                                                    <th>UNIFORM</th>
                                                                    <td>P {{ $salary->uniform }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>TOTAL LATE DEDUCTION</th>
                                                                    <td id="total_late_deduction"></td>
                                                                </tr>

                                                                <tr>
                                                                    <th>TOTAL DEDUCTIONS</th>
                                                                    <td id="total_deductions"></td>
                                                                </tr>
                                                            </table>	
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="row">
                                                <div class="col-md-12 col-lg-12">
                                                    <table class="table table-bordered w-100">
                                                        <tr>
                                                            <th>NET PAY</th>
                                                            <td id="net_pay"></td>
                                                        </tr>
                                                    </table>	
                                                </div>
                                            </div>
                                            <br/>
                                             <form action="{{ route('payslip.store')}}" method="POST">
                                                @csrf
                                                <input type="hidden" id="deployment_id" name="deployment_id" value="{{ $deployment->id }}"/>
                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">Payroll Cut-Off:</label>
                                                    <div class="col-lg-9">
                                                        <select id="payroll-id" name="payroll_id" class="form-control select2">
                                                            <option value="">Select cut off</option>
                                                            @foreach ($payrollCutOffs as $payrollCutOff)
                                                                <option value="{{ $payrollCutOff->id }}"{{ ($leaveType->id == old('payroll_id')) ? 'selected' : '' }}>Payroll Cut-Off {{ \Carbon\Carbon::parse($payrollCutOff->start_date)->format('F jS, Y') }} -  {{ \Carbon\Carbon::parse($payrollCutOff->end_date)->format('F jS, Y') }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">OTHER ADDITIONAL PAY:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" id="other_pay" name="other_pay" value="{{ old('other_pay') }}" class="@error('other_pay') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">OTHER DEDUCTION:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" id="other_deduction" name="other_deduction" value="{{ old('other_deduction') }}" class="@error('other_deduction') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="text-right">
                                                    <button type="submit" class="btn btn-success" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">Save <i class="icon-paperplane ml-2"></i></button>
                                                </div>
                                            </form>
                                            <br/>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="table table-hover table-striped" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);" id="employee_compensation" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);">
                                                        <thead>
                                                            <tr style="text-align:center;">
                                                                <th>DESCRIPTION</th>
                                                                <th>ACTION</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                           
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade {{ request()->get('parent_index') == 7 ? 'active show' : '' }}" id="employee-info" role="tabpanel" aria-labelledby="employee-info-tab">
                                            <div class="row">
                                                <div class="col-md-4 col-lg-4">
                                                    <div class="card card-widget widget-user-2">
                                                    <div class="widget-user-header text-white" style="background: url('../../dist/img/photo4.jpg') center center;">
                                                        <div class="widget-user-image">
                                                        <img class="img-circle elevation-2" src="{{ $deployment->employee->gender->id == 1 ? asset('dist/img/avatar5.png') : asset('dist/img/avatar3.png') }}" alt="User Avatar">
                                                        </div>
                                                        <h3 class="widget-user-username">{{ $deployment->employee->last_name }}, {{ $deployment->employee->first_name }}  {{ $deployment->employee->middle_name }}</h3>
                                                        </div>
                                                        <div class="card-footer p-0">
                                                            <table class="table table-bordered w-100">
                                                                <tr>
                                                                    <th>Employee No</th>
                                                                    <td>{{ $deployment->reference_no }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Fullname</th>
                                                                    <td>{{ $deployment->employee->last_name }}, {{ $deployment->employee->first_name }}  {{ $deployment->employee->middle_name }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Nickname</th>
                                                                    <td>{{ $deployment->employee->nickname }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Date of Birth</th>
                                                                    <td>{{ $deployment->employee->birthdate }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Age</th>
                                                                    <td>{{ $age }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Gender</th>
                                                                    <td>{{ $deployment->employee->gender->name }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Civil Status</th>
                                                                    <td>{{ $deployment->employee->civil_status->name }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Address</th>
                                                                    <td>{{ $deployment->employee->unit.' '.$deployment->employee->lot_block.' '.$deployment->employee->street.' '.$deployment->employee->subdivision.' '.$deployment->employee->barangay.' '.$deployment->employee->municipality.' '.$deployment->employee->province }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Phone</th>
                                                                    <td>+63{{ $deployment->employee->contact_number }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Email</th>
                                                                    <td>{{ $deployment->employee->email }}</td>
                                                                </tr>
                                                            </table>			
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 col-lg-8">
                                                
                                                        <div class="card card-success">
                                                            <div class="card-header">
                                                                In-Case of Emergency Information
                                                            </div>
                                                            <div class="card-body">
                                                                <table class="table table-bordered w-100">
                                                                    <tr>
                                                                        <th>Emergency Contact Name</th>
                                                                        <td>{{ $deployment->employee->emergency_contact_name ?? '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Relationship</th>
                                                                        <td>{{ $deployment->employee->emergency_relationship ?? '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Phone</th>
                                                                        <td>+63{{ $deployment->employee->emergency_contact_number ?? '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Address</th>
                                                                        <td>{{ $deployment->employee->emergency_address ?? '-' }}</td>
                                                                    </tr>
                                                                </table>	
                                                            </div>
                                                        </div>

                                                        <div class="card card-success">
                                                            <div class="card-header">
                                                            Government Information
                                                            </div>
                                                            <div class="card-body">
                                                                <table class="table table-bordered w-100">
                                                                    <tr>
                                                                        <th>SSS</th>
                                                                        <td>{{ $deployment->employee->sss ?? '-'}}</td>
                                                                        <td>
                                                                            @php
                                                                            $imageUrl = $deployment->employee->sss_file != null ? '/images/sss/'.$deployment->employee->id.'/'.$deployment->employee->sss_file : 'http://w3adda.com/wp-content/uploads/2019/09/No_Image-128.png';
                                                                            @endphp
                                                                            @if($deployment->employee->sss_file)
                                                                            <img 
                                                                                class="profile-user-img img-fluid hover-image"
                                                                                src="{{ url($imageUrl) }}"
                                                                                alt="Contract Image"
                                                                                data-toggle="modal" 
                                                                                data-target="#imageModal"
                                                                                data-image-url="{{ url($imageUrl) }}"
                                                                            >
                                                                            @else
                                                                            No Image
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>PAG-IBIG</th>
                                                                        <td>{{ $deployment->employee->pagibig ?? '-' }}</td>
                                                                        <td>
                                                                            @php
                                                                            $imageUrl = $deployment->employee->pagibig_file != null ? '/images/pagibig/'.$deployment->employee->id.'/'.$deployment->employee->pagibig_file : 'http://w3adda.com/wp-content/uploads/2019/09/No_Image-128.png';
                                                                            @endphp
                                                                            @if($deployment->employee->pagibig_file)
                                                                            <img 
                                                                                class="profile-user-img img-fluid hover-image"
                                                                                src="{{ url($imageUrl) }}"
                                                                                alt="Contract Image"
                                                                                 data-toggle="modal" 
                                                                                data-target="#imageModal"
                                                                                data-image-url="{{ url($imageUrl) }}"
                                                                            >
                                                                            @else
                                                                            No Image
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Philhealth</th>
                                                                        <td>{{ $deployment->employee->philhealth ?? '-' }}</td>
                                                                        <td>
                                                                            @php
                                                                            $imageUrl = $deployment->employee->philhealth_file != null ? '/images/philhealth/'.$deployment->employee->id.'/'.$deployment->employee->philhealth_file : 'http://w3adda.com/wp-content/uploads/2019/09/No_Image-128.png';
                                                                            @endphp
                                                                            @if($deployment->employee->philhealth_file)
                                                                            <img 
                                                                                class="profile-user-img img-fluid hover-image"
                                                                                src="{{ url($imageUrl) }}"
                                                                                alt="Contract Image"
                                                                                 data-toggle="modal" 
                                                                                data-target="#imageModal"
                                                                                data-image-url="{{ url($imageUrl) }}"
                                                                            >
                                                                            @else
                                                                            No Image
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>TIN</th>
                                                                        <td>{{ $deployment->employee->tin  ?? '-'}}</td>
                                                                        <td>
                                                                            @php
                                                                            $imageUrl = $deployment->employee->tin_file != null ? '/images/tin/'.$deployment->employee->id.'/'.$deployment->employee->tin_file : 'http://w3adda.com/wp-content/uploads/2019/09/No_Image-128.png';
                                                                            @endphp
                                                                            @if($deployment->employee->tin_file)
                                                                            <img 
                                                                                class="profile-user-img img-fluid hover-image"
                                                                                src="{{ url($imageUrl) }}"
                                                                                alt="Contract Image"
                                                                                 data-toggle="modal" 
                                                                                data-target="#imageModal"
                                                                                data-image-url="{{ url($imageUrl) }}"
                                                                            >
                                                                            @else
                                                                            No Image
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                </table>	
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
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
	<div id="confirmModalAttendance" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to move this data to archive?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" name="ok_button" id="ok_button_attendance" class="btn btn-danger">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <div id="confirmModalOvertime" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to move this data to archive?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" name="ok_button" id="ok_button_overtime" class="btn btn-danger">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <div id="confirmModalLeaves" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to move this data to archive?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" name="ok_button" id="ok_button_leaves" class="btn btn-danger">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    	<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <img src="" alt="Contract Image" class="img-fluid modal-image">
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
        		<script>
     $(document).ready(function() {
        $('.hover-image').hover(function() {
            var imageUrl = $(this).data('image-url');
            console.log('imageUrl',imageUrl.toString())
            $('#imageModal .modal-image').attr('src', imageUrl.toString());
            $('#imageModal').modal('show');
        }, function() {
            $('#imageModal').modal('hide');
        });
    });
</script>
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
                  $(`#attendance_time`).datetimepicker({
                format: 'LT'
            })

             $(`#attendance_out`).datetimepicker({
                format: 'LT'
            })
                 var todayAttendance = new Date(); 
             var maxDate = new Date("<?= $deployment->end_date ?>");

               if (maxDate < todayAttendance) {
                    maxDate = todayAttendance; // If it's less, set maxDate to today's date
                } else {
                    maxDate = maxDate;
                }

            $(`#attendance_date`).datetimepicker({
                   format: 'L',
                minDate: "<?= $deployment->start_date ?>",
                 maxDate: maxDate,
                daysOfWeekDisabled: [0, 6],
                defaultDate: maxDate
            });
			});
		</script>
			<script>
          

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
                                    "columns": [0,1,2,3]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : 'Employee Attendances-List',
                                "exportOptions": {
                                    "columns": [0,1,2,3]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : 'Employee Attendances-List',
                                "exportOptions": {
                                    "columns": [0,1,2,3]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"attendance_date"},
                    {"data":"attendance_time"},
                      {"data":"attendance_out"},
                       {"data":"status"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [{
					"targets": [0,1,2,3],   // target column
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
            $(document).on('click', '#delete_attendance', function(){
                attendance_id = $(this).attr('data-id');
                $('#confirmModalAttendance').modal('show');
            });

            $('#ok_button_attendance').click(function(){
                $.ajax({
                    url:"/attendance/destroy/"+attendance_id,
                    beforeSend:function(){
                        $('#ok_button_attendance').text('Archiving...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModalAttendance').modal('hide');
                            tableActiveAttendances.ajax.reload();
                            tableActiveLate.ajax.reload();
                            $('#ok_button_attendance').text('OK');
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

              var today = new Date(); 
             var maxDateForOver = new Date("<?= $deployment->end_date ?>");

               if (maxDateForOver < today) {
                    maxDateForOver = today; // If it's less, set maxDateForOver to today's date
                } else {
                    maxDateForOver = maxDateForOver;
                }



            $(`#overtime_date`).datetimepicker({
                format: 'L',
                minDate: "<?= $deployment->start_date ?>",
                maxDate: maxDateForOver,
                daysOfWeekDisabled: [0, 6],
                 defaultDate: maxDateForOver
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
            $(document).on('click', '#delete_overtime', function(){
                overtime_id = $(this).attr('data-id');
                $('#confirmModalOvertime').modal('show');
            });

            $('#ok_button_overtime').click(function(){
                $.ajax({
                    url:"/overtime/destroy/"+overtime_id,
                    beforeSend:function(){
                        $('#ok_button_overtime').text('Archiving...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModalOvertime').modal('hide');
                            tableActiveOverTime.ajax.reload();
                            $('#ok_button_overtime').text('OK');
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
            $(document).on('click', '#delete_leaves', function(){
                console.log
                leave_id = $(this).attr('data-id');
                $('#confirmModalLeaves').modal('show');
            });

            $('#ok_button_leaves').click(function(){
                $.ajax({
                    url:"/leaves/destroy/"+leave_id,
                    beforeSend:function(){
                        $('#ok_button_leaves').text('Archiving...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModalLeaves').modal('hide');
                            tableActiveLeaves.ajax.reload();
                            $('#ok_button_leaves').text('OK');
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
             var tableActiveCompensation = $('#employee_compensation').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activePayslip') ?>",
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
                                'title' : 'Employee Compensation-List',
                                "exportOptions": {
                                    "columns": [0]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : 'Employee Compensation-List',
                                "exportOptions": {
                                    "columns": [0]
                                }
                            },
                            {
                                "extend": 'print',
                                 'title' : 'Employee Compensation-List',
                                "exportOptions": {
                                    "columns": [0]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"description"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [{
					"targets": [0,1],   // target column
					"className": "textCenter",
				}]
            });

            
            $(document).on('click', '#generate-payslip', function(){
                let payslipId = $(this).attr('data-id');
                window.open(`/generate-payslip/${payslipId}`,'_blank');
            })

          
            </script>
            <script>

            $(document).on('click', '#edit_payslip', function(){
                let payrollId = $(this).attr('data-payrollId');
                let otherDeduction = $(this).attr('data-otherDeduction');
                let otherPay = $(this).attr('data-otherPay');
                let deploymentId = $('#deployment_id').val();
                $('#payroll-id').val(payrollId).trigger('change'); 
                $('input[name="other_deduction"]').val(otherDeduction);
                $('input[name="other_pay"]').val(otherPay);
                calculateCompensation();
            })

            $(document).ready(function() {
                // Trigger calculation when select box is changed
                $('#payroll-id').change(function() {
                    calculateCompensation();
                });

                // Trigger recalculation on input changes
                $('input[name="other_pay"], input[name="other_deduction"]').on('input', function() {
                    calculateCompensation();
                });
            });

            function calculateCompensation() {
                var deploymentId = $('#deployment_id').val();
                var payrollId = $('#payroll-id').val();
                var otherPay = $('input[name="other_pay"]').val();
                var otherDeduction = $('input[name="other_deduction"]').val();

                // Only trigger AJAX if both payrollId and deploymentId are set
                if (payrollId && deploymentId) {
                    $.ajax({
                        url: "<?= route('checkWorkDetails') ?>",
                        type: 'POST',
                        data: {
                            _token: "<?= csrf_token() ?>",
                            deployment_id: deploymentId,
                            payroll_id: payrollId,
                            other_pay: otherPay,
                            other_deduction: otherDeduction
                        },
                        success: function(response) {
                            // Update fields based on response
                            $('#total_hours_worked').text(response.totalHoursWorked);
                            $('#total_hours_overtime').text(response.totalHoursOverTime);
                            $('#total_hours_late').text(response.totalHoursLate);
                            $('#total_over_time_pay').text(response.overTimeTotal);
                            $('#total_compensation').text(response.totalCompensation);
                            $('#total_late_deduction').text(response.totalLateDeduction);
                            $('#total_deductions').text(response.totalDeduction);
                            $('#total_tax').text(response.tax);
                            $('#holiday_pay').text(response.totalHolidayPay);
                            $('#net_pay').text(response.netPay);
                        },
                        error: function(error) {
                            $('#payroll-id').val("").trigger('change'); 
                            var otherPay = $('input[name="other_pay"]').val("");
                            var otherDeduction = $('input[name="other_deduction"]').val("");
                             swal.fire({
                                title: 'Error!',
                                text: error.responseJSON?.error ?? 'An error occurred while getting payroll data.',
                                type: 'error',
                            });
                        }
                    });
                }
            }

            </script>
        @endpush('scripts')
@endsection