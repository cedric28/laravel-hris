@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Sales Logs</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Sales Logs</li>
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
							
						</div>
						<!-- /.card-header -->
						<div class="card-body">
						<table class="table table-hover table-striped" id="sales-logs">
								<thead>
									<tr style="text-align:center;">
										<th>PRODUCT NAME</th>
                                        <th>QTY</th>
                                        <th>(₱) PRICE</th>
                                        <th>(₱) TOTAL SALES PRICE</th>
										<th>TRANSACTION DATE</th>
									</tr>
								</thead>
								<tbody>
                                    @foreach($salesLogs as $logs)
                                    <tr>
                                        <td>{{ $logs->inventory->product_name }}</td>
                                        <td>{{ $logs->quantity }}</td>
                                        <td>{{ $logs->price }}</td>
                                        <td>{{ $logs->price * $logs->quantity }}</td>
                                        <td>
                                            {{ $logs->created_at }}
                                        </td>
                                    </tr>
                                    @endforeach
								</tbody>
							</table>
						</div>
						<!-- /.card-body -->
						<div class="card-footer clearfix">
							
						</div>
					</div>
				</div>
			</div>
		</div>	
	</section>
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

            var table = $('#sales-logs').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('getSalesLogs') ?>",
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
                                'title' :'Sales Logs',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' :'Sales Logs',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' :'Sales Logs',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"product_name"},
                    {"data":"quantity"},
                    {"data":"price"},
                    {"data":"total_sales_price"},
                    {"data":"created_at"}
                ],
                "columnDefs": [{
					"targets": [1,2,3],   // target column
					"className": "textRight",
				},{
					"targets": [4],   // target column
					"className": "textCenter",
				}
                ],
            });
		</script>
        @endpush('scripts')
@endsection