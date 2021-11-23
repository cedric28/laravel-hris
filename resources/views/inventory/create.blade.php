@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Inventory - New Record</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('inventory.index')}}">Inventory</a></li>
			  <li class="breadcrumb-item">Add New Inventory</li>
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
								<h3 class="card-title">Inventory Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('inventory.store')}}" method="POST">
								@csrf
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Products:</label>
									<div class="col-lg-9 col-sm-12">
										<select id="role-id" name="product_id" class="form-control select2" style="width: 100%;">
											<option value="">Select Product</option>
											@foreach ($products as $product)
												<option value="{{ $product->id }}"{{ ($product->id === old('product_id')) ? ' selected' : '' }}>{{ ucwords($product->product_name) }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Original Price:</label>
									<div class="col-lg-9">	
										<input type="text" name="original_price" value="{{ old('original_price') }}" class="@error('original_price') is-invalid @enderror form-control" placeholder="Original Price" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Selling Price:</label>
									<div class="col-lg-9">	
										<input type="text" name="selling_price" value="{{ old('selling_price') }}" class="@error('selling_price') is-invalid @enderror form-control" placeholder="Selling Price" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Quantity:</label>
									<div class="col-lg-9">	
										<input type="number" name="quantity" value="{{ old('quantity') }}" class="@error('quantity') is-invalid @enderror form-control" placeholder="Quantity" >
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
        <script>
			$(function () {
				$('.select2').select2()
			});
		</script>
        @endpush('scripts')
@endsection