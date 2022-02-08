@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Point</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Point</li>
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
							<!-- <a type="button" href="{{ route('point.create')}}" class="btn btn-outline-success btn-sm float-left"><i class="fas fa-cart-plus mr-2"></i> Add Point</a>  -->
						</div>
						<!-- /.card-header -->
						<div class="card-body">
						<table class="table table-hover table-striped" id="example">
								<thead>
									<tr style="text-align:center;">
										<th>POINT NAME</th>
                                        <th>DISCOUNT RATE</th>
                                        <th>POINT TO BE EARN</th>
                                        <th>PRICE PER POINT</th>
                                        <th>TOTAL NEEDED POINT</th>
										<th>DATE ADDED</th>
										<th>ACTION</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($points as $point)
										<tr>
											<td>{{ $point->point_name }}</td>
                                            <td>{{ $point->discount_rate }}</td>
                                            <td>{{ $point->point }}</td>
                                            <td>{{ $point->price_per_point }}</td>
                                            <td>{{ $point->total_needed_point }}</td>
											<td>{{ $point->created_at }}</td>
											<td>
												
											</td>
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

            var table = $('#example').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activePoints') ?>",
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
                                'title' : 'Point-Discount-List',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : 'Point-Discount-List',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : 'Point-Discount-List',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"point_name"},
                    {"data":"discount_rate"},
                    {"data":"point"},
                    {"data":"price_per_point"},
                    {"data":"total_needed_point"},
                    {"data":"created_at"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [{
					"targets": [1,2,3,4],   // target column
					"className": "textRight",
				},
                {
					"targets": [5],   // target column
					"className": "textCenter",
				}
                ],
            });

			$(document).on('click', '#show', function(){
                var pointId = $(this).attr('data-id');
                window.location.href = 'point/'+pointId;
            });

            $(document).on('click', '#edit', function(){
                var id = $(this).attr('data-id');
                window.location.href = 'point/'+id+'/edit';
            });

            
   
            var point_id;
            $(document).on('click', '#delete', function(){
                point_id = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function(){
                $.ajax({
                    url:"point/destroy/"+point_id,
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