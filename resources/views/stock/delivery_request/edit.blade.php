@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Delivery Request - {{ ucwords($deliveryRequest->reference_no)}} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('delivery-request.index')}}">Delivery Request List</a></li>
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
								<h3 class="card-title">Delivery Request Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
						    <form action="{{ route('delivery-request.update', $deliveryRequest->id)}}" method="POST">
								@csrf
								@method('PATCH')
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Reference No:</label>
									<div class="col-lg-9">	
										<input type="text" name="reference_no" value="{{ old('reference_no',$deliveryRequest->reference_no) }}" class="@error('reference_no') is-invalid @enderror form-control" placeholder="Reference No" >
									</div>
								</div>

                                <div class="form-group row">
                                    <label class="col-form-label col-lg-3">Content</label>
                                    <div class="col-lg-9">
                                    <textarea rows="3" cols="3" name="content" class="@error('content') is-invalid @enderror form-control" placeholder="Content">{{ $deliveryRequest->content}}</textarea>
                                    </div>
								</div>

                                <div class="form-group row">
										<label class="col-lg-3 col-form-label">Supplier:</label>
										<div class="col-lg-9">
											<select id="role-id" name="supplier_id" class="form-control select2">
												<option value="">Select supplier</option>
												@foreach ($suppliers as $supplier)
													<option value="{{ $supplier->id }}"{{ ($supplier->id === old('supplier_id', $deliveryRequest->supplier_id)) ? ' selected' : '' }}>{{ ucwords($supplier->name) }}</option>
												@endforeach
											</select>
										</div>
								</div>


                                <!-- Date -->
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Delivery Date:</label>
									<div class="col-lg-9">	
										<div class="input-group date" id="reservationdate" data-target-input="nearest">
											<input type="text" name="delivery_at" value="{{ old('delivery_at', date('m/d/Y', strtotime($deliveryRequest->delivery_at))) }}" class="@error('received_at') is-invalid @enderror form-control datetimepicker-input" data-target="#reservationdate"/>
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
							<form action="{{ route('addDeliveryRequestItem')}}" method="POST">
								@csrf
								<div class="form-group row">
									<div>	
										<input type="hidden" name="delivery_request_id" value="{{ $deliveryRequest->id }}" class="form-control" >
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
									
								<div class="text-right">
									<button type="submit" class="btn btn-primary">Save <i class="icon-paperplane ml-2"></i></button>
								</div>
							</form>
							<br/>
							<div class="row">
								<div class="col-md-12">
									<table id="delivery-request-items" class="table table-hover table-bordered table-striped">
										<thead>
											<tr>
												<th>Reference No</th>
												<th>Product Name</th>
												<th>Qty</th>
												<th>Delivery Date</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($deliveryRequestItem as $stock)
												<tr>
													<td>{{ $stock->delivery_request->reference_no }}</td>
													<td>{{ $stock->product->product_name }}</td>
													<td>{{ $stock->qty }}</td>
													<td>{{ $stock->delivery_request->delivery_at }}</td>
													<td>
														
													</td>
												</tr>
											@endforeach
										</tbody>
										<tfoot>
											<tr>
                                                <th>Reference No</th>
												<th>Product Name</th>
												<th>Qty</th>
												<th>Delivery Date</th>
												<th>Action</th>
											</tr>
										</tfoot>
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
            CKEDITOR.replace( 'content', {
				filebrowserBrowseUrl: '/js/ckfinder/ckfinder.html',
				filebrowserImageBrowseUrl: '/js/ckfinder/ckfinder.html?Type=Images',
				filebrowserUploadUrl: '/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
				filebrowserImageUploadUrl: '/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
				filebrowserWindowWidth : '1000',
				filebrowserWindowHeight : '700'
			} );
            var table = $('#delivery-request-items').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeProductsDeliveryRequest') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
						"_token":"<?= csrf_token() ?>",
						"delivery_request_id": "<?= $deliveryRequest->id ?>"
					}
                },
                "columns":[
                    {"data":"reference_no"},
                    {"data":"product_name"},
                    {"data":"qty"},
					{"data":"delivery_at"},
                    {"data":"action","searchable":false,"orderable":false}
                ]
            });

            var product_id;
            $(document).on('click', '#delete', function(){
                product_id = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });
			
            $('#ok_button').click(function(){
				let url = window.location.protocol + "//" + window.location.host + "/delivery-request-item/destroy/"+product_id;
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