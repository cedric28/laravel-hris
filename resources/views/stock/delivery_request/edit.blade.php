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
										<select  id="supplier" name="supplier_id" class="form-control select2">
											<option value="">Select supplier</option>
											@foreach ($suppliers as $supplier)
												<option value="{{ $supplier->id }}"{{ ($supplier->id === old('supplier_id', $deliveryRequest->supplier_id)) ? ' selected' : '' }}>{{ ucwords($supplier->name) }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Vehicle:</label>
									<div class="col-lg-9">	
										<input type="text" id="vehicle" name="vehicle" @if($deliveryRequest->status != "completed") disabled='disabled' @endif value="{{ old('vehicle',$deliveryRequest->vehicle) }}" class="@error('vehicle') is-invalid @enderror form-control" placeholder="e.g Mitsubishi" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Vehicle Plate Number:</label>
									<div class="col-lg-9">	
										<input type="text" id="plate_number" @if($deliveryRequest->status != "completed") disabled='disabled' @endif name="vehicle_plate" value="{{ old('vehicle_plate',$deliveryRequest->vehicle_plate) }}" class="@error('vehicle_plate') is-invalid @enderror form-control" placeholder="e.g NGD2889" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Driver Name:</label>
									<div class="col-lg-9">	
										<input type="text" id="driver_name" @if($deliveryRequest->status != "completed") disabled='disabled' @endif name="driver_name" value="{{ old('driver_name',$deliveryRequest->driver_name) }}" class="@error('driver_name') is-invalid @enderror form-control" placeholder="e.g Juan Dela Cruz" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Driver Phone No:</label>
									<div class="col-lg-9">	
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">+63</span>
											</div>
											<input type="text" id="driver_phone_number" @if($deliveryRequest->status != "completed") disabled='disabled' @endif name="contact_number" value="{{ old('contact_number',$deliveryRequest->contact_number) }}" class="@error('contact_number') is-invalid @enderror form-control" placeholder="e.g 9389036501" >
										</div>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Received By:</label>
									<div class="col-lg-9">	
										<input type="text" id="received_by" @if($deliveryRequest->status != "completed") disabled='disabled' @endif  name="received_by" value="{{ old('received_by', $deliveryRequest->received_by) }}" class="@error('received_by') is-invalid @enderror form-control" placeholder="e.g Juan Dela Cruz" >
									</div>
								</div>


                                <!-- Date -->
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Date Delivered:</label>
									<div class="col-lg-9">	
										<div class="input-group date" id="reservationdate" data-target-input="nearest">
											<input type="text" name="delivery_at" value="{{ old('delivery_at', date('m/d/Y', strtotime($deliveryRequest->delivery_at))) }}" class="@error('received_at') is-invalid @enderror form-control datetimepicker-input" data-target="#reservationdate"/>
											<div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fa fa-calendar"></i></div>
											</div>
										</div>
									</div>
                                </div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Status:</label>
									<div class="col-lg-9">
										<select id="status"  @if($deliveryRequest->status == "completed") disabled='disabled' @endif name="status" class="form-control select2">
											<option value="">Select status</option>
											@foreach ($deliveryStatus as $stat)
												<option value="{{ $stat['status'] }}"{{ ($stat['status'] === old('status', $deliveryRequest['status'])) ? ' selected' : '' }}>{{ ucwords($stat['status']) }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row" id="reason_for_cancel">
									<label class="col-form-label col-lg-3">Reason for cancelling:</label>
									<div class="col-lg-9">
										<textarea rows="3" cols="3" name="reason_for_cancel" class="@error('reason_for_cancel') is-invalid @enderror form-control" placeholder="e.i double entry">{{ $deliveryRequest->reason_for_cancel}}</textarea>
									</div>
								</div>
									
								<div class="text-right">
									
									<button type="submit" @if($deliveryRequest->status == "completed" || $deliveryRequest->status == "cancel") disabled='disabled' @endif class="btn btn-success">Update <i class="icon-paperplane ml-2"></i></button>
									
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

								<div class="form-group row">
									<label class="col-form-label col-lg-3">Notes:</label>
									<div class="col-lg-9">
										<textarea rows="3" cols="3" name="note" class="@error('note') is-invalid @enderror form-control" placeholder="e.i special request"></textarea>
									</div>
								</div>
								
								<div class="text-right">
									<button type="submit" @if($deliveryRequest->status == "completed" || $deliveryRequest->status == "cancel") disabled='disabled' @endif class="btn btn-primary">Save <i class="icon-paperplane ml-2"></i></button>
								</div>
							</form>
							<br/>
							<div class="row">
								<div class="col-md-12">
									<table id="delivery-request-items" class="table table-hover table-bordered table-striped">
										<thead>
											<tr style="text-align:center;">
												<th>PRODUCT NAME</th>
												<th>QTY</th>
												<th>RECEIVED QTY</th>
												<th>DEFECTIVE QTY</th>
												<th>EXPIRATION DATE</th>
												<th>NOTE</th>
												<th>ACTION</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($deliveryRequestItem as $stock)
												<tr>
													<td>{{ $stock->product->product_name }}</td>
													<td>{{ $stock->qty }}</td>
													<td>{{ $stock->received_qty }}</td>
													<td>{{ $stock->defectived_qty }}</td>
													<td>{{ $stock->expired_at }}</td>
													<td>{{ $stock->note }}</td>
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

	<div id="editModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
			<form id="deliveryItems">
				<div class="modal-content">
					<div class="modal-header">
						<h4 align="center" class="w-100"><span id="productName"></span></h4>
						<button type="button" class="close float-right" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<h5 align="center" class="w-100"><span class="text-danger" id="generalError"></span></h5>
						<div class="form-group row">
							<label class="col-lg-3 col-form-label">Received Quantity:</label>
							<div class="col-lg-9 col-sm-9">	
								<input type="number" id="received_qty" name="received_qty" class="@error('qty') is-invalid @enderror form-control" placeholder="e.g 1" >
								<span class="text-danger" id="receivedQtyError"></span>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-lg-3 col-form-label">Defective Quantity:</label>
							<div class="col-lg-9 col-sm-9">	
								<input type="number" id="defectived_qty" name="defectived_qty" class="@error('qty') is-invalid @enderror form-control" placeholder="e.g 1" >
								<span class="text-danger" id="defectivedQtyError"></span>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-lg-3 col-form-label">Expiration Date:</label>
							<div class="col-lg-9">	
								<div class="input-group date" id="expireddate" data-target-input="nearest">
									<input type="text" id="expired_at" name="expired_at" value="{{ old('expired_at') }}" class="@error('expired_at') is-invalid @enderror form-control datetimepicker-input" data-target="#expireddate"/>
									<div class="input-group-append" data-target="#expireddate" data-toggle="datetimepicker">
										<div class="input-group-text"><i class="fa fa-calendar"></i></div>
									</div>
								</div>
								<span class="text-danger" id="expiredAtError"></span>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-form-label col-lg-3">Notes:</label>
							<div class="col-lg-9">
								<textarea rows="3" id="note" cols="3" name="note" class="@error('note') is-invalid @enderror form-control" placeholder="e.i special request"></textarea>
							</div>
						</div>
						
					</div>
					<div class="modal-footer">
					<button type="button" name="edit_button" id="edit_button" class="btn btn-danger">Save</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</form>
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
			$("#reason_for_cancel").hide();
			if($("#status").val() == "completed"){
				$('#vehicle').prop("disabled", false); 
				$('#plate_number').prop("disabled", false); 
				$('#driver_name').prop("disabled", false); 
				$('#driver_phone_number').prop("disabled", false); 
				$('#received_by').prop("disabled", false); 	
			
			}else {
				$('#vehicle').prop("disabled", true); 
				$('#plate_number').prop("disabled", true); 
				$('#driver_name').prop("disabled", true); 
				$('#driver_phone_number').prop("disabled", true); 
				$('#received_by').prop("disabled", true); 
				$("#reason_for_cancel").hide();
				if($("#status").val() == "cancel"){
					$("#reason_for_cancel").show();
				}
			}

			$('#status').on('change', function() {
				if(this.value == "completed"){
					$('#vehicle').prop("disabled", false); 
					$('#plate_number').prop("disabled", false); 
					$('#driver_name').prop("disabled", false); 
					$('#driver_phone_number').prop("disabled", false); 
					$('#received_by').prop("disabled", false); 
				} else {
					$('#vehicle').prop("disabled", true); 
					$('#plate_number').prop("disabled", true); 
					$('#driver_name').prop("disabled", true); 
					$('#driver_phone_number').prop("disabled", true); 
					$('#received_by').prop("disabled", true); 
					$("#reason_for_cancel").hide();
					if($("#status").val() == "cancel"){
						$("#reason_for_cancel").show();
					}
				}
			});

            CKEDITOR.replace( 'content', {
				filebrowserBrowseUrl: '/js/ckfinder/ckfinder.html',
				filebrowserImageBrowseUrl: '/js/ckfinder/ckfinder.html?Type=Images',
				filebrowserUploadUrl: '/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
				filebrowserImageUploadUrl: '/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
				filebrowserWindowWidth : '1000',
				filebrowserWindowHeight : '700'
			} );

			let reference_no = {!! json_encode($deliveryRequest->reference_no) !!};
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
				"dom": 'Bfrtip',
                "buttons": [
                    {
                        "extend": 'collection',
                        "text": 'Export',
                        "buttons": [
                            {
                                "extend": 'csv',
                                'title' :`DELIVERY-ITEMS-${reference_no}`,
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5]
                                }
                            },
                            {
                                "extend": 'pdf',
								'title' :`DELIVERY-ITEMS-${reference_no}`,
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5]
                                }
                            },
                            {
                                "extend": 'print',
								'title' :`DELIVERY-ITEMS-${reference_no}`,
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"product_name"},
                    {"data":"qty"},
					{"data":"received_qty"},
					{"data":"defectived_qty"},
					{"data":"expired_at"},
					{"data":"note"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
				"columnDefs": [{
					"targets": [1,2,3],   // target column
					"className": "textRight",
				},
				{
					"targets": [4],   // target column
					"className": "textCenter",
				}
				]
            });

            var product_id;
            $(document).on('click', '#delete', function(){
                product_id = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });

			let qty = 0;
			let received_qty = 0;
			let defectived_qty = 0;
			let productName;
			let expired_at;
			let note = "";
            $(document).on('click', '#edit', function(){
                product_id = $(this).attr('data-id');
				qty = $(this).attr('data-qty');
				received_qty = $(this).attr('data-received_qty');
				defectived_qty = $(this).attr('data-defectived_qty');
				expired_at = $(this).attr('data-expired_at');
				productName = $(this).attr('data-productname');
				note = $(this).attr('data-note');
				$('#productName').html(productName);
				$('#received_qty').val(received_qty);
         		$('#defectived_qty').val(defectived_qty);
				$('#expired_at').val(expired_at);
				$('#note').val(note);
                $('#editModal').modal('show');
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
			//edit products
			$('#edit_button').click(function(){
				let receivedQty = $("#received_qty").val();
				let defectivedQty = $("#defectived_qty").val();
				let expirationDate = $("#expired_at").val();
				let note = $("#note").val();
                $.ajax({
                    url: "<?= route('updateDeliveryRequestItem') ?>",
					dataType:"json",
                    type:"POST",
                    data:{
						"_token":"<?= csrf_token() ?>",
						"product_id": product_id,
						"delivery_request_id": "<?= $deliveryRequest->id ?>",
						"received_qty" : receivedQty,
						"defectived_qty" : defectivedQty,
						"expired_at" : expirationDate,
						"note" : note
					},
                    beforeSend:function(){
                        $('#edit_button').text('Saving...');
                    },
                    success:function(data)
                    {
						if(data.status == 'error'){
							$('#generalError').text(data.message);
							$('#receivedQtyError').text("");
							$('#defectivedQtyError').text("");
							$('#expiredAtError').text("");
							$('#edit_button').text('Save');
						} else {
							setTimeout(function(){
								$("#deliveryItems").trigger("reset");
								$('#editModal').modal('hide');
								table.ajax.reload();
								$('#edit_button').text('Save');
							}, 2000);
						}			
                    },
					error:function(err){
						console.log(err)
						if(err.responseJSON){
							let receivedMessage = err.responseJSON.data.received_qty ? err.responseJSON.data.received_qty : "";
							let defectivedMessage = err.responseJSON.data.defectived_qty ? err.responseJSON.data.defectived_qty : "";
							let expiredMessage = err.responseJSON.data.expired_at ? err.responseJSON.data.expired_at : "";
							$('#receivedQtyError').text(receivedMessage);
							$('#defectivedQtyError').text(defectivedMessage);
							$('#expiredAtError').text(expiredMessage);
							$('#generalError').text("");
						}
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