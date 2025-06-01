@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h1>Client - New Record</h1>
            </div>
            <div class="col-sm-6 d-none d-sm-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('client.index')}}">Clients</a></li>
                    <li class="breadcrumb-item">Add New Client</li>
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
                            <h3 class="card-title">Client Form</h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('client.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Client Name:</label>
                                <div class="col-lg-9">
                                    <input type="text" name="name" value="{{ old('name') }}"
                                        class="@error('name') is-invalid @enderror form-control"
                                        placeholder="Client Name">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Short Name:</label>
                                <div class="col-lg-9">
                                    <input type="text" name="short_name" value="{{ old('short_name') }}"
                                        class="@error('short_name') is-invalid @enderror form-control"
                                        placeholder="Client Short Name">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-lg-3">Company Address</label>
                                <div class="col-lg-9">
                                    <textarea rows="3" name="address"
                                        class="@error('address') is-invalid @enderror form-control"
                                        placeholder="Address">{{ old('address') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Phone:</label>
                                <div class="col-lg-9">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">+63</span>
                                        </div>
                                        <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                                            class="@error('contact_number') is-invalid @enderror form-control"
                                            placeholder="Phone">
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
                                        <input type="email" name="email" value="{{ old('email') }}"
                                            class="@error('email') is-invalid @enderror form-control"
                                            placeholder="Email">
                                    </div>
                                </div>
                            </div>

                            {{-- PDF Upload with Preview --}}
																												<div class="form-group row">
																															<label class="col-lg-3 col-form-label">Contract:</label>
																															<div class="col-lg-9">

																																			{{-- Filename displayed here and clickable --}}
																																			<p id="pdfFileName" style="display: none; font-weight: bold;">
																																							<a href="#" data-toggle="modal" data-target="#previewPdfModal" id="pdfFileLink"></a>
																																			</p>

																																			{{-- File input --}}
																																			<input type="file" id="contractInput" name="contract" accept="application/pdf"
																																							class="@error('contract') is-invalid @enderror form-control">
																																			<small class="text-muted">Only PDF allowed. Max 10MB</small>
																															</div>
																												</div>

																												{{-- PDF Preview Link --}}

                            <div class="text-right">
                                <button type="submit" class="btn btn-success"
                                    style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 
                                    0px 3px 4px 0px rgba(0, 0, 0, 0.14), 
                                    0px 1px 8px 0px rgba(0, 0, 0, 0.12);">
                                    Save <i class="icon-paperplane ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PDF Preview Modal -->
<div class="modal fade" id="previewPdfModal" tabindex="-1" role="dialog" aria-labelledby="previewPdfModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <iframe id="pdfViewer" src="" width="100%" height="600px" style="border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.getElementById("contractInput");
        const pdfFileNameContainer = document.getElementById("pdfFileName");
        const pdfFileLink = document.getElementById("pdfFileLink");
        const pdfViewer = document.getElementById("pdfViewer");

        fileInput.addEventListener("change", function () {
            const file = fileInput.files[0];

            if (file && file.type === "application/pdf") {
                const fileURL = URL.createObjectURL(file);
                pdfViewer.src = fileURL;

                pdfFileLink.textContent = file.name;
                pdfFileLink.href = "#";
                pdfFileNameContainer.style.display = "block";
            } else {
                pdfViewer.src = "";
                pdfFileLink.textContent = "";
                pdfFileNameContainer.style.display = "none";
            }
        });
    });
</script>
@endpush

