@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Employee - New Record</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('deployment.index')}}">Employees</a></li>
			  <li class="breadcrumb-item">Add New Employee</li>
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
								<h3 class="card-title">Bulk Attendance Form <a class="ml-2 btn btn-primary" href="{{ route('download') }}" target="_blank"> <i class="nav-icon fas fa-file-excel"></i> Download Attendance Template</a></h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('bulkAttendance')}}" method="POST"  enctype="multipart/form-data">
								@csrf
		      <div class="form-group row">
         <label class="col-lg-3 col-form-label">File Attendance:</label>
         <div class="col-lg-5">	
          <input type="file" name="excel_file" value="{{ old('excel_file') }}" class="@error('excel_file') is-invalid @enderror form-control" placeholder="e.g Excel only" >
         </div>
         <div class="col-lg-4">	
           <button type="submit" class="btn btn-success">Save <i class="icon-paperplane ml-2"></i></button>
          </div>
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
    <script>

    </script>
    @endpush('scripts')
@endsection