@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Attendance - {{ ucwords($deployment->employee->name)}} - {{ ucwords($deployment->client->name)}} Company</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('deployment.index')}}">Employees</a></li>
			    <li class="breadcrumb-item">Attendance Details</li>
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
                            <h3 class="card-title">Attendance Form</h3>
                            <a type="button" href="{{ route('overtime.edit', $deployment->id )}}" class="btn bg-gradient-success float-right"><i class="fas fa-clock mr-2"></i> File Overtime</a>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
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
                                        <button type="submit" class="btn btn-primary">Save <i class="icon-paperplane ml-2"></i></button>
                                    </div>
							</form>
						</div>
						<div class="card-footer clearfix">
						<div class="row">
                            <div class="col-md-12">
                                <div id="accordion">
                                    <div class="card card-primary">
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
					</div>
				</div>
			</div>
		</div>	
	</section>
    	<!-- /page content -->
	<div id="confirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
                </div>
                <div class="modal-footer">
                 <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
	<!-- /page content -->
        @push('scripts')
        <!-- Javascript -->

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

        @endpush('scripts')
@endsection