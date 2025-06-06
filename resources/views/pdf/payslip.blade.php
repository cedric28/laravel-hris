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
      font-size: 0.6rem;
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
      margin-bottom: 3px;
    }

    .header img {
      max-width: 200px;
    }

    .company-info {
      margin-bottom: 3px;
    }

    .col-md-6 {
    /* Your styles here */
    float: left;
    width: 50%;
}

.row::after {
    content: "";
    clear: both;
    display: table;
}



</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src='{{  public_path('assets/img/logo.png') }}' width="60px" height="60px"alt="Company Logo">
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
    <td>{{ date("F j, Y",strtotime($payrollDate)) }}</td>
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
<div class="row">
  <div class="col-md-6 custom-gap">
    <h3 style="text-align:center">COMPENSATION</h3>
<table>
  <tr>
    <td>RATE PER HOUR</td>
    <td>{{ Config::get('app.currency') }} {{  $ratePerHour }}</td>
  </tr>
   <tr>
    <td>TOTAL DAYS WORKED</td>
    <td>{{ $totalHoursWorkedDays }} day/s</td>
  </tr>
  <tr>
    <td>TOTAL HOURS WORKED</td>
    <td>{{  $totalHoursWorked }} hr/s</td>
  </tr>
    <tr>
    <td>TOTAL HOURS OVERTIME</td>
    <td>{{ $totalHoursOverTime }} hr/s</td>
  </tr>
    <tr>
    <td>TOTAL HOURS LATE</td>
    <td>{{ $totalHoursLate }} hr/s</td>
  </tr>
 
  <tr>
    <td>TOTAL OVERTIME PAY</td>
    <td>{{ Config::get('app.currency') }} {{  Str::currency($overTimeTotal) }}</td>
  </tr>
  <tr>
    <td>SALARY</td>
    <td>{{ Config::get('app.currency') }} {{  Str::currency($basicSalaryTotal) }}</td>
  </tr>
 
  <tr>
    <td>DE MINIMIS BENEFITS</td>
    <td>{{ Config::get('app.currency') }} {{  Str::currency($deMinimisBenefits) }}</td>
  </tr>
  <tr>
    <td>ADDITIONAL PAY</td>
    <td>{{ Config::get('app.currency') }} {{  Str::currency($otherPay) }}</td>
  </tr>
   <tr>
    <td>HOLIDAY PAY</td>
    <td>{{ Config::get('app.currency') }} {{  Str::currency($totalHolidayPay) }}</td>
  </tr>
  @if (!empty($thirteenthMonthPay) && floatval($thirteenthMonthPay) != 0)
        <tr>
          <td>13TH MONTH PAY</td>
          <td>{{ Config::get('app.currency') }} {{  Str::currency($thirteenthMonthPay) }}</td>
        </tr>
    @endif
  <tr>
    <td><b>TOTAL COMPENSATION</b></td>
    <td><b>{{ Config::get('app.currency') }} {{  Str::currency($totalCompensation) }}</b></td>
  </tr>
 
</table>
  </div>
   <div class="col-md-6 custom-gap">
   <h3 style="text-align:center">DEDUCTIONS</h3>
<table>
  <tr>
    <td>SSS</td>
    <td>{{ Config::get('app.currency') }} {{ Str::currency($employeeDetails->salary->sss / 2) }}</td>
  </tr>
  <tr>
    <td>PHILHEALTH</td>
    <td>{{ Config::get('app.currency') }} {{ Str::currency($employeeDetails->salary->philhealth / 2) }}</td>
  </tr>
  <tr>
    <td>HDMF</td>
    <td>{{ Config::get('app.currency') }} {{ Str::currency($employeeDetails->salary->pagibig / 2) }}</td>
  </tr>
  <tr>
    <td>TAX</td>
    <td>{{ Config::get('app.currency') }} {{ Str::currency($tax / 2) }}</td>
  </tr>
  @if (!empty($thirteenthMonthTax) && floatval($thirteenthMonthTax) != 0)
    <tr>
      <td>13th MONTH PAY TAX</td>
      <td>{{ Config::get('app.currency') }} {{  Str::currency($thirteenthMonthTax) }}</td>
    </tr>
  @endif
  @if (!empty($generalDeductions))
      @foreach ($generalDeductions as $deduction)
        <tr>
          <td>{{ strtoupper($deduction['name']) }}</td>
          <td>{{ Config::get('app.currency') }} {{  Str::currency($deduction['amount'] / 2) }}</td>
        </tr>
      @endforeach
    @endif
   <tr>
    <td>UNIFORM</td>
    <td>{{ Config::get('app.currency') }} {{ Str::currency($employeeDetails->salary->uniform) }}</td>
  </tr>
   <tr>
    <td>OTHER DEDUCTION</td>
    <td>{{ Config::get('app.currency') }} {{  Str::currency($otherDeduction) }}</td>
  </tr>
  <tr>
    <td>TOTAL LATE DEDUCTION</td>
    <td>{{ Config::get('app.currency') }} {{ Str::currency($lateTotalDeduction) }}</td>
  </tr>
  <tr>
    <td><b>TOTAL DEDUCTIONS</b></td>
    <td><b>{{ Config::get('app.currency') }} {{ Str::currency($totalDeduction) }}</b></td>
  </tr>
</table>
   </div>
</div>



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