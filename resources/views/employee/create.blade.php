@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Applicant - New Record</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('employee.index')}}">Applicants</a></li>
			           <li class="breadcrumb-item">Add New Applicant</li>
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
								<h3 class="card-title">Applicant Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form action="{{ route('employee.store')}}" method="POST" enctype="multipart/form-data">
								@csrf
												<div class="card card-success">
													<div class="card-header">
														Basic Information
													</div>
													<div class="card-body">
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Firstname:</label>
															<div class="col-lg-9">	
																<input type="text" name="first_name" value="{{ old('first_name') }}" class="@error('first_name') is-invalid @enderror form-control" placeholder="e.g Juan" >
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Middlename:</label>
															<div class="col-lg-9">	
																<input type="text" name="middle_name" value="{{ old('middle_name') }}" class="@error('middle_name') is-invalid @enderror form-control" placeholder="e.g Santiago" >
															</div>
														</div>
															<div class="form-group row">
															<label class="col-lg-3 col-form-label">Lastname:</label>
															<div class="col-lg-9">	
																<input type="text" name="last_name" value="{{ old('last_name') }}" class="@error('last_name') is-invalid @enderror form-control" placeholder="e.g Dela Cruz" >
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Nickname:</label>
															<div class="col-lg-9">	
																<input type="text" name="nickname" value="{{ old('nickname') }}" class="@error('nickname') is-invalid @enderror form-control" placeholder="Nickname" >
															</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Date of Birth:</label>
																<div class="col-lg-9">
																	<div class="input-group date" id="birthdate" data-target-input="nearest">
																			<input type="text" name="birthdate" value="{{ old('birthdate') }}" placeholder="e.g 2022-08-20" onkeydown="return false;" class="@error('birthdate') is-invalid @enderror form-control datetimepicker-input" data-target="#reservationdate"/>
																			<div class="input-group-append" data-target="#birthdate" data-toggle="datetimepicker">
																				<div class="input-group-text"><i class="fa fa-calendar"></i></div>
																			</div>
																	</div>
																</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Sex:</label>
															<div class="col-lg-9">	
																<select name="gender_id" class="form-control">
																	<option value="">Please select</option>
																	@foreach ($gender as $gend)
																		<option value="{{ $gend->id }}" {{ $gend->id == old('gender_id')  ? ' selected' : '' }}>
																			{{ $gend->name }}
																		</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Civil Status:</label>
															<div class="col-lg-9">	
																<select name="civil_status_id" class="form-control">
																	<option value="">Please select</option>
																	@foreach ($civilStatus as $civilStat)
																		<option value="{{ $civilStat->id }}"{{ $civilStat->id == old('civil_status_id')  ? ' selected' : '' }}>
																			{{ $civilStat->name }}
																		</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Nationality:</label>
															<div class="col-lg-9">	
																<input type="text" name="nationality" value="{{ old('nationality') }}" class="@error('nationality') is-invalid @enderror form-control" placeholder="e.g Filipino" >
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Religion:</label>
															<div class="col-lg-9">	
																<input type="text" name="religion" value="{{ old('religion') }}" class="@error('religion') is-invalid @enderror form-control" placeholder="e.g Catholic" >
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Phone:</label>
															<div class="col-lg-9">	
																<div class="input-group mb-3">
																	<div class="input-group-prepend">
																		<span class="input-group-text">+63</span>
																	</div>
																	<input type="text" name="contact_number" value="{{ old('contact_number') }}" class="@error('contact_number') is-invalid @enderror form-control" placeholder="Phone" >
																</div>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Email:</label>
															<div class="col-lg-9">	
																<div class="input-group mb-3">
																	<div class="input-group-prepend">
																		<span class="input-group-text"><i class="fas fa-envelope"></i></span>
																	</div>
																	<input type="email" name="email" value="{{ old('email') }}" class="@error('email') is-invalid @enderror form-control" placeholder="Email" >
																</div>
															</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Present Address:</label>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Unit/Room No./Floor Building Name:</label>
																<div class="col-lg-9">	
																	<input type="text" name="unit" value="{{ old('unit') }}" class="@error('unit') is-invalid @enderror form-control" placeholder="e.g 3" >
																</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Lot/Block/House No.:</label>
																<div class="col-lg-9">	
																	<input type="text" name="lot_block" value="{{ old('lot_block') }}" class="@error('lot_block') is-invalid @enderror form-control" placeholder="e.g Lot 7 blk 2 293" >
																</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Street:</label>
																<div class="col-lg-9">	
																	<input type="text" name="street" value="{{ old('street') }}" class="@error('street') is-invalid @enderror form-control" placeholder="e.g Camachile" >
																</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Subdivision/Village:</label>
																<div class="col-lg-9">	
																	<input type="text" name="subdivision" value="{{ old('subdivision') }}" class="@error('subdivision') is-invalid @enderror form-control" placeholder="e.g Almara" >
																</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">City/Municipality:</label>
																<div class="col-lg-9">	
																	<input type="text" name="municipality" value="{{ old('municipality') }}" class="@error('municipality') is-invalid @enderror form-control" placeholder="e.g Balanga" >
																</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Barangay:</label>
																<div class="col-lg-9">	
																	<input type="text" name="barangay" value="{{ old('barangay') }}" class="@error('barangay') is-invalid @enderror form-control" placeholder="e.g San Jose" >
																</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Province:</label>
																<div class="col-lg-9">	
																	<input type="text" name="province" value="{{ old('province') }}" class="@error('province') is-invalid @enderror form-control" placeholder="e.g Bataan" >
																</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Zip code:</label>
																<div class="col-lg-9">	
																	<input type="text" name="zip" value="{{ old('zip') }}" class="@error('zip') is-invalid @enderror form-control" placeholder="e.g 2100" >
																</div>
														</div>
													</div>
												</div>
												<div class="card card-success">
													<div class="card-header">
														In-Case of Emergency Information
													</div>
													<div class="card-body">
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Emergency Contact Name:</label>
															<div class="col-lg-9">	
																<input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="@error('emergency_contact_name') is-invalid @enderror form-control" placeholder="Fullname" >
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Relationship:</label>
															<div class="col-lg-9">	
																<input type="text" name="emergency_relationship" value="{{ old('emergency_relationship') }}" class="@error('emergency_relationship') is-invalid @enderror form-control" placeholder="Relationship" >
															</div>
														</div>

														<div class="form-group row">
															<label class="col-lg-3 col-form-label">Phone:</label>
															<div class="col-lg-9">	
																<div class="input-group mb-3">
																	<div class="input-group-prepend">
																		<span class="input-group-text">+63</span>
																	</div>
																	<input type="text" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}" class="@error('emergency_contact_number') is-invalid @enderror form-control" placeholder="Phone" >
																</div>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-form-label col-lg-3">Address</label>
															<div class="col-lg-9">
																<textarea rows="3" cols="3" name="emergency_address" class="@error('emergency_address') is-invalid @enderror form-control" placeholder="Address">{{ old('emergency_address') }}</textarea>
															</div>
														</div>
													</div>
												</div>
												<div class="card card-success">
													<div class="card-header">
														Government Information
													</div>
													<div class="card-body">
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">SSS:</label>
															<div class="col-lg-5">	
																<input type="text" name="sss" value="{{ old('sss') }}" class="@error('sss') is-invalid @enderror form-control" placeholder="SSS" >
															</div>
															<div class="col-lg-4">	
																	<input type="file" id="sss_file" name="sss_file"value="{{ old('sss_file') }}" class="@error('sss_file') is-invalid @enderror form-control" placeholder="SSS File" >
																</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">PAG-IBIG:</label>
															<div class="col-lg-5">	
																<input type="text" name="pagibig" value="{{ old('pagibig') }}" class="@error('pagibig') is-invalid @enderror form-control" placeholder="PAG-IBIG" >
															</div>
																<div class="col-lg-4">	
																	<input type="file" id="pagibig_file" name="pagibig_file"value="{{ old('pagibig_file') }}" class="@error('pagibig_file') is-invalid @enderror form-control" placeholder="Pagibig File" >
																</div>
														</div>
														<div class="form-group row">
																<label class="col-lg-3 col-form-label">Philhealth:</label>
															
																<div class="col-lg-5">	
																<input type="text" name="philhealth" value="{{ old('philhealth') }}" class="@error('philhealth') is-invalid @enderror form-control" placeholder="Philhealth" >
															</div>
																<div class="col-lg-4">	
																	<input type="file" id="philhealth_file" name="philhealth_file"value="{{ old('philhealth_file') }}" class="@error('philhealth_file') is-invalid @enderror form-control" placeholder="Philhealth File" >
																</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">TIN:</label>
															<div class="col-lg-5">	
																<input type="text" name="tin" value="{{ old('tin') }}" class="@error('tin') is-invalid @enderror form-control" placeholder="TIN" >
															</div>
																<div class="col-lg-4">	
																	<input type="file" id="tin_file" name="tin_file"value="{{ old('tin_file') }}" class="@error('tin_file') is-invalid @enderror form-control" placeholder="TIN File" >
																</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">NBI:</label>
															<div class="col-lg-5">	
																<input type="text" name="nbi" value="{{ old('nbi') }}" class="@error('nbi') is-invalid @enderror form-control" placeholder="NBI No." >
															</div>
																<div class="col-lg-4">	
																	<input type="file" id="nbi_file" name="nbi_file"value="{{ old('nbi_file') }}" class="@error('nbi_file') is-invalid @enderror form-control" placeholder="NBI File" >
																</div>
														</div>
													</div>
												</div>


								<div class="text-right">
									<button type="submit" class="btn btn-success" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);">Next <i class="icon-paperplane ml-2"></i></button>
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
        @push('scripts')

								<script>
									$(function () {
									var bindDatePicker = function() {
											$("#birthdate").datetimepicker({
												showClear: true,
												showClose: true,
												allowInputToggle: true,
												useCurrent: false,
												ignoreReadonly: true,
												format:'YYYY-MM-DD',
												icons: {
													time: "fas fa-clock",
													date: "fas fa-calendar",
													up: "fas fa-chevron-up",
													down: "fas fa-chevron-down"
												}
											});
									}
								
									bindDatePicker();
								});
								</script>

        @endpush('scripts')
@endsection