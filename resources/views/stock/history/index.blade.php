@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Stock In History</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
			  <li class="breadcrumb-item">Stock In History</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
      	<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- About Me Box -->
					<div class="card card-primary">
						<div class="card-header">
							<h3 class="card-title">Products</h3>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<table id="stock-in-history" class="table table-hover table-bordered table-striped">
										<thead>
											<tr style="text-align:center;">
												<th>REFERENCE NO</th>
												<th>SUPPLIER</th>
												<th>PRODUCT NAME</th>
												<th>QTY</th>
												<th>RECEIVED QTY</th>
												<th>DEFECTIVE QTY</th>
												<th>EXPIRATION DATE</th>
												<th>DATE ADDED</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($deliveryRequestItems as $deliveryRequestItem)
												<tr>
													<td>{{ $deliveryRequestItem->delivery_request->reference_no }}</td>
													<td>{{ $deliveryRequestItem->delivery_request->supplier->name }}</td>
													<td>{{ $deliveryRequestItem->product->product_name }}</td>
													<td style="text-align:right;">{{ $deliveryRequestItem->qty }}</td>
													<td style="text-align:right;">{{ $deliveryRequestItem->received_qty }}</td>
													<td style="text-align:right;">{{ $deliveryRequestItem->defectived_qty }}</td>
													<td>{{ $deliveryRequestItem->expired_at }}</td>
													<td>{{ $deliveryRequestItem->created_at }}</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
			</div>
		</div>	
	</section>
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
        <!-- Vendors -->
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
            var table = $('#stock-in-history').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeStockInHistory') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
						"_token":"<?= csrf_token() ?>"
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
                                'title' :'Stock In History',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5,6,7]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' :'Stock In History',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5,6,7]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' :'Stock In History',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5,6,7]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"reference_no"},
                    {"data":"supplier"},
                    {"data":"product_name"},
                    {"data":"qty"},
					{"data":"received_qty"},
					{"data":"defective_qty"},
                    {"data":"expired_at"},
					{"data":"created_at"}
                ],
				"columnDefs": [{
					"targets": [3,4,5],   // target column
					"className": "textRight",
				}]
            });
		</script>
		<script>
			$(function () {
				$('.select2').select2()
				//Date picker
				$('#reservationdate').datetimepicker({
					format: 'L'
				});

				$('#expireddate').datetimepicker({
					format: 'L'
				});
			});
		</script>
		
        @endpush('scripts')
@endsection