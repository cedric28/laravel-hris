@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Employee - {{ ucwords($deployment->employee->last_name).", ".ucwords($deployment->employee->first_name)." ".ucwords($deployment->employee->middle_name) }} Details</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('deployment.index')}}">Employees</a></li>
			 										<li class="breadcrumb-item">{{ ucwords($deployment->employee->name) }} Details</li>
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
									<td>{{ $deployment->client->name }}</td>
								</tr>
								<tr>
									<th>Employee Fullname</th>
									<td>{{ $deployment->employee->name }}</td>
								</tr>

								<tr>
									<th>Position</th>
									<td>{{ $deployment->position }}</td>
								</tr>

								<tr>
									<th>Start Date</th>
									<td>{{ $deployment->start_date }}</td>
								</tr>

								<tr>
									<th>End Date</th>
									<td>{{ $deployment->end_date }}</td>
								</tr>
							@if( $deployment->schedule)
								<tr>
									<th>Schedule</th>
									<td>
										<p>{{ $deployment->schedule->slug }}</p>
										<p>
										TIME-IN: {{ $deployment->schedule->time_in }} 
										</p>
										<p>
										TIME-OUT: {{ $deployment->schedule->time_out }}
										</p>
									</td>
								</tr>
							@else
							<tr>
									<th>Schedule</th>
									<td>
										<p>No Schedule</p>
							
									</td>
								
								</tr>
								@endif
								
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