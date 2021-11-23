@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>User - {{ ucwords($user->first_name) }} {{ ucwords($user->last_name) }} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('user.index')}}">Users</a></li>
			  <li class="breadcrumb-item">{{ ucwords($user->first_name) }} {{ ucwords($user->last_name) }}Details</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
      	<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- About Me Box -->
					<div class="card">
						<div class="card-header">
							
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<table class="table table-bordered">
								<tr>
									<th>Firstname</th>
									<td>{{ $user->first_name }}</td>
								</tr>
								<tr>
									<th>Last Name</th>
									<td>{{ $user->last_name }}</td>
								</tr>
								<tr>
									<th>Email</th>
									<td>{{ $user->email }}</td>
								</tr>
								<tr>
									<th>Role</th>
									<td>{{ $user->role->name }}</td>
								</tr>
							</table>
						</div>
						<!-- /.card-body -->
						<div class="card-footer clearfix">
							
						</div>
					</div>
					<!-- /.card -->
				</div>
			</div>
		</div>	
	</section>
        @push('scripts')
        <!-- Javascript -->
      
        @endpush('scripts')
@endsection