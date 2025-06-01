@extends('layouts.app')
<style>
    .modal-lg {
        max-width: 90%;
    }
    .modal-body {
        height: 80vh;
    }
</style>

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h1>Client - {{ ucwords($client->name) }} Details</h1>
            </div>
            <div class="col-sm-6 d-none d-sm-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('client.index') }}">Clients</a></li>
                    <li class="breadcrumb-item">{{ ucwords($client->name) }} Details</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Client Details Card -->
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr><th>Reference</th><td>{{ $client->reference_no }}</td></tr>
                            <tr><th>Client Name</th><td>{{ $client->name }}</td></tr>
                            <tr><th>Short Name</th><td>{{ $client->short_name }}</td></tr>
                            <tr><th>Contact Number</th><td>{{ $client->contact_number }}</td></tr>
                            <tr><th>Email</th><td>{{ $client->email }}</td></tr>
                            <tr><th>Address</th><td>{{ $client->address }}</td></tr>
                            <tr>
                                <th>Contract</th>
                                <td>
                                    @if($client->contract)
                                        @php
                                            	$fileName = $client->contract;
																																													$fileUrl = $fileName ? url('/files/' . $client->id . '/' . $fileName) : null;
                                        @endphp
                                       	@if($fileUrl)
																																									<a href="#" data-toggle="modal" data-target="#pdfModal" style="font-weight: bold; text-decoration: underline;">
																																													{{ $fileName }}
																																									</a><br><br>
																																								@else
																																												<p>No Contract Found. Please upload.</p>
																																								@endif
                                    @else
                                        No Contract Uploaded
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer clearfix"></div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</section>

<!-- PDF Preview Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Contract Preview</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @if($fileUrl)
          <embed src="{{ $fileUrl }}" type="application/pdf" width="100%" height="100%" />
        @else
          <p>No contract file found.</p>
        @endif
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="{{ asset('vendors/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
<script src="{{ asset('vendors/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
@endpush
@endsection
