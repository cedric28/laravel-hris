@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Payroll Setting - New Record</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('payroll.index')}}">Payroll Setting</a></li>
			  <li class="breadcrumb-item">Add New Payroll Setting</li>
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
								<h3 class="card-title">Payroll Form</h3>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form method="POST" action="{{ route('payroll.store') }}">
											@csrf

											{{-- Cutoff Mode Toggle --}}
											<div class="form-group row">
															<label class="col-lg-3 col-form-label">Cutoff Mode:</label>
															<div class="col-lg-9">
																			<select name="mode" id="mode" class="form-control">
																							<option value="manual" {{ old('mode') == 'manual' ? 'selected' : '' }}>Manual</option>
																							<option value="auto" {{ old('mode') == 'auto' ? 'selected' : '' }}>Auto Generate (Full Year)</option>
																			</select>
															</div>
											</div>

											{{-- Manual Section --}}
											<div id="manual-section">
															<div class="form-group row">
																			<label class="col-lg-3 col-form-label">Start Date:</label>
																			<div class="col-lg-9">
																							<input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
																			</div>
															</div>
															<div class="form-group row">
																			<label class="col-lg-3 col-form-label">End Date:</label>
																			<div class="col-lg-9">
																							<input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
																			</div>
															</div>
															<div class="form-group row">
																			<label class="col-lg-3 col-form-label">Description:</label>
																			<div class="col-lg-9">
																							<input type="text" name="description" class="form-control" value="{{ old('description') }}" required>
																			</div>
															</div>
											</div>

											{{-- Auto-Generation Section --}}
											<div id="auto-section" style="display: none;">
															<div class="form-group row">
																			<label class="col-lg-3 col-form-label">Select Year:</label>
																			<div class="col-lg-9">
																							<select name="year" class="form-control">
																											@for($i = now()->year; $i <= now()->year + 5; $i++)
																															<option value="{{ $i }}" {{ old('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
																											@endfor
																							</select>
																			</div>
															</div>
															<div class="form-group row">
																			<label class="col-lg-3 col-form-label">Cutoff Type:</label>
																			<div class="col-lg-9">
																							<select name="cutoff_type" class="form-control">
																											<option value="1-15">1-15 and 16-end</option>
																							</select>
																			</div>
															</div>
											</div>

											<div class="form-group row mb-3">
															<div class="col-lg-12 text-right">
																			<button type="submit" class="btn btn-primary">Save Payroll</button>
															</div>
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
				$('.select2').select2()
				//Date picker
				$('#start_date').datetimepicker({
					format: 'L'
				});

					$('#end_date').datetimepicker({
					format: 'L'
				});

			});
			
		</script>
		<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modeSelect = document.getElementById('mode');
        const manualSection = document.getElementById('manual-section');
        const autoSection = document.getElementById('auto-section');

        const manualInputs = manualSection.querySelectorAll('input');

        function toggleSections() {
            if (modeSelect.value === 'auto') {
                manualSection.style.display = 'none';
                autoSection.style.display = 'block';

                // Disable manual inputs
                manualInputs.forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });

            } else {
                manualSection.style.display = 'block';
                autoSection.style.display = 'none';

                // Enable manual inputs
                manualInputs.forEach(input => {
                    input.disabled = false;
                    input.setAttribute('required', true);
                });
            }
        }

        modeSelect.addEventListener('change', toggleSections);
        toggleSections();
    });
</script>

  @endpush('scripts')
@endsection