@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>General Deductions - {{ ucwords($deduction->name)}} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('general-deductions.index')}}">General Deductions</a></li>
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
								<h3 class="card-title">General Deduction Edit Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('general-deductions.update', $deduction->id)}}" method="POST" >
								@csrf
								@method('PATCH')
										<div class="form-group row">
									<label class="col-lg-3 col-form-label">Name:</label>
									<div class="col-lg-9">	
										<input type="text" name="name" value="{{ old('name', $deduction->name) }}" class="@error('name') is-invalid @enderror form-control" placeholder="Holiday Name" >
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Amount:</label>
									<div class="col-lg-9">	
													<input type="text" name="amount" value="{{ old('amount', $deduction->amount) }}" class="@error('amount') is-invalid @enderror form-control" placeholder="0.00" >
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