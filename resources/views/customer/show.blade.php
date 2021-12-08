@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Customer - {{ ucwords($customer->name) }} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('customer.index')}}">Customers</a></li>
			  <li class="breadcrumb-item">{{ ucwords($customer->name) }} Details</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
      	<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- About Me Box -->
					<div class="card">
						<div class="card-header">
							
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<table class="table table-bordered">
								<tr>
									<th>Reference</th>
									<td>{{ $customer->reference_no }}</td>
								</tr>
								<tr>
									<th>Total Points</th>
									<td>{{ $totalPoints }} points</td>
								</tr>
								<tr>
									<th>Customer Name</th>
									<td>{{ $customer->name }}</td>
								</tr>
								<tr>
									<th>Contact Number</th>
									<td>{{ $customer->contact_number }}</td>
								</tr>
								<tr>
									<th>Email</th>
									<td>{{ $customer->email }}</td>
								</tr>
								<tr>
									<th>Address</th>
									<td>{{ $customer->address }}</td>
								</tr>

								<tr>
									<th>Total Points</th>
									<td>{{ $totalPoints }}</td>
								</tr>

							</table>
						</div>
						<!-- /.card-body -->
						<div class="card-footer clearfix">
							
						</div>
					</div>
					<!-- /.card -->
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<!-- About Me Box -->
					<div class="card card-primary">
						<div class="card-header">
							<h3 class="card-title">Points History</h3>						
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<table id="customerpoints" class="table table-hover table-bordered table-striped">
								<thead>
									<tr>
										<th>POINT</th>
										<th>DATE ADDED</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($customer->customer_points as $point)
										<tr>
											<td>{{ $point->point }}</td>
											<td>{{ $point->created_at }}</td>
										</tr>
									@endforeach
								</tbody>
								<tfoot>
									<tr>
										<th>POINT</th>
										<th>DATE ADDED</th>
									</tr>
								</tfoot>
							</table>
						</div>
						<!-- /.card-body -->
						<div class="card-footer clearfix">
							
						</div>
					</div>
					<!-- /.card -->
				</div>
			</div>
		</div>	
	</section>
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
            var table = $('#customerpoints').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('getCustomerPoints') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
						"_token":"<?= csrf_token() ?>",
						"customer_id": "<?= $customer->id ?>"
					}
                },
                "columns":[
                    {"data":"point"},
					{"data":"created_at"},
                ]
            });
		</script>
        @endpush('scripts')
@endsection