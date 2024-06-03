@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Leaves - {{ ucwords($deployment->employee->name)}} - {{ ucwords($deployment->client->name)}} Company</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('deployment.index')}}">Employees</a></li>
			  									<li class="breadcrumb-item">Leave Details</li>
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
								<h3 class="card-title">Leave Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
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
							</form>
						</div>
						<div class="card-footer clearfix">
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