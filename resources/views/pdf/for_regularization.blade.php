<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>CERTIFICATE FOR REGULARIZATION</title>
        <link href="{{ asset('assets/css/sb-admin-2.css') }}" rel="stylesheet" type="text/css">
		<style>
		body {
				border: 10px solid;
				border-radius: 20px;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				padding: 20px;
					border-image: linear-gradient(50deg, turquoise, greenyellow) 1;
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
								<td colspan="4"><div align="center"><span style="font-size: 30pt;font-weight:bold">CERTIFICATE FOR REGULARIZATION</span></div></td>
								<td>&nbsp;</td>
						</tr>
						<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
						</tr>
						<tr>
								<td colspan="4"><div align="center"><span style="font-size: 14pt;font-style: italic;">This is to certify that <span style="font-size: 15pt;font-style: italic;font-weight:bold">{{ $employee }}</span> has successfully completed the regularization process as per the requirements outlined by <span style="font-size: 15pt;font-style: italic;font-weight:bold">{{ $company }}</span>. This certificate is awarded in recognition of their commitment to fulfilling all necessary obligations and standards set forth by the organization.</span></div></td>
								<td>&nbsp;</td>
						</tr>
						<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
						</tr>
						<tr>
						 	<td colspan="4"><div align="center"></div></td>
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