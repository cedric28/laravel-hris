@extends('layouts.app')
<style>
.hover-image{
	cursor: pointer !important;
}

 .modal-image {
       width: 70%;
        height: auto;
        margin: 0 auto;
        display: block;
    }
    .modal-lg {
        width: 70%;
    }
    .modal-content {
        padding: 0;
    }
</style>
@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Employee - {{ ucwords($employee->name) }} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('employee.index')}}">Employees</a></li>
			           <li class="breadcrumb-item">{{ ucwords($employee->name) }} Details</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
      	<div class="container-fluid">
								<div class="row">
									<div class="col-md-4">
										<div class="card card-widget widget-user-2">
										<div class="widget-user-header text-white" style="background: url('../dist/img/photo4.jpg') center center;">
											<div class="widget-user-image">
											<img class="img-circle elevation-2" src="{{ $employee->gender->id == 1 ? asset('dist/img/avatar5.png') : asset('dist/img/avatar3.png') }}" alt="User Avatar">
											</div>
											<h3 class="widget-user-username">{{ $employee->last_name }}, {{ $employee->first_name }}  {{ $employee->middle_name }}</h3>
											</div>
											<div class="card-footer p-0">
														<table class="table table-bordered w-100">
										<tr>
											<th>Employee No</th>
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
											<th>Age</th>
											<td>{{ $age }}</td>
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
										</div>
				<div class="col-md-8">
					<!-- About Me Box -->
					<div class="card">
						<div class="card-header">
							
						</div>
						<!-- /.card-header -->
						<div class="card-body">

							<div class="card card-success">
								<div class="card-header">
									In-Case of Emergency Information
								</div>
								<div class="card-body">
									<table class="table table-bordered w-100">
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
								 <table class="table table-bordered w-100">
										<tr>
														<th>SSS</th>
														<td>{{ $employee->sss ?? '-'}}</td>
														<td>
																		@php
																		$imageUrl = $employee->sss_file != null ? '/images/sss/'.$employee->id.'/'.$employee->sss_file : 'http://w3adda.com/wp-content/uploads/2019/09/No_Image-128.png';
																		@endphp
																		@if($employee->sss_file)
																		<img 
																						class="profile-user-img img-fluid hover-image"
																						src="{{ url($imageUrl) }}"
																						alt="Contract Image"
																							data-toggle="modal" 
																						data-target="#imageModal"
																						data-image-url="{{ url($imageUrl) }}"
																		>
																		@else
																		No Image
																		@endif
														</td>
										</tr>
										<tr>
														<th>PAG-IBIG</th>
														<td>{{ $employee->pagibig ?? '-' }}</td>
														<td>
																		@php
																		$imageUrl = $employee->pagibig_file != null ? '/images/pagibig/'.$employee->id.'/'.$employee->pagibig_file : 'http://w3adda.com/wp-content/uploads/2019/09/No_Image-128.png';
																		@endphp
																		@if($employee->pagibig_file)
																		<img 
																						class="profile-user-img img-fluid hover-image"
																						src="{{ url($imageUrl) }}"
																						alt="Contract Image"
																							data-toggle="modal" 
																						data-target="#imageModal"
																						data-image-url="{{ url($imageUrl) }}"
																		>
																		@else
																		No Image
																		@endif
														</td>
										</tr>
										<tr>
														<th>Philhealth</th>
														<td>{{ $employee->philhealth ?? '-' }}</td>
														<td>
																		@php
																		$imageUrl = $employee->philhealth_file != null ? '/images/philhealth/'.$employee->id.'/'.$employee->philhealth_file : 'http://w3adda.com/wp-content/uploads/2019/09/No_Image-128.png';
																		@endphp
																		@if($employee->philhealth_file)
																		<img 
																						class="profile-user-img img-fluid hover-image"
																						src="{{ url($imageUrl) }}"
																						alt="Contract Image"
																							data-toggle="modal" 
																						data-target="#imageModal"
																						data-image-url="{{ url($imageUrl) }}"
																		>
																		@else
																		No Image
																		@endif
														</td>
										</tr>
										<tr>
														<th>TIN</th>
														<td>{{ $employee->tin  ?? '-'}}</td>
														<td>
																		@php
																		$imageUrl = $employee->tin_file != null ? '/images/tin/'.$employee->id.'/'.$employee->tin_file : 'http://w3adda.com/wp-content/uploads/2019/09/No_Image-128.png';
																		@endphp
																		@if($employee->tin_file)
																		<img 
																						class="profile-user-img img-fluid hover-image"
																						src="{{ url($imageUrl) }}"
																						alt="Contract Image"
																						data-toggle="modal" 
																						data-target="#imageModal"
																						data-image-url="{{ url($imageUrl) }}"
																		>
																		@else
																		No Image
																		@endif
														</td>
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
	
	<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <img src="" alt="Contract Image" class="img-fluid modal-image">
            </div>
        </div>
    </div>
</div>

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
		<script>
      $(document).ready(function() {
        $('.hover-image').hover(function() {
            var imageUrl = $(this).data('image-url');
            console.log('imageUrl',imageUrl.toString())
            $('#imageModal .modal-image').attr('src', imageUrl.toString());
            $('#imageModal').modal('show');
        }, function() {
            $('#imageModal').modal('hide');
        });
    });
</script>
        @endpush('scripts')
@endsection