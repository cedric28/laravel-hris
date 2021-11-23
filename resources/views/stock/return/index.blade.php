@extends('layouts.app')

@section('content')
    <section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Return Stock List</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Return Stock List</li>
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
                            <a type="button" href="{{ route('return-stock.create')}}" class="btn btn-outline-success btn-sm float-left"><i class="fas fa-cart-plus mr-2"></i> Add Return Stock</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="delivery" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Reference No</th>
                                        <th>Supplier</th>
                                        <th>Notes</th>
                                        <th>Delivery Date</th>
                                        <th>Received Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($returnStocks as $returnStock)
                                        <tr>
                                            <td>{{ $returnStock->reference_no }}</td>
                                            <td>{{ $returnStock->supplier->name }}</td>
                                            <td>{!! $returnStock->content !!}</td>
                                            <td>{{ $returnStock->delivery_at }}</td>
                                            <td>{{ $returnStock->created_at }}</td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Reference No</th>
                                        <th>Supplier</th>
                                        <th>Notes</th>
                                        <th>Delivery Date</th>
                                        <th>Received Date</th>
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
			 var table = $('#delivery').DataTable({
				"responsive": true, "lengthChange": false, "autoWidth": false,
                // "dom": 'Blfrtip',
      			// "buttons": ["csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeReturnStock') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{"_token":"<?= csrf_token() ?>"}
                },
                "columns":[
                    {"data":"reference_no"},
                    {"data":"name"},
                    {"data":"content"},
                    {"data":"delivery_at"},
                    {"data":"received_at"},
                    {"data":"action","searchable":false,"orderable":false}
                ]
            });

			$(document).on('click', '#show', function(){
                var deliveryId = $(this).attr('data-id');
                window.location.href = 'return-stock/'+deliveryId;
            });

            $(document).on('click', '#edit', function(){
                var id = $(this).attr('data-id');
                window.location.href = 'return-stock/'+id+'/edit';
            });

            
   
            var delivery_id;
            $(document).on('click', '#delete', function(){
                delivery_id = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function(){
                $.ajax({
                    url:"return-stock/destroy/"+delivery_id,
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