@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Applicant - {{ ucwords($employee->name) }} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('employee.index')}}">Applicants</a></li>
			           <li class="breadcrumb-item">{{ ucwords($employee->name) }} Details</li>
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
							<div class="card card-success">
								<div class="card-header">
									Basic Information
								</div>
								<div class="card-body">
									<table class="table table-bordered">
										<tr>
											<th>Reference</th>
											<td>{{ $employee->reference_no }}</td>
										</tr>
										<tr>
											<th>Fullname</th>
											<td>{{ $employee->last_name }}, {{ $employee->first_name }}  {{ $employee->middle_name }}</td>
										</tr>
										<tr>
											<th>Nickname</th>
											<td>{{ $employee->nickname }}</td>
										</tr>
										<tr>
											<th>Date of Birth</th>
											<td>{{ $employee->birthdate }}</td>
										</tr>
										<tr>
											<th>Gender</th>
											<td>{{ $employee->gender->name }}</td>
										</tr>
										<tr>
											<th>Civil Status</th>
											<td>{{ $employee->civil_status->name }}</td>
										</tr>
										<tr>
											<th>Address</th>
											<td>{{ $employee->unit.' '.$employee->lot_block.' '.$employee->street.' '.$employee->subdivision.' '.$employee->barangay.' '.$employee->municipality.' '.$employee->province }}</td>
										</tr>
										<tr>
											<th>Phone</th>
											<td>+63{{ $employee->contact_number }}</td>
										</tr>
										<tr>
											<th>Email</th>
											<td>{{ $employee->email }}</td>
										</tr>
									</table>			
								</div>
							</div>

							<div class="card card-success">
								<div class="card-header">
									In-Case of Emergency Information
								</div>
								<div class="card-body">
									<table class="table table-bordered">
										<tr>
											<th>Emergency Contact Name</th>
											<td>{{ $employee->emergency_contact_name ?? '-' }}</td>
										</tr>
										<tr>
											<th>Relationship</th>
											<td>{{ $employee->emergency_relationship ?? '-' }}</td>
										</tr>
										<tr>
											<th>Phone</th>
											<td>+63{{ $employee->emergency_contact_number ?? '-' }}</td>
										</tr>
										<tr>
											<th>Address</th>
											<td>{{ $employee->emergency_address ?? '-' }}</td>
										</tr>
									</table>	
								</div>
							</div>

							<div class="card card-success">
								<div class="card-header">
								Government Information
								</div>
								<div class="card-body">
									<table class="table table-bordered">
										<tr>
											<th>SSS</th>
											<td>{{ $employee->sss ?? '-'}}</td>
										</tr>
										<tr>
											<th>PAG-IBIG</th>
											<td>{{ $employee->pagibig ?? '-' }}</td>
										</tr>
										<tr>
											<th>Philhealth</th>
											<td>{{ $employee->philhealth ?? '-' }}</td>
										</tr>
										<tr>
											<th>TIN</th>
											<td>{{ $employee->tin  ?? '-'}}</td>
										</tr>
									</table>	
								</div>
							</div>
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
       
		</script>
        @endpush('scripts')
@endsection