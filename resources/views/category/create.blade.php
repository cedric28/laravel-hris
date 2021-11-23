@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Category - New Record</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('category.index')}}">Categories</a></li>
			  <li class="breadcrumb-item">Add New Category</li>
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
								<h3 class="card-title">Category Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('category.store')}}" method="POST" enctype="multipart/form-data">
								@csrf
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Category:</label>
									<div class="col-lg-9">	
										<input type="text" name="category_name" value="{{ old('category_name') }}" class="@error('category_name') is-invalid @enderror form-control" placeholder="Category Name" >
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
        
        @endpush('scripts')
@endsection