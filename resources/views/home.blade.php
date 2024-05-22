@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
	<section class="content">
      <div class="container-fluid">
      
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ $totalApplicants }}</h3>
                <p>Applicant/s {{  $year }}</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-stalker"></i>
              </div>
              <a href="{{ route('employee.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ $totalEmployees }}</h3>

                <p>New Deployed Employee/s {{  $year }}</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="{{ route('deployment.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ $user }}</h3>

                <p>Registered Users</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="{{ route('user.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{  $totalClients }}</h3>
                <p>Clients</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="{{ route('client.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <div class="col-lg-12 col-12">
            <figure class="highcharts-figure">
              <div id="yearly"></div>
            </figure>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 col-12">
            <figure class="highcharts-figure">
              <div id="monthly"></div>
            </figure>
          </div>
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
        @push('scripts')
        <script>
          let yearNow = {!! json_encode($year) !!};
       
          let salesYear = {!! json_encode($salesPerYear) !!};
          let salesMonthly = {!! json_encode($salesPerMonth) !!};
          let sales = salesYear.map(item => {
              return parseFloat(item.total_sales)
          })

          let years = salesYear.map(item => {
              return item.year
          })

          let monthSales = 10000

          let months = salesMonthly.map(item => {
              let month = "";
              switch (item.month) {
                case 01:
                  month = "January";
                  break;
                case 02:
                  month = "February";
                  break;
                case 03:
                  month = "March";
                  break;
                case 04:
                  month = "April";
                  break;
                case 05:
                  month = "May";
                  break;
                case 06:
                  month = "June";
                  break;
                case 07:
                  month = "July";
                  break;
                case 08:
                  month = "August";
                  break;
                case 09:
                  month = "September";
                  break;
                case 10:
                  month = "October";
                  break;
                case 11:
                  month = "November";
                  break;
                case 12:
                  month = "December";
              }

              return month;
          })

          console.log(months);
  
          Highcharts.chart('yearly', {
              title: {
                text: 'Yearly Total Employee for Reguralization'
              },

              subtitle: {
                text: 'REVIER AGENCY'
              },

              yAxis: {
                title: {
                  text: 'Total Employee for Regularization Per Year'
                }
              },

              xAxis: {
                accessibility: {
                  rangeDescription: `Range: ${years[0]} to ${years.slice(-1)}`
                }
              },

              legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
              },

              plotOptions: {
                series: {
                  label: {
                    connectorAllowed: false
                  },
                  pointStart: years[0]
                }
              },

            series: [{
              name: 'Total Number of Employee',
              data: sales
            }],

            responsive: {
              rules: [{
                condition: {
                  maxWidth: 500
                },
                chartOptions: {
                  legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                  }
                }
              }]
            }

          });

          Highcharts.chart('monthly', {
            chart: {
              type: 'spline'
            },
            title: {
              text: `Employee Monthly Perfect Attendance for ${yearNow}`
            },
            subtitle: {
              text: 'REVIER AGENCY'
            },
            xAxis: {
              categories: months
            },
            yAxis: {
              title: {
                text: 'Total Perfect Attendance Per Month'
              },
              labels: {
                formatter: function () {
                  return this.value + '°';
                }
              }
            },
            tooltip: {
              crosshairs: true,
              shared: true
            },
            plotOptions: {
              spline: {
                marker: {
                  radius: 4,
                  lineColor: '#666666',
                  lineWidth: 1
                }
              }
            },
            series: [{
              name: 'Total Number of Employee',
              marker: {
                symbol: 'square'
              },
              data: []

            }]
          });
        </script>
        @endpush('scripts')
@endsection