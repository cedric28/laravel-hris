@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Delivery Request - New Record</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('delivery-request.index')}}">Delivery Request List</a></li>
			  <li class="breadcrumb-item">Add New Delivery Request</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
      	<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="card card-primary card-outline">
						<div class="card-header">
							@include('partials.message')
							@include('partials.errors')
							<div class="row">
								<h3 class="card-title">Delivery Request Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('delivery-request.store')}}" method="POST">
								@csrf
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

                                <!-- Date -->
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Date Delivered:</label>
									<div class="col-lg-9">	
										<div class="input-group date" id="reservationdate" data-target-input="nearest">
											<input type="text" name="delivery_at" class="@error('delivery_at') is-invalid @enderror form-control datetimepicker-input" data-target="#reservationdate"/>
											<div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fa fa-calendar"></i></div>
											</div>
										</div>
									</div>
                                </div>

								<div class="card card-primary">
									<div class="card-header">
										Products
									</div>

									<div class="card-body">
										<table class="table" id="products_table">
											<thead>
												<tr>
													<th>Product</th>
													<th>Quantity</th>
												</tr>
											</thead>
											<tbody>
												@foreach (old('products', ['']) as $index => $oldProduct)
													<tr id="product{{ $index }}">
														<td>
															<select name="products[]" class="form-control">
																<option value="">-- Select Product --</option>
																@foreach ($products as $product)
																	<option value="{{ $product->id }}"{{ $oldProduct == $product->id ? ' selected' : '' }}>
																		{{ $product->product_name }}
																	</option>
																@endforeach
															</select>
														</td>
														<td>
															<input type="number" name="quantities[]" class="form-control" value="{{ old('quantities.' . $index) ?? '1' }}" />
														</td>
														<td>
															<a id="delete_row" class="btn btn-danger">Delete</a>
														</td>
													</tr>
												@endforeach
												<tr id="product{{ count(old('products', [''])) }}"></tr>
											</tbody>
										</table>

										<div class="row">
											<div class="col-md-12">
										
												<!-- <button id="delete_row" class="btn btn-danger">Delete</button> -->
											
												<button id="add_row" class="btn btn-success">+ Add Row</button>
											</div>
										</div>
									</div>
								</div>

								<div class="text-right">
									<button type="submit" class="btn btn-primary">SAVE <i class="icon-paperplane ml-2"></i></button>
								</div>
							</div>
						</div>
						<div class="card-footer clearfix">
							
						</div>
					</div>
				</div>
			</div>
		</div>	
	</section>
	@push('scripts')
	<!-- Javascript -->
	<!-- Vendors -->
	<script>
		$(document).ready(function(){
			let row_number = {{ count(old('products', [''])) }};
			$("#add_row").click(function(e){
				e.preventDefault();
				let new_row_number = row_number - 1;
				$('#product' + row_number).html($('#product' + new_row_number).html()).find('td:first-child');
				$('#products_table').append('<tr id="product' + (row_number + 1) + '"></tr>');
				row_number++;
			});
			$(document).on('click',"#delete_row", function(e)
    		{
				e.preventDefault();
				if(row_number > 1){
					
					$(this).closest('tr').remove();
					// $("#product" + (row_number - 1)).html('');
					// $(this).closest("tbody").find("#product" + (row_number - 1)).html('');
					// row_number--;
				}
    		});
			// $("#delete_row").click(function(e){
			// 	e.preventDefault();
			// 	if(row_number > 1){
			// 	// 	console.log(row_number);
			// 		$("#product" + (row_number - 1)).html('');
			// 		row_number--;
					
			// 	}
			// });
		});

		
		$(function () {
            $('.select2').select2()

            CKEDITOR.replace( 'content', {
                filebrowserBrowseUrl: '/js/ckfinder/ckfinder.html',
                filebrowserImageBrowseUrl: '/js/ckfinder/ckfinder.html?Type=Images',
                filebrowserUploadUrl: '/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
                filebrowserImageUploadUrl: '/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
                filebrowserWindowWidth : '1000',
                filebrowserWindowHeight : '700'
            } );
        	//Date picker
			$('#reservationdate').datetimepicker({
				format: 'L'
			});
		});
	</script>
	@endpush('scripts')
@endsection