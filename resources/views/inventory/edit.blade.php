@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Inventory - {{ ucwords($inventory->product_name)}} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('inventory.index')}}">Inventory</a></li>
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
								<h3 class="card-title">Inventory Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('inventory.update',$inventory->id)}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Product Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="product_name" value="{{ old('product_name', $inventory->product_name) }}" class="@error('product_name') is-invalid @enderror form-control" placeholder="Product Name" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Generic Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="generic_name" value="{{ old('generic_name', $inventory->generic_name) }}" class="@error('generic_name') is-invalid @enderror form-control" placeholder="Generic Name" >
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Unit Measurement:</label>
									<div class="col-lg-9">	
										<input type="text" name="unit_measurement" value="{{ old('unit_measurement', $inventory->unit_measurement) }}" class="@error('unit_measurement') is-invalid @enderror form-control" placeholder="Unit Measurement e.g 10 ml" >
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">SKU:</label>
									<div class="col-lg-9">	
										<input type="text" name="sku" value="{{ old('sku', $inventory->sku) }}" class="@error('sku') is-invalid @enderror form-control" placeholder="SKU" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-form-label col-lg-3">Content</label>
									<div class="col-lg-9">
									<textarea rows="3" cols="3" name="content" class="@error('content') is-invalid @enderror form-control" placeholder="Content">{{ $inventory->content}}</textarea>
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
										<select id="role-id" name="category_id" class="form-control select2">
											<option value="">Select category</option>
											@foreach ($categories as $category)
												<option value="{{ $category->id }}"{{ ($category->id === $categoryId ) ? ' selected' : '' }}>{{ ucwords($category->category_name) }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Supplier:</label>
									<div class="col-lg-9">
										<select id="role-id" name="supplier_id" class="form-control select2">
											<option value="">Select supplier</option>
											@foreach ($suppliers as $supplier)
												<option value="{{ $supplier->id }}"{{ ($supplier->id === old('supplier_id', $inventory->supplier_id)) ? ' selected' : '' }}>{{ ucwords($supplier->name) }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Original Price:</label>
									<div class="col-lg-9">	
										<input type="text" name="original_price" value="{{ old('original_price', $inventory->original_price) }}" class="@error('original_price') is-invalid @enderror form-control" placeholder="Original Price" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Selling Price:</label>
									<div class="col-lg-9">	
										<input type="text" name="selling_price" value="{{ old('selling_price', $inventory->selling_price) }}" class="@error('selling_price') is-invalid @enderror form-control" placeholder="Selling Price" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Unit Measurement:</label>
									<div class="col-lg-9">	
										<input type="text" name="unit_measurement" value="{{ old('unit_measurement',$inventory->unit_measurement) }}" class="@error('unit_measurement') is-invalid @enderror form-control" placeholder="e.g 1ml" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Stock on Hand:</label>
									<div class="col-lg-9">	
										<input disabled="disabled"  name="quantity" value="{{ old('quantity', $inventory->quantity) }}" class="@error('quantity') is-invalid @enderror form-control" placeholder="Quantity" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-form-label col-lg-3">Adjust Stock ?:</label>
									<div class="col-lg-9">
										<div class="icheck-success d-inline">
											<input type="checkbox" name="adjust_checker" checked id="checkboxSuccess1">
											<label for="checkboxSuccess1">
											</label>
										</div>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Inventory Adjustment Type:</label>
									<div class="col-lg-9 col-sm-12">
										<select id="adjustment" disabled name="inventory_adjustment_type_id" class="form-control select2" style="width: 100%;">
											<option value="">Select adjustment</option>
											@foreach ($inventoryAdjustmentTypes as $adjustment)
												<option value="{{ $adjustment->id }}"{{ ($adjustment->id === old('inventory_adjustment_type_id')) ? ' selected' : '' }}>{{ ucwords($adjustment->type) }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Stock Adjusted:</label>
									<div class="col-lg-9">	
										<input type="number" disabled="disabled"  name="adjusted_quantity" id="stock_adjusted" value="{{ old('adjusted_quantity') }}" class="@error('adjusted_quantity') is-invalid @enderror form-control" placeholder="Quantity" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-form-label col-lg-3">Reason:</label>
									<div class="col-lg-9">
										<textarea disabled id="reason" rows="3" cols="3" name="reason" class="@error('reason') is-invalid @enderror form-control" placeholder="Reason"></textarea>
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
		<!-- Bootstrap Switch -->
		<script src="{{ asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
        <script>
			$(function () {
				$( "#checkboxSuccess1" ).prop( "checked", false );
				$('#checkboxSuccess1').on('change', function(){
					if($(this).prop('checked') === true){
						$( "#adjustment" ).prop( "disabled", false );
						$( "#reason" ).prop( "disabled", false );
						$("#stock_adjusted").prop( "disabled", false );
						$( "#checkboxSuccess1" ).prop( "value", 1 );
					} else{
						$( "#adjustment" ).prop( "disabled", true );
						$( "#reason" ).prop( "disabled", true );
						$("#stock_adjusted").prop( "disabled", true );
						$( "#checkboxSuccess1" ).prop( "value", null);
					}
				});
				$('.select2').select2()
			});
		</script>
        @endpush('scripts')
@endsection