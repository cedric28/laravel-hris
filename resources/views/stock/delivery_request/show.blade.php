@extends('layouts.app')

@section('content')
    <section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Delivery Request - {{ ucwords($deliveryRequest->reference_no) }} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('delivery-request.index')}}">Delivery Request List</a></li>
			  <li class="breadcrumb-item">{{ ucwords($deliveryRequest->reference_no) }} Details</li>
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
									<td>{{ $deliveryRequest->reference_no }}</td>
								</tr>
								<tr>
									<th>Notes</th>
									<td>{!! $deliveryRequest->content !!}</td>
								</tr>
								<tr>
									<th>Delivery Date</th>
									<td>{{ $deliveryRequest->delivery_at }}</td>
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
													<td>{{ $stock->delivery_request->delivery_date }}</td>
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