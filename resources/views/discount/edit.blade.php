@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Discount - {{ ucwords($discount->discount_name)}} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('discount.index')}}">Discounts</a></li>
			  <li class="breadcrumb-item">Edit Details</li>
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
							@include('partials.message')
							@include('partials.errors')
							<div class="row">
								<h3 class="card-title">Discount Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('discount.update', $discount->id)}}" method="POST" >
								@csrf
								@method('PATCH')
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Discount Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="discount_name" value="{{ old('discount_name', $discount->discount_name) }}" class="@error('discount_name') is-invalid @enderror form-control" placeholder="Discount Name" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Discount Rate(In Decimal):</label>
									<div class="col-lg-9">	
										<input type="text" name="discount_rate" value="{{ old('discount_rate', $discount->discount_rate) }}" class="@error('discount_rate') is-invalid @enderror form-control" placeholder="Discount Rate" >
									</div>
								</div>

								<div class="text-right">
									<button type="submit" class="btn btn-primary">Save <i class="icon-paperplane ml-2"></i></button>
								</div>
							</form>
						</div>
						<div class="card-footer clearfix">
							
						</div>
					</div>
				</div>
			</div>
		</div>	
	</section>
	<!-- /page content -->
        @push('scripts')
        <!-- Javascript -->
        <!-- Vendors -->
    
        @endpush('scripts')
@endsection