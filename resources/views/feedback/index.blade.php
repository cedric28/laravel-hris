@extends('layouts.app')

@section('content')
	<section class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1>Employee Performance Evaluation</h1>
          </div>
          <div class="col-sm-6 d-none d-sm-block">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
              <li class="breadcrumb-item">Employee Performance Evaluation</li>
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
							<a type="button" style="box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);" href="{{ route('feedback.create')}}" class="btn btn-outline-success btn-sm float-left"><i class="fas fa-users mr-2"></i> Add Performance Evaluation</a>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
                            <div class="card card-success card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Active Performance Evaluation</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Inactive Performance Evaluation</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                                            <table class="table table-hover table-striped" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);"id="feedbacks">
                                                <thead>
                                                    <tr style="text-align:center;">
                                                        <th>EMPLOYEE NAME</th>
                                                        <th>COMPANY</th>
                                                        <th>RATE</th>
                                                        <th>ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                  
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>
                                                                
                                                            </td>
                                                        </tr>
                                                 
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab"> 
                                            <table class="table table-hover table-striped" style="box-shadow: 1px 16px 20px 0px rgba(19,54,30,0.75);"id="inactive-feedbacks">
                                                <thead>
                                                    <tr style="text-align:center;">
                                                        <th>EMPLOYEE NAME</th>
                                                        <th>COMPANY</th>
                                                        <th>RATE</th>
                                                        <th>ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>
                                                            
                                                        </td>
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
    <div id="restoreModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to restore this data?</h4>
                </div>
                <div class="modal-footer">
                <button type="button" name="restore_button" id="restore_button" class="btn btn-danger">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
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
            var table = $('#feedbacks').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('activeFeedBack') ?>",
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
                            filename: 'List of Employees Performance Evaluation as of <?= date('F j, Y') ?>',
                            exportOptions: {
                                columns: [0, 1, 2]
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

                                    'List of Employees Performance Evaluation as of <?= date('F j, Y') ?>',
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
                                    columns: [0, 1, 2]
                                },
                                filename: 'List of Employees Performance Evaluation as of {{ \Carbon\Carbon::now()->toFormattedDateString() }}',
                                customize: function(doc) {
                                    doc.pageMargins = [40, 160, 40,
                                        60
                                    ];
                                    doc.defaultStyle.fontSize = 10;
                                    doc.styles.tableHeader.fontSize = 10;
                                    doc.content.unshift({
                                        text: 'List of Employees Performance Evaluation as of {{ \Carbon\Carbon::now()->toFormattedDateString() }}',
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
                                "title": 'List of Employees Performance Evaluation as of <?= date('F j, Y') ?>',
                                "exportOptions": {
                                    "columns": [0, 1, 2]
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
                    {"data":"full_name"},
                    {"data":"client_name"},
                    {"data":"rate"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [{
					"targets": [0,1,2],   // target column
					"className": "textCenter",
				}]
            });

            var tableInactive = $('#inactive-feedbacks').DataTable({
				"responsive": true, 
				"lengthChange": false, 
				"autoWidth": false,
      			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url":"<?= route('InactiveFeedBack') ?>",
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
                            filename: 'List of Inactive Employees Performance Evaluation as of <?= date('F j, Y') ?>',
                            exportOptions: {
                                columns: [0, 1, 2]
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

                                    'List of Inactive Employees Performance Evaluation as of <?= date('F j, Y') ?>',
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
                                    columns: [0, 1, 2]
                                },
                                filename: 'List of Inactive Employees Performance Evaluation as of {{ \Carbon\Carbon::now()->toFormattedDateString() }}',
                                customize: function(doc) {
                                    doc.pageMargins = [40, 160, 40,
                                        60
                                    ];
                                    doc.defaultStyle.fontSize = 10;
                                    doc.styles.tableHeader.fontSize = 10;
                                    doc.content.unshift({
                                        text: 'List of Inactive Employees Performance Evaluation as of {{ \Carbon\Carbon::now()->toFormattedDateString() }}',
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
                                "title": 'List of Inactive Employees Performance Evaluation as of <?= date('F j, Y') ?>',
                                "exportOptions": {
                                    "columns": [0, 1, 2]
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
                    {"data":"full_name"},
                    {"data":"client_name"},
                    {"data":"rate"},
                    {"data":"action","searchable":false,"orderable":false}
                ],
                "columnDefs": [{
					"targets": [0,1,2],   // target column
					"className": "textCenter",
				}]
            });

			$(document).on('click', '#show', function(){
                var feedbackId = $(this).attr('data-id');
                window.location.href = 'feedback/'+feedbackId;
            });

            $(document).on('click', '#edit', function(){
                var id = $(this).attr('data-id');
                window.location.href = 'feedback/'+id+'/edit';
            });

            var feedbackId;
            $(document).on('click', '#delete', function(){
                feedbackId = $(this).attr('data-id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function(){
                $.ajax({
                    url:"feedback/destroy/"+feedbackId,
                    beforeSend:function(){
                        $('#ok_button').text('Archiving...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModal').modal('hide');
							table.ajax.reload();
                            tableInactive.ajax.reload();
                            $('#ok_button').text('OK');
                        }, 2000);
                    }
                })
            });

            var feedbackId;
            $(document).on('click', '#restore', function(){
                feedbackId = $(this).attr('data-id');
                $('#restoreModal').modal('show');
            });

            $('#restore_button').click(function(){
                $.ajax({
                    url:"feedback/restore/"+feedbackId,
                    beforeSend:function(){
                        $('#restore_button').text('Restoring...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#restoreModal').modal('hide');
                            tableInactive.ajax.reload();
                            table.ajax.reload();
                            $('#restore_button').text('OK');
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