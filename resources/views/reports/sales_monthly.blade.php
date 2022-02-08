@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Monthly Yearly Report</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Monthly Yearly Reports</li>
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
                                        <a href="/generate-pdf-monthy-sales?start_date={{request('start_date')}}&end_date={{request('end_date')}}" class="btn btn-danger" id="generateYearlySales">Generate PDF</a>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <form action="{{ route('salesMonthly')}}">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="input-group date" id="start_date" data-target-input="nearest">
                                                    <input type="number" name="start_date" value="{{request('start_date')}}" class="@error('start_date') is-invalid @enderror form-control datetimepicker-input" data-target="#start_date"/>
                                                    <div class="input-group-append" data-target="#start_date" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                    <span class="ml-3 mt-2">to</span>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group date" id="end_date" data-target-input="nearest">
                                                    <input type="number" name="end_date" value="{{request('end_date')}}" class="@error('end_date') is-invalid @enderror form-control datetimepicker-input" data-target="#end_date"/>
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
                                            <tr style="text-align:center;">
                                                <th>INVOICE NO</th>
                                                <th>CUSTOMER NAME</th>
                                                <th>TOTAL PRICE</th>
                                                <th>TOTAL DISCOUNT</th>
                                                <th>TOTAL AMOUNT DUE</th>
                                                <th>DATE ADDED</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sales as $sale)
                                            <tr>
                                                <td>{{$sale->or_no}}</td>
                                                <td>{{$sale->customer_fullname}}</td>
                                                <td class="textRight">{{$sale->total_price}}</td>
                                                <td class="textRight">{{$sale->total_discount}}</td>
                                                <td class="textRight">{{$sale->total_amount_due}}</td>
                                                <td class="textCenter">{{$sale->created_at}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $sales->render() }}
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
			var d = new Date();
            var year = d.getFullYear() - 18;
            d.setFullYear(year);
        	//Date picker
			$('#start_date').datetimepicker({
                viewMode: 'months',
                format: 'MM',
                defaultDate: d
			});
            $('#end_date').datetimepicker({
                viewMode: 'months',
                format: 'MM',
                defaultDate: d,
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