@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Product - {{ ucwords($product->product_name) }} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('product.index')}}">Products</a></li>
			  <li class="breadcrumb-item">{{ ucwords($product->product_name) }} Details</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
      	<div class="container-fluid">
			<div class="row">
				<div class="col-md-3">
				<!-- Profile Image -->
					<div class="card card-primary card-outline">
						<div class="card-body box-profile">
							<div class="text-center">
							@php
								$imageUrl = $product->image != null ? '/images/'.$product->id.'/'.$product->image : 'http://w3adda.com/wp-content/uploads/2019/09/No_Image-128.png';
							@endphp
							<img class="profile-user-img img-fluid img-circle"
								src="{{ url($imageUrl) }}"
								alt="Product Image">
							</div>
							<ul class="list-group list-group-unbordered mb-3">
								<li class="list-group-item">
									<span>Product Name:</span>
									<b>{{ ucwords($product->product_name) }}</b> 
								</li>
							
								<li class="list-group-item">
									<span>Generic Name:</span>
									<b>{{ ucwords($product->generic_name) }}</b> 
								</li>
								
								<li class="list-group-item">
									<span>SKU:</span>
									<b>{{ ucwords($product->sku) }}</b> 
								</li>
								<li class="list-group-item">
									<span>Status:</span>
									<b>{{ $product->deleted_at != null ?  'DEACTIVE' : 'ACTIVE'}}</b> 
								</li>
							</ul>
							<!-- @if ($product->deleted_at != null)
							<a href="#" class="btn btn-success btn-block"><b>ACTIVE</b></a>
							@else
							<a href="#" class="btn btn-danger btn-block"><b>DEACTIVATE</b></a>
							@endif -->
						</div>
					<!-- /.card-body -->
					</div>
				<!-- /.card -->
				</div>
				
				<div class="col-md-9">
					<!-- About Me Box -->
					<div class="card card-primary">
						<div class="card-header">
							<h3 class="card-title">Details</h3>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<p class="text-muted">
							{!! $product->content !!}
							</p>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
			</div>
		</div>	
	</section>
	<!-- /page content -->
        @push('scripts')
        <!-- Javascript -->
        <!-- Vendors -->
      
        <script src="{{ asset('vendors/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/jquery.scrollbar/jquery.scrollbar.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/jquery-scrollLock/jquery-scrollLock.min.js') }}"></script>
        @endpush('scripts')
@endsection