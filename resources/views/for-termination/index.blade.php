@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Employee for Termination</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Employee for Termination</li>
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
              <h3 class="card-title">Employee for Termination </strong></h3>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
                            <div class="card card-success card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Active For Termination</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                                            <table class="table table-hover table-striped" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);" id="termination">
                                                <thead>
                                                    <tr style="text-align:center;">
                                                        <th>EMPLOYEE NAME</th>
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
            var base64Logo = @json($base64Logo);
            var currentUser = @json(Auth::user()->first_name . ' ' . Auth::user()->last_name);
            var table = $('#termination').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeForTermination') ?>",
                    "dataType":"json",
                    "type":"POST",
                    "data":{"_token":"<?= csrf_token() ?>"}
                },
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        "extend": 'collection',
                        "text": 'Export',
                        "buttons": [{
                            extend: 'csv',
                            filename: 'List of Employees for Termination as of <?= date('F j, Y') ?>',
                            exportOptions: {
                                columns: [0, 1]
                            },
                            customize: function(csv) {

                                function centerText(text, width = 80) {
                                    let pad = Math.floor((width - text.length) / 2);
                                    if (pad < 0) pad = 0;
                                    return ' '.repeat(pad) + text;
                                }

                                var headerLines = [
                                    centerText('REVMAN AGENCY'),
                                    centerText(
                                        'Address: 2nd Flr Medical Arts Bldg 1., University Of Perpetual Help System - Laguna Compound,'
                                    ),
                                    centerText(
                                        'Old 3 National Highway Brgy. Sto Nino Binan City Laguna'),
                                    centerText('Email: revman.applicant@gmail.com'),
                                    centerText('Cellphone: +639359722646 / +639564729639'),
                                    '',

                                    'List of Employees for Termination as of <?= date('F j, Y') ?>',
                                    ''
                                ].join('\n');


                                var footerLines = [
                                    '',
                                    'Prepared By: <?= $currentUser ?>',
                                    ''
                                ].join('\n');

                                return headerLines + csv + footerLines;
                            }
                            },
                            {
                                extend: 'pdfHtml5',
                                title: '',
                                exportOptions: {
                                    columns: [0, 1]
                                },
                                filename: 'List of Employees for Termination as of {{ \Carbon\Carbon::now()->toFormattedDateString() }}',
                                customize: function(doc) {
                                    doc.pageMargins = [40, 160, 40,
                                        60
                                    ];
                                    doc.defaultStyle.fontSize = 10;
                                    doc.styles.tableHeader.fontSize = 10;
                                    doc.content.unshift({
                                        text: 'List of Employees for Termination as of {{ \Carbon\Carbon::now()->toFormattedDateString() }}',
                                        alignment: 'left',
                                        fontSize: 12,
                                        bold: true,
                                        margin: [0, 0, 0, 10]
                                    });

                                    doc['header'] = function() {
                                        return {
                                            margin: [40, 20, 40, 0],
                                            alignment: 'center',
                                            stack: [{
                                                    image: base64Logo,
                                                    width: 60,
                                                    alignment: 'center',
                                                    margin: [0, 0, 0, 5]
                                                },
                                                {
                                                    text: 'REVMAN AGENCY',
                                                    style: 'header',
                                                    fontSize: 14,
                                                    bold: true,
                                                    alignment: 'center',
                                                    margin: [0, 0, 0, 2]
                                                },
                                                {
                                                    text: 'Address: 2nd Flr Medical Arts Bldg 1., University Of Perpetual Help System - Laguna Compound,\nOld 3 National Highway Brgy. Sto Nino Binan City Laguna',
                                                    fontSize: 10,
                                                    alignment: 'center',
                                                    margin: [0, 0, 0, 1]
                                                },
                                                {
                                                    text: 'Email: revman.applicant@gmail.com | Cellphone: +639359722646 / +639564729639',
                                                    fontSize: 10,
                                                    alignment: 'center',
                                                    margin: [0, 0, 0, 0]
                                                }
                                            ]
                                        };
                                    };

                                    doc['footer'] = function(page, pages) {
                                        return {
                                            columns: [{
                                                    alignment: 'left',
                                                    text: 'Prepared By: ' + currentUser,
                                                    fontSize: 10
                                                },
                                                {
                                                    alignment: 'right',
                                                    text: [{
                                                        text: 'Page ' + page.toString() +
                                                            ' of ' + pages.toString(),
                                                        fontSize: 10
                                                    }]
                                                }
                                            ],
                                            margin: [10, 10]
                                        };
                                    };
                                }
                            },
                            {
                                "extend": 'print',
                                "title": 'List of Employees for Termination as of <?= date('F j, Y') ?>',
                                "exportOptions": {
                                    "columns": [0, 1]
                                },
                                customize: function(win) {
                                    $(win.document.body)
                                        .css('font-size', '10pt')
                                        .prepend(
                                            '<div style="text-align:center;">' +
                                            '<img src="' + base64Logo + '" width="80"/><br/>' +
                                            '<strong>REVMAN AGENCY</strong><br/>' +
                                            'Address: 2nd Flr Medical Arts Bldg 1., University Of Perpetual Help System - Laguna Compound,<br/>' +
                                            'Old 3 National Highway Brgy. Sto Nino Binan City Laguna<br/>' +
                                            'Email: revman.applicant@gmail.com<br/>' +
                                            'Cellphone: +639359722646 / +639564729639<br/><br/>' +
                                            '</div>'
                                        );
                                    $(win.document.body).append(
                                        `<div style="margin-top: 50px; text-align: left;"><strong>Prepared By: ${currentUser}</strong></div>`
                                    );
                                }
                            }
                        ],
                    }
                ],
                "columns":[
                    {"data":"fullname"},
                    {"data":"client_name"}
                ],
                "columnDefs": [{
					"targets": [0,1],   // target column
					"className": "textCenter",
				}]
            });
            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                $('.table:visible').each( function(e) {
                    $(this).DataTable().columns.adjust().responsive.recalc();
                });
            });
		</script>
        @endpush('scripts')
@endsection