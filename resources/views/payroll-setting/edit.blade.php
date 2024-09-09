@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Payroll Setting - {{ ucwords($payroll->description)}} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('payroll.index')}}">Payroll Setting</a></li>
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
								<h3 class="card-title">Payroll Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('payroll.update', $payroll->id)}}" method="POST" >
								@csrf
								@method('PATCH')
									<div class="form-group row">
									<label class="col-lg-3 col-form-label">Start Date</label>
									<div class="col-lg-9">	
											<div class="input-group date" id="start_date" data-target-input="nearest">
													<input type="text" name="start_date"  value="{{ old('start_date', $startDate) }}" class="form-control datetimepicker-input" data-target="#start_date"/>
														<div class="input-group-append" data-target="#start_date" data-toggle="datetimepicker">
															<div class="input-group-text"><i class="fa fa-calendar"></i></div>
														</div>
										</div>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">End Date</label>
									<div class="col-lg-9">	
											<div class="input-group date" id="end_date" data-target-input="nearest">
													<input type="text" name="end_date"  value="{{ old('end_date', $endDate) }}" class="form-control datetimepicker-input" data-target="#end_date"/>
														<div class="input-group-append" data-target="#end_date" data-toggle="datetimepicker">
															<div class="input-group-text"><i class="fa fa-calendar"></i></div>
														</div>
										</div>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Description:</label>
									<div class="col-lg-9">	
										<input type="text" name="description" value="{{ old('description', $payroll->description) }}" class="@error('description') is-invalid @enderror form-control" placeholder="Description" >
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