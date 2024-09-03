@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Holiday - {{ ucwords($holiday->name)}} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('holiday-setting.index')}}">Holidays</a></li>
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
								<h3 class="card-title">Holiday Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('holiday-setting.update', $holiday->id)}}" method="POST" >
								@csrf
								@method('PATCH')
										<div class="form-group row">
									<label class="col-lg-3 col-form-label">Holiday Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="name" value="{{ old('name', $holiday->name) }}" class="@error('name') is-invalid @enderror form-control" placeholder="Holiday Name" >
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Holiday Date</label>
									<div class="col-lg-9">	
											<div class="input-group date" id="holidays" data-target-input="nearest">
													<input type="text" name="holiday"  value="{{ old('holiday',$date) }}" class="form-control datetimepicker-input" data-target="#holidays"/>
														<div class="input-group-append" data-target="#holidays" data-toggle="datetimepicker">
															<div class="input-group-text"><i class="fa fa-calendar"></i></div>
														</div>
										</div>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Percentage (%):</label>
									<div class="col-lg-9">	
													<input type="text" name="percentage" value="{{ old('percentage', $holiday->percentage) }}" class="@error('percentage') is-invalid @enderror form-control" placeholder="0.00" >
									</div>
								</div>

								<div class="text-right">
									<button type="submit" class="btn btn-success" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">Save <i class="icon-paperplane ml-2"></i></button>
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
									        <script>
			$(function () {
				$('.select2').select2()
				//Date picker
				$('#holidays').datetimepicker({
					format: 'L'
				});


			});
		</script>
      
        <script src="{{ asset('vendors/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/jquery.scrollbar/jquery.scrollbar.min.js') }}"></script>
        <script src="{{ asset('vendors/bower_components/jquery-scrollLock/jquery-scrollLock.min.js') }}"></script>
        @endpush('scripts')
@endsection