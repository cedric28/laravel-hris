<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
		<title>PAYSLIP</title>
<style>
  body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 5px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}

 .container {
      max-width: 800px;
      background-color: #fff;
    }

    .header {
      text-align: center;
      margin-bottom: 5px;
    }

    .header img {
      max-width: 200px;
    }

    .company-info {
      margin-bottom: 5px;
    }

</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src='{{  public_path('assets/img/logo.png') }}' width="100px" height="100px"alt="Company Logo">
    </div>

    <div class="company-info">
      <h3>REVMAN AGENCY</h3>
      <p>Address: 2nd Flr Medical Arts Bldg 1., <br/>University Of Perpetual Help System - Laguna Compound, <br/>
      Old 3 National Highway Brgy. Sto Nino Binan City Laguna</p>
      <p>Email: revman.applicant@gmail.com</p>
      <p>Cellphone: +639359722646 / +639564729639</p>
    </div>


<table>
  <tr>
    <th>NAME:</th>
    <td>{{ $employee }}</td>
    <th>PAYROLL DATE:</th>
    <td>{{ date("F j, Y",strtotime($endDate)) }}</td>
  </tr>
  <tr>
    <th>TIN:</th>
  <td>{{ $employeeDetails->employee->tin ?? '-' }}</td>
    <th>DATE COVERED:</th>
    <td>Payroll: {{ $startDate }} - {{ $endDate }}</td>
  </tr>
  <tr>
    <th>SSS NO.:</th>
    <td>{{ $employeeDetails->employee->sss ?? '-' }}</td>
    <th>TAX STATUS:</th>
    <td>S</td>
  </tr>
  <tr>
    <th>PHILHEALTH NO.:</th>
    <td>{{ $employeeDetails->employee->philhealth ?? '-' }}</td>
    <th>CLIENT:</th>
    <td>{{ $company }}</td>
  </tr>
  <tr>
    <th>HDMF:</th>
    <td>{{ $employeeDetails->employee->pagibig ?? '-' }}</td>
    <th>POSITION:</th>
    <td>{{ $position }}</td>
  </tr>
 
</table>

<h3>COMPENSATION</h3>
<table>
  <tr>
    <td>BASIC</td>
    <td>{{ Config::get('app.currency') }} {{  Str::currency($basicSalaryTotal) }}</td>
  </tr>
  <tr>
    <td>DE MINIMIS BENEFITS</td>
    <td>{{ Config::get('app.currency') }} {{  Str::currency($deMinimisBenefits) }}</td>
  </tr>
  @if($overTimeTotal > 0)
   <tr>
    <td>OVERTIME</td>
    <td>{{ Config::get('app.currency') }} {{  Str::currency($overTimeTotal) }}</td>
  </tr>
  @endif
  <tr>
    <td><b>TOTAL COMPENSATION</b></td>
    <td><b>{{ Config::get('app.currency') }} {{  Str::currency($totalCompensation) }}</b></td>
  </tr>
 
</table>

<h3>DEDUCTIONS</h3>
<table>
  <tr>
    <td>SSS</td>
    <td>{{ Config::get('app.currency') }} {{ Str::currency($employeeDetails->salary->sss) }}</td>
  </tr>
  <tr>
    <td>PHILHEALTH</td>
    <td>{{ Config::get('app.currency') }} {{ Str::currency($employeeDetails->salary->philhealth) }}</td>
  </tr>
  <tr>
    <td>HDMF</td>
    <td>{{ Config::get('app.currency') }} {{ Str::currency($employeeDetails->salary->pagibig) }}</td>
  </tr>
  <tr>
    <td>TAX</td>
    <td>{{ Config::get('app.currency') }} {{ Str::currency($tax) }}</td>
  </tr>
   <tr>
    <td>UNIFORM</td>
    <td>{{ Config::get('app.currency') }} {{ Str::currency($employeeDetails->salary->uniform) }}</td>
  </tr>
  <tr>
    <td><b>TOTAL DEDUCTIONS</b></td>
    <td><b>{{ Config::get('app.currency') }} {{ Str::currency($totalDeduction) }}</b></td>
  </tr>
</table>
<br/>
<hr/>
<br/>
<table>
 
  <tr>
    <td><b>NET PAY</b></td>
    <td><b>{{ Config::get('app.currency') }} {{ Str::currency($netPay) }}</b></td>
  </tr>
</table>
</div>
</body>
</html>