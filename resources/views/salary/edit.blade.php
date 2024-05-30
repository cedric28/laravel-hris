@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Compensation - {{ ucwords($deployment->employee->name)}} - {{ ucwords($deployment->client->name)}} Company</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('deployment.index')}}">Employees</a></li>
			  									<li class="breadcrumb-item">Compensation Details</li>
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
								<h3 class="card-title">Compensation Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
                            <form action="{{ route('salary.update', $salary->id)}}" method="POST">
                            @csrf
                            @method('PATCH')
                                <div id="accordion">
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseSalary" aria-expanded="true">
                                                    Compensation Information
                                                </a>
                                            </h4>
                                        </div>
                                    
                                        <div id="collapseSalary" class="collapse show" data-parent="#accordion" style="">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">BASIC SALARY:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="basic_salary" value="{{ old('sss',$salary->basic_salary) }}" class="@error('basic_salary') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">BASE RATE:</label>
                                                    <div class="col-lg-9">	
                                                        <select name="rate_base" class="form-control">
                                                            <option value="">Please select</option>
                                                            @foreach ($baseRate as $base)
                                                            <option value="{{ $base['value'] }}"{{ ($salary->rate_base == old('rate_base',$base['value'])) ? ' selected' : '' }}>
                                                                    {{ $base['label'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseOne" aria-expanded="true">
                                                    Government Mandated
                                                </a>
                                            </h4>
                                        </div>
                                        
                                        <div id="collapseOne" class="collapse show" data-parent="#accordion" style="">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">SSS:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="sss" value="{{ old('sss',$salary->sss) }}" class="@error('sss') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">TAX (%):</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="tax" value="{{ old('tax',$salary->tax) }}" class="@error('tax') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">PAG-IBIG:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="pagibig" value="{{ old('pagibig',$salary->pagibig) }}" class="@error('pagibig') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">PHILHEALTH:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="philhealth" value="{{ old('philhealth',$salary->philhealth) }}" class="@error('philhealth') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                            <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseTwo" aria-expanded="true">
                                                Allowances
                                            </a>
                                            </h4>
                                        </div>
                                        <div id="collapseTwo" class="collapse show" data-parent="#accordion" style="">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">MEAL ALLOWANCE:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="meal_allowance" value="{{ old('meal_allowance',$salary->meal_allowance) }}" class="@error('meal_allowance') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">LAUNDRY ALLOWANCE:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="laundry_allowance" value="{{ old('laundry_allowance',$salary->laundry_allowance) }}" class="@error('laundry_allowance') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">TRANSPORTATION ALLOWANCE:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="transportation_allowance" value="{{ old('transportation_allowance',$salary->transportation_allowance) }}" class="@error('transportation_allowance') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">COLA:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="cola" value="{{ old('cola',$salary->cola) }}" class="@error('cola') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseThree" aria-expanded="true">
                                                    Other Deduction
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseThree" class="collapse show" data-parent="#accordion" style="">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">UNIFORM:</label>
                                                    <div class="col-lg-9">	
                                                        <input type="text" name="uniform" value="{{ old('uniform',$salary->uniform) }}" class="@error('uniform') is-invalid @enderror form-control" placeholder="0.00" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							
								<div class="text-right">
									<button type="submit" class="btn btn-success">Save <i class="icon-paperplane ml-2"></i></button>
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
	<div id="confirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to move this data to archive?</h4>
                </div>
                <div class="modal-footer">
                 <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
	<!-- /page content -->
        @push('scripts')
        <!-- Javascript -->

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
              $('#leavetime').datetimepicker({
                        format: 'LT'
                })

                $('#startdate').datetimepicker({
                format: 'L'
            });

var tableActiveLeaves = $('#employee_leaves').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeLeaves') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{
                        "_token":"<?= csrf_token() ?>",
                        "deployment_id": "<?= $deployment->id ?>"
                    }
                },
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        "extend": 'collection',
                        "text": 'Export',
                        "buttons": [
                            {
                                "extend": 'csv',
                                'title' : 'Employee Leaves-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'pdf',
                                'title' : 'Employee Leaves-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            },
                            {
                                "extend": 'print',
                                'title' : 'Employee Leaves-List',
                                "exportOptions": {
                                    "columns": [0,1,2]
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"leave_type"},
                    {"data":"leave_date"},
                    {"data":"leave_time"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [{
					"targets": [1,2],   // target column
					"className": "textCenter",
				}]
            });
            
   
            var leave_id;
            $(document).on('click', '#delete', function(){
                console.log
                leave_id = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function(){
                $.ajax({
                    url:"/leaves/destroy/"+leave_id,
                    beforeSend:function(){
                        $('#ok_button').text('Deleting...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModal').modal('hide');
                            tableActiveLeaves.ajax.reload();
                        }, 2000);
                    }
                })
            });

            
            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                $('.table:visible').each( function(e) {
                    $(this).DataTable().columns.adjust().responsive.recalc();
                });
            });
            </script>

        @endpush('scripts')
@endsection