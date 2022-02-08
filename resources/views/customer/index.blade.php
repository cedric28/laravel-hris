@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Customers</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Customers</li>
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
							<a type="button" href="{{ route('customer.create')}}" class="btn btn-outline-success btn-sm float-left"><i class="fas fa-users mr-2"></i> Add Customer</a>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
						<table class="table table-hover table-striped" id="customer">
								<thead>
									<tr style="text-align:center;">
										<th>REFERENCE NO</th>
                                        <th>FULLNAME</th>
                                        <th>CONTACT NO</th>
                                        <th>EMAIL</th>
                                        <th>ADDRESS</th>
										<th>DATE ADDED</th>
										<th>ACTION</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($customers as $customer)
										<tr>
											<td>{{ $customer->reference_no }}</td>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->contact_number }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->address }}</td>
											<td>{{ $customer->created_at }}</td>
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

            var table = $('#customer').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeCustomer') ?>",
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
                                'title' : 'Customer-List',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : 'Customer-List',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : 'Customer-List',
                                "exportOptions": {
                                    "columns": [0,1,2,3,4,5]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"reference_no"},
                    {"data":"name"},
                    {"data":"contact_number"},
                    {"data":"email"},
                    {"data":"address"},
                    {"data":"created_at"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [
				{
					"targets": [5],   // target column
					"className": "textCenter",
				}]
            });

			$(document).on('click', '#show', function(){
                var customerId = $(this).attr('data-id');
                window.location.href = 'customer/'+customerId;
            });

            $(document).on('click', '#edit', function(){
                var id = $(this).attr('data-id');
                window.location.href = 'customer/'+id+'/edit';
            });

            
   
            var customer_id;
            $(document).on('click', '#delete', function(){
                customer_id = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function(){
                $.ajax({
                    url:"customer/destroy/"+customer_id,
                    beforeSend:function(){
                        $('#ok_button').text('Deleting...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModal').modal('hide');
							table.ajax.reload();
                        }, 2000);
                    }
                })
            });
		</script>
        @endpush('scripts')
@endsection