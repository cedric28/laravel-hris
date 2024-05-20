@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Perfect Attendance</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Perfect Attendance</li>
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
                  <div class="row">
                  <h3 class="card-title">For the Month of <strong>{{ $currentMonth }} {{  $year }} </strong></h3>
                  </div>
              </div>
						<!-- /.card-header -->
						<div class="card-body">
              <div class="card card-primary card-outline card-outline-tabs">
                  <div class="card-header p-0 border-bottom-0">
                      
                  </div>
                  <div class="card-body">
                      <table class="table table-hover table-striped" id="perfect_attendance">
                          <thead>
                              <tr style="text-align:center;">
                                  <th>FULLNAME</th>
                                  <th>COMPANY</th>
                                  <th>ACTION</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                              </tr>
                            
                          </tbody>
                      </table>
                  </div>
              </div>
						</div>
						<!-- /.card-body -->
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
		<!-- DataTables  & Plugins -->
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
       var table = $('#perfect_attendance').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activePerfectAttendance') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{"_token":"<?= csrf_token() ?>"}
                },
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        "extend": 'collection',
                        "text": 'Export',
                        "buttons": [
                            {
                                "extend": 'csv',
                                'title' :  'Employees-List Perfect Attendance',
                                "exportOptions": {
                                    "columns": [0,1]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' :  'Employees-List Perfect Attendance',
                                "exportOptions": {
                                    "columns": [0,1]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : 'Employees-List Perfect Attendance',
                                "exportOptions": {
                                    "columns": [0,1]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"fullname"},
                    {"data":"company"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [{
					"targets": [1],   // target column
					"className": "textCenter",
				}]
      });


      	$(document).on('click', '#generate_pdf', function(){
          var deploymentId = $(this).attr('data-id');
          window.location.href = 'generate-pdf-perfect-attendance/'+deploymentId;
        });
          

		</script>
        @endpush('scripts')
@endsection