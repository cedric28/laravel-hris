@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Schedule - {{ ucwords($schedule->deployment->employee->name) }} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('user.index')}}">Schedules</a></li>
			  									<li class="breadcrumb-item">{{ ucwords($schedule->deployment->employee->name) }} Details</li>
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
									<th>Client</th>
									<td>{{ ucwords($schedule->deployment->client->name) }}</td>
								</tr>
								<tr>
									<th>Employee Fullname</th>
									<td>{{ ucwords($schedule->deployment->employee->name) }}</td>
								</tr>
								<tr>
									<th>Schedule</th>
									<td>{{ $schedule->slug }}</td>
								</tr>
								<tr>
									<th>TIME IN</th>
									<td>{{ $schedule->time_in }}</td>
								</tr>
								<tr>
									<th>TIME OUT</th>
									<td>{{ $schedule->time_out }}</td>
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