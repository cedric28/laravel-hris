@extends('layouts.app')

@section('content')
    <section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Point Of Sale</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">POS</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div id="cart"></div>
     
    </section>
    @push('scripts')
    <script src="/js/app.js"></script>
    <script>
			$(function () {
				$('.select2').select2()

        $("#product-id").on('change', function(e) {
          // console.log($(this).select2('data')[0]);
          console.log($(this).attr('data-product'));
        })
				//Date picker
				$('#reservationdate').datetimepicker({
					format: 'L'
				});

				$('#expireddate').datetimepicker({
					format: 'L'
				});
			});
		</script>
    @endpush('scripts')
@endsection

