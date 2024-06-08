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
            <h1>Client - {{ ucwords($client->name) }} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('client.index')}}">Clients</a></li>
			           <li class="breadcrumb-item">{{ ucwords($client->name) }} Details</li>
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
									<td>{{ $client->reference_no }}</td>
								</tr>
								<tr>
									<th>Client Name</th>
									<td>{{ $client->name }}</td>
								</tr>
        <tr>
									<th>Short Name</th>
									<td>{{ $client->short_name }}</td>
								</tr>
								<tr>
									<th>Contact Number</th>
									<td>{{ $client->contact_number }}</td>
								</tr>
								<tr>
									<th>Email</th>
									<td>{{ $client->email }}</td>
								</tr>
								<tr>
									<th>Address</th>
									<td>{{ $client->address }}</td>
								</tr>

									<tr>
									<th>Contract</th>
									<td>
										@php
										$imageUrl = $client->contract != null ? '/images/'.$client->id.'/'.$client->contract : 'http://w3adda.com/wp-content/uploads/2019/09/No_Image-128.png';
										@endphp
										@if($client->contract)
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
		<script>
       
		</script>
        @endpush('scripts')
@endsection