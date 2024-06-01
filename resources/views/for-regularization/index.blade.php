@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Employee for Regularization</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Employee for Regularization</li>
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
              <h3 class="card-title">Employee for Regularization {{  $year }} </strong></h3>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
                            <div class="card card-success card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Active For Regularization</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                                            <table class="table table-hover table-striped" id="for-regularization">
                                                <thead>
                                                    <tr style="text-align:center;">
                                                        <th>EMPLOYEE NAME</th>
                                                        <th>COMPANY</th>
                                                        <th>RATE</th>
                                                        <th>ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                  
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>
                                                                
                                                            </td>
                                                        </tr>
                                                 
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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

            var table = $('#for-regularization').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeForRegularization') ?>",
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
                                'title' : 'Employee For Regularization-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'pdf',
                               'title' : 'Employee For Regularization-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'print',
                               'title' : 'Employee For Regularization-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"fullname"},
                    {"data":"client_name"},
                    {"data":"rate"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [{
					"targets": [0,1,2],   // target column
					"className": "textCenter",
				}]
            });

			$(document).on('click', '#generate_pdf', function(){
                var feedbackId = $(this).attr('data-id');
                  window.open('generate-certificate-regularization/'+feedbackId,'_blank');
            })

            
            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                $('.table:visible').each( function(e) {
                    $(this).DataTable().columns.adjust().responsive.recalc();
                });
            });
		</script>
        @endpush('scripts')
@endsection