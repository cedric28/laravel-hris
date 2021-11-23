@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Supplier - {{ ucwords($supplier->name) }} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('supplier.index')}}">Suppliers</a></li>
			  <li class="breadcrumb-item">{{ ucwords($supplier->name) }} Details</li>
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
									<th>Supplier Name</th>
									<td>{{ $supplier->name }}</td>
								</tr>
								<tr>
									<th>Short Name</th>
									<td>{{ $supplier->short_name }}</td>
								</tr>
								<tr>
									<th>Contact Number</th>
									<td>{{ $supplier->contact_number }}</td>
								</tr>
								<tr>
									<th>Email</th>
									<td>{{ $supplier->email }}</td>
								</tr>
								<tr>
									<th>Address</th>
									<td>{{ $supplier->address }}</td>
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
		</div>	
	</section>
        @push('scripts')
        <!-- Javascript -->
      
        @endpush('scripts')
@endsection