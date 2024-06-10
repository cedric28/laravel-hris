@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Bulk Attendance Record</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('deployment.index')}}">Employees</a></li>
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
								<h3 class="card-title">Bulk Attendance Form <a class="ml-2 btn btn-primary" 
style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);" href="{{ route('download') }}" target="_blank"> <i class="nav-icon fas fa-file-excel"></i> Download Attendance Template</a></h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('bulkAttendance')}}" method="POST"  enctype="multipart/form-data">
								@csrf
		      <div class="form-group row">
									<div class="col-lg-4">	
									  <label class="col-form-label">Company:</label>
										<select id="client-id" name="client_id" class="form-control select2">
											<option value="">Select Client</option>
											@foreach ($clients as $client)
												<option value="{{ $client->id }}"{{ ($client->id == old('client_id')) ? 'selected' : '' }}>{{ ucwords($client->name) }}</option>
											@endforeach
										</select>
									</div>
         <div class="col-lg-4">	
									  <label class="col-form-label">File Attendance:</label>
          <input type="file" name="excel_file" value="{{ old('excel_file') }}" class="@error('excel_file') is-invalid @enderror form-control" placeholder="e.g Excel only" >
         </div>
							
         <div class="col-lg-4">	
           <button type="submit" class="btn btn-success " style="margin-top: 35px;box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);" 
style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">Save <i class="icon-paperplane ml-2"></i></button>
          </div>
        </div>				
							</form>
							<br/>
							 <div class="card card-info">
												<div class="card-header">
																<h4 class="card-title w-100">
																				<a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseLate" aria-expanded="true">
																							Attendance Log
																				</a>
																</h4>
												</div>
								
												<div id="collapseLate" class="collapse show" data-parent="#accordion" style="">
																<div class="card-body">
																			<table class="table table-hover table-striped" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);"id="attendance_log">
																								<thead>
																												<tr style="text-align:center;">
																																<th>COMPANY</th>
																																<th>EMPLOYEE FULLNAME </th>
																																<th>ATTENDANCE </th>
																																<th>STATUS </th>
																												</tr>
																								</thead>
																								<tbody>
																											
																																<tr style="text-align:center;">
																																				<td></td>
																																				<td></td>                                    
																																					<td></td>
																																				<td></td>  
																																</tr>
																								
																								</tbody>
																				</table>       
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
	    var tableActiveAttendanceLog = $('#attendance_log').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeAttendanceBulkUpload') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
                        "_token":"<?= csrf_token() ?>",
                        "client_id": "<?= $clientId ?>"
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
                                 'title' : 'Attendance Log',
                                "exportOptions": {
                                   "columns": [0,1,2,3]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : 'Attendance Log',
                                "exportOptions": {
                                    "columns": [0,1,2,3]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : 'Attendance Log',
                                "exportOptions": {
                                    "columns": [0,1,2,3]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"company"},
                    {"data":"fullname"},
																				{"data":"attendance_date"},
																				{"data":"status"}
                ],
                "columnDefs": [{
					"targets": [0,1,2,3],   // target column
					"className": "textCenter",
				}]
            });

												  $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                $('.table:visible').each( function(e) {
                    $(this).DataTable().columns.adjust().responsive.recalc();
                });
            });

    </script>
    @endpush('scripts')
@endsection