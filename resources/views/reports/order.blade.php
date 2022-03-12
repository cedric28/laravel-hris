@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Order Report</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Order Report</li>
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
                                    <a href="/generate-pdf-order-report" class="btn btn-danger" id="generateYearlySales">Generate PDF</a>
                                    <a href="/print-order-report" class="btn btn-primary" id="printOrderReport">Print</a>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>SKU</th>
                                                <th>PRODUCT NAME</th>
                                                <th>CURRENT QUANTITY</th>
                                                <th>MINIMUM QUANTITY</th>
                                                <th>STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orderReports as $order)
                                            <tr>
                                                <td class="textRight">{{ $order->sku }}</td>
                                                <td>{{ $order->product_name }}</td>
                                                <td class="textRight">{{$order->quantity }}</td>
                                                <td class="textRight">{{ $inventoryLevel[0]->re_stock }}</td>
                                                <td class="textCenter">
                                                    @if($order->quantity == 0)
                                                        <span title="Danger" class="badge bg-danger">DANGER</span>
                                                    @elseif($order->quantity < $inventoryLevel[0]->re_stock)
                                                        <span title="Danger" class="badge bg-danger">RE-STOCK</span>
                                                    @elseif($order->quantity == $inventoryLevel[0]->critical)
                                                        <span title="Danger" class="badge bg-warning">CRITICAL</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $orderReports->render() }}
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
    <script src="{{ asset('dist/js/jquery.printPage.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#printOrderReport').printPage();
        });
    </script>
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