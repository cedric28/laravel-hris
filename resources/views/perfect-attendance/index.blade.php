@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Perfect Attendance</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Perfect Attendance</li>
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
                  <div class="row">
                  <h3 class="card-title">Attendance for the Month of {{ $currentMonth }} {{  $year }}</h3>
                  </div>
              </div>
						<!-- /.card-header -->
						<div class="card-body">
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                   
                                </div>
                                <div class="card-body">
                                    <table class="table table-hover table-striped" id="logs">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>FULLNAME</th>
                                                <th>COMPANY</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          
                                           <tr>
                                             <td></td>
                                             <td></td>
                                           </tr>
                                          
                                        </tbody>
                                    </table>
                                </div>
                            </div>
						</div>
						<!-- /.card-body -->
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
		<!-- DataTables  & Plugins -->
		<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
		<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
		<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
		<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
		<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
		<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
		<script>

          

		</script>
        @endpush('scripts')
@endsection