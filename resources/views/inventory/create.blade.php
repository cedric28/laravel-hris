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
							<form action="{{ route('inventory.store')}}" method="POST" enctype="multipart/form-data">
								@csrf
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Product Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="product_name" value="{{ old('product_name') }}" class="@error('product_name') is-invalid @enderror form-control" placeholder="Product Name" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Generic Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="generic_name" value="{{ old('generic_name') }}" class="@error('generic_name') is-invalid @enderror form-control" placeholder="Generic Name" >
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">SKU:</label>
									<div class="col-lg-9">	
										<input type="text" name="sku" value="{{ old('sku') }}" class="@error('sku') is-invalid @enderror form-control" placeholder="SKU" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-form-label col-lg-3">Content</label>
									<div class="col-lg-9">
										<textarea rows="3" cols="3" name="content" class="@error('content') is-invalid @enderror form-control" placeholder="Content"></textarea>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Photo:</label>
									<div class="col-lg-9">	
										<input type="file" name="image" class="@error('image') is-invalid @enderror form-control" placeholder="Image" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Category:</label>
									<div class="col-lg-9">
										<select id="category-id" name="category_id" class="@error('category_id') is-invalid @enderror form-control select2">
											<option value="">Select category</option>
											@foreach ($categories as $category)
												<option value="{{ $category->id }}"{{ ($category->id === old('category_id')) ? ' selected' : '' }}>{{ ucwords($category->category_name) }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Supplier:</label>
									<div class="col-lg-9">
										<select id="supplier-id" name="supplier_id" class="@error('supplier_id') is-invalid @enderror form-control select2">
											<option value="">Select supplier</option>
											@foreach ($suppliers as $supplier)
												<option value="{{ $supplier->id }}"{{ ($supplier->id === old('supplier_id')) ? ' selected' : '' }}>{{ ucwords($supplier->name) }}</option>
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