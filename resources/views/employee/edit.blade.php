@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Employee - {{ ucwords($employee->name)}} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('employee.index')}}">Employees</a></li>
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
								<h3 class="card-title">Employee Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('employee.update', $employee->id)}}" method="POST">
								@csrf
								@method('PATCH')
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Reference No:</label>
									<div class="col-lg-9">	
										<input type="text" disabled="disabled" name="reference_no" value="{{ old('reference_no', $employee->reference_no) }}" class="@error('reference_no') is-invalid @enderror form-control" placeholder="Reference No" >
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Employee Fullname:</label>
									<div class="col-lg-9">	
										<input type="text" name="name" value="{{ old('name', $employee->name) }}" class="@error('name') is-invalid @enderror form-control" placeholder="Employee Fullname" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Nickname:</label>
									<div class="col-lg-9">	
										<input type="text" name="nickname" value="{{ old('nickname', $employee->nickname) }}" class="@error('nickname') is-invalid @enderror form-control" placeholder="Nickname" >
									</div>
								</div>
								<div class="form-group row">
									<label class="col-form-label col-lg-3">Address</label>
									<div class="col-lg-9">
										<textarea rows="3" cols="3" name="address" class="@error('address') is-invalid @enderror form-control" placeholder="Address">{{ old('address', $employee->address) }}</textarea>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Phone:</label>
									<div class="col-lg-9">	
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">+63</span>
											</div>
											<input type="text" name="contact_number" value="{{ old('contact_number', $employee->contact_number) }}" class="@error('contact_number') is-invalid @enderror form-control" placeholder="Phone" >
										</div>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Email:</label>
									<div class="col-lg-9">	
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fas fa-envelope"></i></span>
											</div>
											<input type="email" name="email" value="{{ old('email', $employee->email) }}" class="@error('email') is-invalid @enderror form-control" placeholder="Email" >
										</div>
									</div>
								</div>

								<div class="card card-primary">
									<div class="card-header">
										Employment History
									</div>
									<div class="card-body">
										<table class="table" id="products_table">
											<thead>
												<tr>
													<th>TITLE</th>
													<th>EMPLOYMENT TYPE</th>
													<th>COMPANY NAME</th>
													<th>LOCATION</th>
													<th>START DATE</th>
													<th>END DATE</th>
													<th>INDUSTRY</th>
													<th>DESCRIPTION</th>
												</tr>
											</thead>
											<tbody>
												@foreach (old('products', ['']) as $index => $oldEmploymentType)
													<tr id="product{{ $index }}">
													<td>
															<input type="text" name="title[]" class="form-control" value="{{ old('title.' . $index) ?? '' }}" />
														</td>
													<td>
															<select name="employmentTypes[]" class="form-control">
																<option value="">Please select</option>
																@foreach ($employmentTypes as $employmentType)
																	<option value="{{ $employmentType->id }}"{{ $oldEmploymentType == $employmentType->id ? ' selected' : '' }}>
																		{{ $employmentType->name }}
																	</option>
																@endforeach
															</select>
														</td>
														<td>
															<input type="text" name="company[]" class="form-control" value="{{ old('company.' . $index) ?? '' }}" />
														</td>
														<td>
															<input type="text" name="location[]" class="form-control" value="{{ old('location.' . $index) ?? '' }}" />
														</td>
														<td>
															<div class="input-group date" id="startdate" data-target-input="nearest">
																<input type="text" name="startdate[]" class="form-control datetimepicker-input" data-target="#startdate"/>
																<div class="input-group-append" data-target="#startdate" data-toggle="datetimepicker">
																	<div class="input-group-text"><i class="fa fa-calendar"></i></div>
																</div>
															</div>
														</td>
														<td>
															<div class="input-group date" id="enddate" data-target-input="nearest">
																<input type="text" name="enddate[]" class="form-control datetimepicker-input" data-target="#enddate"/>
																<div class="input-group-append" data-target="#enddate" data-toggle="datetimepicker">
																	<div class="input-group-text"><i class="fa fa-calendar"></i></div>
																</div>
															</div>
														</td>
														<td>
															<select name="industries[]" class="form-control">
																<option value="">Please select</option>
																@foreach ($industries as $industry)
																	<option value="{{ $industry->id }}"{{ $oldEmploymentType == $industry->id ? ' selected' : '' }}>
																		{{ $industry->name }}
																	</option>
																@endforeach
															</select>
														</td>
														<td>
														<textarea rows="3" cols="3" name="description[]" class="form-control" placeholder="Address">{{ old('location.' . $index) ?? '' }}</textarea>
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
      
        <script src="{{ asset('vendors/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/jquery.scrollbar/jquery.scrollbar.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/jquery-scrollLock/jquery-scrollLock.min.js') }}"></script>

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
			$('#startdate').datetimepicker({
				format: 'L'
			});
			$('#enddate').datetimepicker({
				format: 'L'
			});
		});
	</script>
        @endpush('scripts')
@endsection