@extends('layouts.app')

@section('content')
<style>
    .modal-lg {
        max-width: 90%;
    }
    .modal-body {
        height: 80vh;
    }
</style>

<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h1>Client - {{ ucwords($client->name)}} Details</h1>
      </div>
      <div class="col-sm-6 d-none d-sm-block">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('client.index')}}">Clients</a></li>
          <li class="breadcrumb-item">Edit Details</li>
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
              <h3 class="card-title">Client Edit Form</h3>
            </div>
          </div>
          <div class="card-body">
            <form action="{{ route('client.update', $client->id)}}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PATCH')
              
              <div class="form-group row">
                <label class="col-lg-3 col-form-label">Reference No:</label>
                <div class="col-lg-9">  
                  <input type="text" disabled name="reference_no" value="{{ old('reference_no', $client->reference_no) }}" class="@error('reference_no') is-invalid @enderror form-control" placeholder="Reference No" >
                </div>
              </div>

              <div class="form-group row">
                <label class="col-lg-3 col-form-label">Client Name:</label>
                <div class="col-lg-9">  
                  <input type="text" name="name" value="{{ old('name', $client->name) }}" class="@error('name') is-invalid @enderror form-control" placeholder="Client Name" >
                </div>
              </div>

              <div class="form-group row">
                <label class="col-lg-3 col-form-label">Short Name:</label>
                <div class="col-lg-9">  
                  <input type="text" name="short_name" value="{{ old('short_name', $client->short_name) }}" class="@error('short_name') is-invalid @enderror form-control" placeholder="Client Short Name" >
                </div>
              </div>

              <div class="form-group row">
                <label class="col-form-label col-lg-3">Company Address</label>
                <div class="col-lg-9">
                  <textarea rows="3" name="address" class="@error('address') is-invalid @enderror form-control" placeholder="Address">{{ old('address', $client->address) }}</textarea>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-lg-3 col-form-label">Phone:</label>
                <div class="col-lg-9">  
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">+63</span>
                    </div>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $client->contact_number) }}" class="@error('contact_number') is-invalid @enderror form-control" placeholder="Phone" >
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
                    <input type="email" name="email" value="{{ old('email', $client->email) }}" class="@error('email') is-invalid @enderror form-control" placeholder="Email" >
                  </div>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-lg-3 col-form-label">Contract:</label>
                <div class="col-lg-9">
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

																			<input type="file" name="contract" class="@error('contract') is-invalid @enderror form-control" accept="application/pdf">
																			<small class="text-muted">Only PDF, max size: 10MB</small>
															</div>
              </div>

              <div class="text-right">
                <button type="submit" class="btn btn-success" style="box-shadow: 0 3px 3px -2px rgba(0,0,0,0.2), 0 3px 4px rgba(0,0,0,0.14), 0 1px 8px rgba(0,0,0,0.12);">
                  Save <i class="icon-paperplane ml-2"></i>
                </button>
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

<!-- Modal to Preview PDF -->
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
