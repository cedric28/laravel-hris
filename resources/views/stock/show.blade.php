@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Stock In - {{ ucwords($delivery->reference_no) }} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('stock.index')}}">Stock In</a></li>
			  <li class="breadcrumb-item">{{ ucwords($delivery->reference_no) }} Details</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
      	<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="card card-primary card-outline">
						<div class="card-body">
							<table class="table table-bordered">
								<tr>
									<th>Reference No</th>
									<td>{{ $delivery->reference_no }}</td>
								</tr>
								<tr>
									<th>Vehicle</th>
									<td>{{ $delivery->vehicle }}</td>
								</tr>
								<tr>
									<th>Vehicle Plate Number</th>
									<td>{{ $delivery->vehicle_plate }}</td>
								</tr>
								<tr>
									<th>Driver's Name</th>
									<td>{{ $delivery->driver_name }}</td>
								</tr>
								<tr>
									<th>Driver's Phone Number</th>
									<td>+63{{ $delivery->contact_number }}</td>
								</tr>
								<tr>
									<th>Received By</th>
									<td>{{ $delivery->received_by }}</td>
								</tr>
								<tr>
									<th>Date Received</th>
									<td>{{ $delivery->received_at }}</td>
								</tr>
							</table>
						</div>
					<!-- /.card-body -->
					</div>
				<!-- /.card -->
				</div>
			</div>
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
									<table id="example1" class="table table-hover table-bordered table-striped">
										<thead>
											<tr>
												<th>PRODUCT NAME</th>
												<th>QTY</th>
												<th>EXPIRATION DATE</th>
												<th>DATE ADDED</th>
												
											</tr>
										</thead>
										<tbody>
											@foreach ($stocks as $stock)
												<tr>
													<td>{{ $stock->product->product_name }}</td>
													<td>{{ $stock->qty }}</td>
													<td>{{ $stock->expired_at }}</td>
													<td>{{ $stock->created_at }}</td>
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
			let reference_no = {!! json_encode($delivery->reference_no) !!};
            var table = $('#example1').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeProductsDelivery') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
						"_token":"<?= csrf_token() ?>",
						"delivery_id": "<?= $delivery->id ?>"
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
                                'title' :`Stock In Entry Products Ref No#${reference_no}`,
                                "exportOptions": {
                                    "columns": [0,1,2,3]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : `Stock In Entry Products Ref No#${reference_no}`,
                                "exportOptions": {
                                    "columns": [0,1,2,3]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : `Stock In Entry Products Ref No#${reference_no}`,
                                "exportOptions": {
                                    "columns": [0,1,2,3]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"product_name"},
                    {"data":"qty"},
                    {"data":"expired_at"},
					{"data":"received_at"},
                ],
				"columnDefs": [{
					"targets": [2,3],   // target column
					"className": "textCenter",
				}]
            });

			// $(document).on('click', '#show', function(){
            //     var productId = $(this).attr('data-id');
            //     window.location.href = 'stock/'+productId;
            // });

            // $(document).on('click', '#edit', function(){
            //     var id = $(this).attr('data-id');
            //     window.location.href = 'stock/'+id+'/edit';
            // });

            var product_id;
            $(document).on('click', '#delete', function(){
                product_id = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });
			
            $('#ok_button').click(function(){
				let url = window.location.protocol + "//" + window.location.host + "/stock/destroy/"+product_id;
                $.ajax({
                    url: url,
                    beforeSend:function(){
                        $('#ok_button').text('Deleting...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModal').modal('hide');
							table.ajax.reload();
                            $('#ok_button').text('OK');
                        }, 2000);
						
                    }
                })
            });
		</script>
        @endpush('scripts')
@endsection