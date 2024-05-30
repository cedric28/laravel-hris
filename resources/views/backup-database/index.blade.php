@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Backup Database</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
      	<div class="container-fluid">
		 <div class="row">

                 
                  
                    <div class="card">
                  <div class="position-relative" style="display: flex;align-items: center;justify-content: center;flex-direction: column;">
                <img src="{{ asset('dist/img/backupdatabase.png') }}" alt="Photo 1" class="img-fluid">
                  <div class="ribbon-wrapper ribbon-lg">
                  <div class="ribbon bg-success text-lg">
                  Database
                  </div>
                  </div>
              

                      <div class="card-body">
                        <h5 class="card-title">{{ $tableCounts }} tables available</h5>
                        <p class="card-text">Click the button to backup database.</p>
                      </div>
                      <div class="card-footer">
                        <button id="create-backup-btn" class="btn btn-primary btn-block btn-flat w-100">Backup</button>
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
    $(document).ready(function() {
        $('#create-backup-btn').on('click', function() {
            $.ajax({
                type: 'POST',
                url: "<?= route('backupDatabase') ?>",
                data: {"_token":"<?= csrf_token() ?>"},
                success: function(response) {
                  console.log('response',response)
                    if (response.success) {
                        swal.fire({
                            title: 'Success!',
                            text: response.message,
                            type: 'uccess',
                        });
                    } else {
                        swal.fire({
                            title: 'Error!',
                            text: response.message,
                            type: 'error',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while creating the backup.',
                        type: 'error',
                    });
                }
            });
        });
    });
</script>
        @endpush('scripts')
@endsection