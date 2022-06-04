@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Stock - {{ ucwords($delivery->reference_no)}} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('stock.index')}}">Stock In Entry</a></li>
			  <li class="breadcrumb-item">Edit Details</li>
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
								<h3 class="card-title">Stock Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
						    <form action="{{ route('stock.update', $delivery->id)}}" method="POST">
								@csrf
								@method('PATCH')
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Reference No:</label>
									<div class="col-lg-9">	
										<input type="text" name="reference_no" value="{{ old('reference_no',$delivery->reference_no) }}" class="@error('reference_no') is-invalid @enderror form-control" placeholder="Reference No" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Vehicle:</label>
									<div class="col-lg-9">	
										<input type="text" name="vehicle" value="{{ old('vehicle',$delivery->vehicle) }}" class="@error('vehicle') is-invalid @enderror form-control" placeholder="Vehicle" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Vehicle Plate Number:</label>
									<div class="col-lg-9">	
										<input type="text" name="vehicle_plate" value="{{ old('vehicle_plate',$delivery->vehicle_plate) }}" class="@error('vehicle_plate') is-invalid @enderror form-control" placeholder="Vehicle Plate Number" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Driver Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="driver_name" value="{{ old('driver_name',$delivery->driver_name) }}" class="@error('driver_name') is-invalid @enderror form-control" placeholder="Driver Name" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Driver Phone No:</label>
									<div class="col-lg-9">	
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">+63</span>
											</div>
											<input type="text" name="contact_number" value="{{ old('contact_number',$delivery->contact_number) }}" class="@error('contact_number') is-invalid @enderror form-control" placeholder="Phone" >
										</div>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Received By:</label>
									<div class="col-lg-9">	
										<input type="text" name="received_by" value="{{ old('received_by', $delivery->received_by) }}" class="@error('received_by') is-invalid @enderror form-control" placeholder="Received By" >
									</div>
								</div>

                                <!-- Date -->
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Date Received:</label>
									<div class="col-lg-9">	
										<div class="input-group date" id="reservationdate" data-target-input="nearest">
											<input type="text" name="received_at" value="{{ old('received_at', date('m/d/Y', strtotime($delivery->received_at))) }}" class="@error('received_at') is-invalid @enderror form-control datetimepicker-input" data-target="#reservationdate"/>
											<div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fa fa-calendar"></i></div>
											</div>
										</div>
									</div>
                                </div>
									
								<div class="text-right">
									<button type="submit" class="btn btn-success">Update <i class="icon-paperplane ml-2"></i></button>
								</div>
							</form>
						</div>
						<div class="card-footer clearfix">
							
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<!-- About Me Box -->
					<div class="card card-primary">
						<div class="card-header">
							<h3 class="card-title">Add Products</h3>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('addProduct')}}" method="POST">
								@csrf
								<div class="form-group row">
									<div>	
										<input type="hidden" name="delivery_id" value="{{ $delivery->id }}" class="form-control" >
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Products:</label>
									<div class="col-lg-9 col-sm-12">
										<select id="product-id" name="product_id" class="@error('product_id') is-invalid @enderror form-control select2" style="width: 100%;">
											<option value="">Select Product</option>
											@foreach ($products as $product)
												<option value="{{ $product->id }}"{{ ($product->id === old('product_id')) ? ' selected' : '' }}>{{ ucwords($product->product_name) }}</option>
											@endforeach
										</select>
									</div>
								</div>
									
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Quantity:</label>
									<div class="col-lg-3 col-sm-3">	
										<input type="number" name="qty" value="{{ old('qty') }}" class="@error('qty') is-invalid @enderror form-control" placeholder="Quantity" >
									</div>
								</div>

								<div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Expiration Date:</label>
									<div class="col-lg-9">	
										<div class="input-group date" id="expireddate" data-target-input="nearest">
											<input type="text" name="expired_at" value="{{ old('expired_at') }}" class="@error('expired_at') is-invalid @enderror form-control datetimepicker-input" data-target="#expireddate"/>
											<div class="input-group-append" data-target="#expireddate" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fa fa-calendar"></i></div>
											</div>
										</div>
									</div>
                                </div>
									
								<div class="text-right">
									<button type="submit" class="btn btn-primary">Save <i class="icon-paperplane ml-2"></i></button>
								</div>
							</form>
							<br/>
							<div class="row">
								<div class="col-md-12">
									<table id="stock-product" class="table table-hover table-bordered table-striped">
										<thead>
											<tr>
												<th>PRODUCT NAME</th>
												<th>QUANTITY</th>
												<th>EXPIRATION DATE</th>
												<th>DATE ADDED</th>
												<th>ACTION</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($stocks as $stock)
												<tr>
													<td>{{ $stock->product->product_name }}</td>
													<td>{{ $stock->qty }}</td>
													<td class="textCenter">{{ $stock->expired_at }}</td>
													<td class="textCenter">{{ $stock->created_at }}</td>
													<td>
														
													</td>
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
			let reference_no = {!! json_encode($delivery->reference_no) !!};
            var table = $('#stock-product').DataTable({
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
                    {"data":"action","searchable":false,"orderable":false}
                ]
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