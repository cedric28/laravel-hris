@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Inventory</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Inventory</li>
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
							<a type="button" href="{{ route('inventory.create')}}" class="btn btn-outline-success btn-sm float-left"><i class="fas fa-cart-plus mr-2"></i> Add Inventory</a>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
						<table class="table table-hover table-striped" id="inventory">
								<thead>
									<tr>
										<th>Product Name</th>
                                        <th>Generic Name</th>
                                        <th>Details</th>
                                        <th>Category</th>
                                        <th>Original Price</th>
                                        <th>Selling Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
										<th>Created At</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($inventories as $inventory)

										<tr>
											<td>{{ $inventory->product->product_name }}</td>
											<td>{{ $inventory->product->generic_name }}</td>
                                            <td>{{ $inventory->product->content }}</td>
                                            <td>{{ $inventory->product->categories[0]->category_name }}</td>
											<td>{{ $inventory->original_price }}</td>
                                            <td>{{ $inventory->selling_price }}</td>
											<td>{{ $inventory->quantity }}</td>
                                            <td>
                                                @if($inventory->quantity == 0)
                                                    <span title="Danger" class="badge bg-danger">Danger</span>
                                                @elseif($inventory->quantity < $inventoryLevel[0]->re_stock)
                                                    <span title="Danger" class="badge bg-danger">Re-Stock</span>
                                                @elseif($inventory->quantity == $inventoryLevel[0]->critical)
                                                    <span title="Danger" class="badge bg-warning">Critical</span>
                                                @elseif($inventory->quantity > $inventoryLevel[0]->critical)
                                                    <span title="Danger" class="badge bg-success">Average</span>
                                                @endif
                                            </td>
                                            <td>{{ $inventory->created_at }}</td>
											<td>
												
											</td>
										</tr>
									@endforeach
								</tbody>
								<tfoot>
                                    <tr>
										<th>Product Name</th>
                                        <th>Generic Name</th>
                                        <th>Details</th>
                                        <th>Category</th>
                                        <th>Original Price</th>
                                        <th>Selling Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
										<th>Created At</th>
										<th>Action</th>
									</tr>
								</tfoot>
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
	<!-- /page content -->
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

            var table = $('#inventory').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeInventory') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{"_token":"<?= csrf_token() ?>"}
                },
                "columns":[
                    {"data":"product_name"},
                    {"data":"generic_name"},
                    {"data":"content"},
                    {"data":"category_name"},
                    {"data":"original_price"},
                    {"data":"selling_price"},
                    {"data":"quantity"},
                    {"data":"status"},
                    {"data":"created_at"},
                    {"data":"action","searchable":false,"orderable":false}
                ]
            });

			$(document).on('click', '#show', function(){
                var inventoryId = $(this).attr('data-id');
                window.location.href = 'inventory/'+inventoryId;
            });

            $(document).on('click', '#edit', function(){
                var id = $(this).attr('data-id');
                window.location.href = 'inventory/'+id+'/edit';
            });

            
   
            var inventory_id;
            $(document).on('click', '#delete', function(){
                inventory_id = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function(){
                $.ajax({
                    url:"inventory/destroy/"+inventory_id,
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