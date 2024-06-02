<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>CERTIFICATE OF BEST PERFORMER</title>
        <link href="{{ asset('assets/css/sb-admin-2.css') }}" rel="stylesheet" type="text/css">
		<style>
		body {
					 margin: 0;
        padding: 0;
        background-image: url('{{  public_path('assets/img/backgroundimage.jpg') }}');
        background-size: cover;
        background-position: center;
			}
			footer {
				border-top: 1px solid #eeeeee;;
				/* padding: 15px 20px; */
				padding: 50px 20% 0;
				text-align: center;
    font-size: 13px;
			}
			.invoice-box {
				max-width: 100%;
				margin: auto;
				padding: 30px;
				/* border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); */
				font-size: 16px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: center;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}


			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

			}

			.title{
                font-size: 50px;
            }

   .textLeft {
			 text-align : left;
		 }
		</style>
	</head>

	<body>
		<div class="invoice-box">
			<table cellpadding="0" cellspacing="0">
				<tr class="top">
					<td>
						<table width="1030" border="0" align="center">
							<tr>
									<td colspan="4"><div align="center"></div></td>
									<td>&nbsp;</td>
							</tr>
							<tr>
								<td colspan="4"><div align="center"><span style="font-size: 30pt;font-weight:bold">CERTIFICATE OF BEST PERFORMER</span></div></td>
								<td>&nbsp;</td>
						</tr>
						
						<tr>
								<td colspan="4"><div align="center"><span style="font-size: 14pt;font-style: italic;">This certifies that <span style="font-size: 15pt;font-style: italic;font-weight:bold">{{ $employee }}</span> has been recognized as the Best Performer for the month of <span style="font-size: 15pt;font-style: italic;font-weight:bold">{{ $month }}</span>,<span style="font-size: 15pt;font-style: italic;font-weight:bold">{{ $year }}</span> at <span style="font-size: 15pt;font-style: italic;font-weight:bold">{{ $company }}</span>. With a perfect attendance record and consistently achieving a performance rating of 10, <span style="font-size: 15pt;font-style: italic;font-weight:bold">{{ $employee }}</span> has demonstrated unparalleled dedication, excellence, and commitment to their role.</span></div></td>
								<td>&nbsp;</td>
						</tr>
								<tr>
								<td colspan="4"><div align="center"><span style="font-size: 14pt;font-style: italic;">Their exceptional work ethic, remarkable achievements, and positive impact on the team have set a standard of excellence for all employees to aspire to.</span></div></td>
								<td>&nbsp;</td>
						</tr>
							<tr>
								<td colspan="4"><div align="center"><span style="font-size: 14pt;font-style: italic;">Presented on this <span style="font-size: 15pt;font-style: italic;font-weight:bold">{{ $day }}</span> of <span style="font-size: 15pt;font-style: italic;font-weight:bold">{{ $month }}</span>, <span style="font-size: 15pt;font-style: italic;font-weight:bold">{{ $year }}</span>.</span></div></td>
								<td>&nbsp;</td>
						</tr>
				
						<tr>
								<td><div align="right"><span style="font-size: 14pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="{{  public_path('assets/img/logo.png') }}" style="width: 100%; max-width: 150px;height: 150px;" /> </span></div></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td><div align="left"><span style="font-size: 14pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="{{ public_path('assets/img/achievement.png') }}" style="width: 100%; max-width: 150px;height: 150px;" /> </span></div></td>
						</tr>
					</table>
					</td>
    </tr>	
			</table>
		</div>
	</body>
</html>