@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Daily Preventive Maintenance Report</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Daily Preventive Maintenance Report</li>
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
						</div>
						<!-- /.card-header -->
						<div class="card-body">
                            <div class="row col-md-12">
                                <div class="col-md-7">
                                    <div class="col-md-4">
                                        <a href="/generate-pdf-daily-preventive?start_date={{request('start_date')}}&end_date={{request('end_date')}}" class="btn btn-danger" id="generateYearlySales">Generate PDF</a>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <form action="{{ route('dailyPreventive')}}">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="input-group date" id="start_date" data-target-input="nearest">
                                                    <input type="text" name="start_date" class="@error('start_date') is-invalid @enderror form-control datetimepicker-input" value="{{request('start_date')}}" data-target="#start_date"/>
                                                    <div class="input-group-append" data-target="#start_date" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                    <span class="ml-3 mt-2">to</span>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group date" id="end_date" data-target-input="nearest">
                                                    <input type="text" name="end_date" value="{{request('end_date')}}" class="@error('end_date') is-invalid @enderror form-control datetimepicker-input" data-target="#end_date"/>
                                                    <div class="input-group-append" data-target="#end_date" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-outline-primary" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Qty</th>
                                                <th>Expiration Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($deliveries as $stock)
                                            <tr>
                                                <td>{{ $stock->product->product_name }}</td>
                                                <td>{{$stock->qty }}</td>
                                                <td>{{ $stock->expired_at }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $deliveries->render() }}
                                </div>
                            </div>
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
		$(function () {
        	//Date picker
			$('#start_date').datetimepicker({
                format: "L"
            });
            $('#end_date').datetimepicker({
                format: "L",
                useCurrent: false 
			});

            $("#start_date").on("dp.change", function (e) {
                $('#end_date').data("DateTimePicker").minYear(e.date);
            });
            $("#end_date").on("dp.change", function (e) {
                $('#start_date').data("DateTimePicker").maxYear(e.date);
            });

        });
	</script>
	@endpush('scripts')
@endsection