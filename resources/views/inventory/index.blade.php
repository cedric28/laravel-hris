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
							<a type="button" href="{{ route('inventory.create')}}" class="btn btn-outline-success btn-sm float-left"><i class="fas fa-cart-plus mr-2"></i> Add Item</a>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Active Products</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Inactive Products</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                                            <table class="table table-hover table-striped display" style="width:100%" id="inventory">
                                                <thead>
                                                    <tr style="text-align:center;">
                                                        <th>PRODUCT NAME</th>
                                                        <th>GENERIC NAME</th>
                                                        <th>SKU/SERIAL NUMBER</th>
                                                        <th>CATEGORY/TYPE</th>
                                                        <th>DETAILS/GENERALIZATION</th>
                                                        <th>(₱) ORIGINAL PRICE</th>
                                                        <th>(₱) SELLING PRICE</th>
                                                        <th>UNIT MEASUREMENT</th>
                                                        <th>STOCK</th>
                                                        <th>STATUS</th>
                                                        <th>DATE ADDED</th>
                                                        <th>ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($inventories as $inventory)
                    
                                                        <tr>
                                                            <td>{{ $inventory->product_name }}</td>
                                                            <td>{{ $inventory->generic_name }}</td>
                                                            <td>{{ $inventory->sku }}</td>
                                                            <td>{{ $inventory->categories[0]->category_name }}</td>
                                                            <td>{{ $inventory->content }}</td>
                                                            <td>{{ $inventory->original_price }}</td>
                                                            <td>{{ $inventory->selling_price }}</td>
                                                            <td>{{ $inventory->unit_measurement }}</td>
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
                                                    <tr style="text-align:center;">
                                                        <th>PRODUCT NAME</th>
                                                        <th>GENERIC NAME</th>
                                                        <th>SKU/SERIAL NUMBER</th>
                                                        <th>CATEGORY/TYPE</th>
                                                        <th>DETAILS/GENERALIZATION</th>
                                                        <th>(₱) ORIGINAL PRICE</th>
                                                        <th>(₱) SELLING PRICE</th>
                                                        <th>UNIT MEASUREMENT</th>
                                                        <th>STOCK</th>
                                                        <th>STATUS</th>
                                                        <th>DATE ADDED</th>
                                                        <th>ACTION</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab"> 
                                            <table class="table table-hover table-striped display" style="width:100%" id="inactive">
                                                <thead>
                                                    <tr style="text-align:center;">
                                                        <th>PRODUCT NAME</th>
                                                        <th>GENERIC NAME</th>
                                                        <th>SKU/SERIAL NUMBER</th>
                                                        <th>CATEGORY/TYPE</th>
                                                        <th>DETAILS/GENERALIZATION</th>
                                                        <th>(₱) ORIGINAL PRICE</th>
                                                        <th>(₱) SELLING PRICE</th>
                                                        <th>UNIT MEASUREMENT</th>
                                                        <th>STOCK</th>
                                                        <th>STATUS</th>
                                                        <th>DATE ADDED</th>
                                                        <th>ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($InactiveInventories as $inventory)
                                            
                                                        <tr>
                                                            <td>{{ $inventory->product_name }}</td>
                                                            <td>{{ $inventory->generic_name }}</td>
                                                            <td>{{ $inventory->sku }}</td>
                                                            <td>{{ $inventory->categories[0]->category_name }}</td>
                                                            <td>{{ $inventory->content }}</td>
                                                            <td>{{ $inventory->original_price }}</td>
                                                            <td>{{ $inventory->selling_price }}</td>
                                                            <td>{{ $inventory->unit_measurement }}</td>
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
                                                    <tr style="text-align:center;">
                                                        <th>PRODUCT NAME</th>
                                                        <th>GENERIC NAME</th>
                                                        <th>SKU/SERIAL NUMBER</th>
                                                        <th>CATEGORY/TYPE</th>
                                                        <th>DETAILS/GENERALIZATION</th>
                                                        <th>(₱) ORIGINAL PRICE</th>
                                                        <th>(₱) SELLING PRICE</th>
                                                        <th>UNIT MEASUREMENT</th>
                                                        <th>STOCK</th>
                                                        <th>STATUS</th>
                                                        <th>DATE ADDED</th>
                                                        <th>ACTION</th>
                                                    </tr>
                                                </tfoot>
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

    <div id="restoreModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to restore this data?</h4>
                </div>
                <div class="modal-footer">
                 <button type="button" name="restore_button" id="restore_button" class="btn btn-danger">OK</button>
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

var tableInactive = $('#inactive').DataTable({
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "dom": 'Bfrtip',
            "buttons": [
                {
                    "extend": 'collection',
                    "text": 'Export',
                    "buttons": [
                        {
                            "extend": 'csv',
                            'title' :'Inactive Inventory Products',
                            "exportOptions": {
                                "columns": [0,1,2,3,4,5,6,7,8,9,10]
                            }
                        },
                        {
                            "extend": 'pdf',
                            'title' :'Inactive Inventory Products',
                            "exportOptions": {
                                "columns": [0,1,2,3,4,5,6,7,8,9,10]
                            }
                        },
                        {
                            "extend": 'print',
                            'title' :'Inactive Inventory Products',
                            "exportOptions": {
                                "columns": [0,1,2,3,4,5,6,7,8,9,10]
                            }
                        }
                    ],
                }
            ],
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url":"<?= route('InActiveInventory') ?>",
                "dataType":"json",
                "type":"POST",
                "data": function (d){
                    d._token = "<?= csrf_token() ?>"
                }
            },
            initComplete: function () {
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
    
                            column
                                .search( val ? val : '', true, false )
                                .draw();
                        } );
    
                    column.data().unique().sort().each( function ( d, j ) {
                        select.append( '<option value="'+d+'">'+d+'</option>' )
                    } );
                } );
            },
            "columns":[
                {"data":"product_name"},
                {"data":"generic_name"},
                {"data":"sku"},
                {"data":"category_name"},
                {"data":"content"},
                {"data":"original_price"},
                {"data":"selling_price"},
                {"data":"unit_measurement"},
                {"data":"quantity"},
                {"data":"status"},
                {"data":"created_at"},
                {"data":"action","searchable":true,"orderable":false}
            ],
            "columnDefs": [
                {
                    "targets": [2,5,6,8],   // target column
                    "className": "textRight",
                },
                {
                    "targets": [10],   // target column
                    "className": "textCenter",
                }
            ]
        });



        var inventoryId;
        $(document).on('click', '#restore', function(){
            inventoryId = $(this).attr('data-id');
            $('#restoreModal').modal('show');
        });

        $('#restore_button').click(function(){
            $.ajax({
                url:"inventory/restore/"+inventoryId,
                beforeSend:function(){
                    $('#restore_button').text('Restoring...');
                },
                success:function(data)
                {
                    setTimeout(function(){
                        $('#restoreModal').modal('hide');
                        tableInactive.ajax.reload();
                        table.ajax.reload();
                        $('#restore_button').text('OK');
                    }, 2000);
                }
            })
        });
            
            var table = $('#inventory').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        "extend": 'collection',
                        "text": 'Export',
                        "buttons": [
                            {
                                "extend": 'csv',
                                'title' :'Inventory Products',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5,6,7,8,9,10]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' :'Inventory Products',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5,6,7,8,9,10]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' :'Inventory Products',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5,6,7,8,9,10]
                                }
                            }
                        ],
                    }
                ],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeInventory') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data": function (d){
                        d._token = "<?= csrf_token() ?>"
                    }
                },
                initComplete: function () {
                    this.api().columns().every( function () {
                        var column = this;
                        var select = $('<select><option value=""></option></select>')
                            .appendTo( $(column.footer()).empty() )
                            .on( 'change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
        
                                column
                                    .search( val ? val : '', true, false )
                                    .draw();
                            } );
        
                        column.data().unique().sort().each( function ( d, j ) {
                            select.append( '<option value="'+d+'">'+d+'</option>' )
                        } );
                    } );
                },
                "columns":[
                    {"data":"product_name"},
                    {"data":"generic_name"},
                    {"data":"sku"},
                    {"data":"category_name"},
                    {"data":"content"},
                    {"data":"original_price"},
                    {"data":"selling_price"},
                    {"data":"unit_measurement"},
                    {"data":"quantity"},
                    {"data":"status"},
                    {"data":"created_at"},
                    {"data":"action","searchable":true,"orderable":false}
                ],
                "columnDefs": [
                    {
					    "targets": [2,5,6,8],   // target column
					    "className": "textRight",
				    },
                    {
					    "targets": [10],   // target column
					    "className": "textCenter",
				    }
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
                            tableInactive.ajax.reload();
							table.ajax.reload();
                            $('#ok_button').text('OK');
                        }, 2000);
                    }
                })
            });


            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                $('.table:visible').each( function(e) {
                $(this).DataTable().columns.adjust().responsive.recalc();
                });
            });

            
		</script>
        @endpush('scripts')
@endsection