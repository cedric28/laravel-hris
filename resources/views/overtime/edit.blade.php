@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Overtime - {{ ucwords($deployment->employee->name)}} - {{ ucwords($deployment->client->name)}} Company</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('deployment.index')}}">Employees</a></li>
			    <li class="breadcrumb-item">Overtime Details</li>
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
							<h3 class="card-title">Overtime Form</h3>
						    <a type="button" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);" href="{{ route('attendance.edit', $deployment->id )}}" class="btn bg-gradient-success float-right"><i class="fas fa-calendar mr-2"></i> File Attendance</a>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
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
							</form>
						</div>
						<div class="card-footer clearfix">
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
                    <h4 align="center" style="margin:0;">Are you sure you want to move this data to archive?</h4>
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

        @endpush('scripts')
@endsection