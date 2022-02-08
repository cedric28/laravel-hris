@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Inventory Adjustment Logs</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Inventory Adjustment Logs</li>
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
						<table class="table table-hover table-striped" id="inventory-logs">
								<thead>
									<tr style="text-align:center;">
										<th>PRODUCT NAME</th>
                                        <th>QTY</th>
                                        <th>ADJUSTMENT TYPE</th>
                                        <th>REASON</th>
										<th>DATE ADDED</th>
									</tr>
								</thead>
								<tbody>
                                    @foreach($inventoryAdjustments as $adjustment)
                                        <tr>
											<td>{{ $adjustment->inventory->product->product_name }}</td>
											<td>{{ $adjustment->adjusted_quantity }}</td>
                                            <td>{{ $adjustment->inventory_adjustment_type->type }}</td>
                                            <td>{{ $adjustment->reason }}</td>
                                            <td>{{ $adjustment->created_at }}</td>
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

            var table = $('#inventory-logs').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('getInventoryAdjustmentProducts') ?>",
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
                                'title' :'Inventory Adjustment Logs',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' :'Inventory Adjustment Logs',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' :'Inventory Adjustment Logs',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"product_name"},
                    {"data":"adjusted_quantity"},
                    {"data":"type"},
                    {"data":"reason"},
                    {"data":"created_at"}
                ],
                "columnDefs": [{
					"targets": [1],   // target column
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