<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Return Products Report</title>
<style>
	@import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,900,700,500,300,100);
*{
  margin: 0;
  box-sizing: border-box;
  -webkit-print-color-adjust: exact;
}
body{
  background: #E0E0E0;
  font-family: 'Roboto', sans-serif;
}
::selection {background: #f31544; color: #FFF;}
::moz-selection {background: #f31544; color: #FFF;}
.clearfix::after {
    content: "";
    clear: both;
    display: table;
}
.col-left {
    float: left;
}
.col-right {
    float: right;
}
h1{
  font-size: 1.5em;
  color: #444;
}
h2{font-size: .9em;}
h3{
  font-size: 1.2em;
  font-weight: 300;
  line-height: 2em;
}
p{
  font-size: .75em;
  color: #666;
  line-height: 1.2em;
}
a {
    text-decoration: none;
    color: #00a63f;
}

#invoiceholder{
  width:100%;
  height: 100%;
}
#invoice{
  position: relative;
  /* margin: 0 auto; */
  width: 100%;
  background: #FFF;
}

[id*='invoice-']{ /* Targets all id with 'col-' */
/*  border-bottom: 1px solid #EEE;*/
  padding: 20px;
}

#invoice-top{border-bottom: 2px solid #00a63f;}
#invoice-mid{min-height: 110px;}
#invoice-bot{ min-height: 240px;}

.logo{
    display: inline-block;
    vertical-align: middle;
	width: 110px;
    overflow: hidden;
}

.header{
      display:flex;
      justify-content:space-between;
      flex-direction:row;
}

.prepared-by{
  display: flex;
  flex-direction: column;
}

#prepared-name{
  color: #444;
  font-size: .9em;
  font-weight: bold;
}

#date-created{
  color: #444;
  font-size: .9em;
  font-weight: bold;
}

.info{
    display: inline-block;
    vertical-align: middle;
    margin-left: 20px;
}
.logo img,
.clientlogo img {
    width: 100%;
}
.clientlogo{
    display: inline-block;
    vertical-align: middle;
	width: 50px;
}
.clientinfo {
    display: inline-block;
    vertical-align: middle;
    margin-left: 20px
}
.title{
  float: right;
}
.title-table{
  text-align: center;
  margin: 20px 0;
}
.title p{text-align: right;}
#message{margin-bottom: 30px; display: block;}
h2 {
    margin-bottom: 5px;
    color: #444;
}
.col-right td {
    color: #666;
    padding: 5px 8px;
    border: 0;
    font-size: 0.75em;
    border-bottom: 1px solid #eeeeee;
}
.col-right td label {
    margin-left: 5px;
    font-weight: 600;
    color: #444;
}
.cta-group a {
    display: inline-block;
    padding: 7px;
    border-radius: 4px;
    background: rgb(196, 57, 10);
    margin-right: 10px;
    min-width: 100px;
    text-align: center;
    color: #fff;
    font-size: 0.75em;
}
.cta-group .btn-primary {
    background: #00a63f;
}
.cta-group.mobile-btn-group {
    display: none;
}
table{
  width: 100%;
  border-collapse: collapse;
}
td{
    padding: 10px;
    border-bottom: 1px solid #cccaca;
    font-size: 0.70em;
    text-align: left;
}

.tabletitle th {
  border-bottom: 2px solid #ddd;
  text-align: right;
}
.tabletitle th:nth-child(2) {
    text-align: left;
}
th {
    font-size: 0.7em;
    text-align: left;
    padding: 5px 10px;
}
.item{width: 50%;}
.list-item td {
    text-align: right;
}
.list-item td:nth-child(2) {
    text-align: left;
}
.total-row th,
.total-row td {
    text-align: right;
    font-weight: 700;
    font-size: .75em;
    border: 0 none;
}
.table-main {
    
}
footer {
    border-top: 1px solid #eeeeee;;
    padding: 15px 20px;
}
.effect2
{
  position: relative;
}

@media screen and (max-width: 767px) {
  .header{
    flex-direction:column;
    text-align:center;
  }
    h1 {
        font-size: .9em;
    }
    #invoice {
        width: 100%;
    }
    #message {
        margin-bottom: 20px;
    }
    [id*='invoice-'] {
        padding: 20px 10px;
    }
    .logo {
        width: 140px;
    }
    .title {
        float: none;
        display: inline-block;
        vertical-align: middle;
        margin-left: 40px;
    }
    .title p {
        text-align: left;
    }
    .col-left,
    .col-right {
        width: 100%;
    }
    .table {
        margin-top: 20px;
    }
    #table {
        white-space: nowrap;
        overflow: auto;
    }
    td {
        white-space: normal;
    }
    .cta-group {
        text-align: center;
    }
    .cta-group.mobile-btn-group {
        display: block;
        margin-bottom: 20px;
    }
     /*==================== Table ====================*/
    .table-main {
        border: 0 none;
    }  
      .table-main thead {
        border: none;
        clip: rect(0 0 0 0);
        height: 1px;
        margin: -1px;
        overflow: hidden;
        padding: 0;
        position: absolute;
        width: 1px;
      }
      .table-main tr {
        border-bottom: 2px solid #eee;
        display: block;
        margin-bottom: 20px;
      }
      .table-main td {
        font-weight: 700;
        display: block;
        padding-left: 40%;
        max-width: none;
        position: relative;
        border: 1px solid #cccaca;
        text-align: left;
      }
      .table-main td:before {
        /*
        * aria-label has no advantage, it won't be read inside a table
        content: attr(aria-label);
        */
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: normal;
        text-transform: uppercase;
      }
    .total-row th {
        display: none;
    }
    .total-row td {
        text-align: left;
    }
    footer {text-align: center;}
}

</style>
</head>
<body>
  <div id="invoiceholder">
  <div id="invoice" class="effect2">
    
    <div id="invoice-top">
      <div class="logo"><img src="dist/img/logo-drug.png" alt="Logo" /></div>
      <div class="title">
        <h1>Tall Drug and Gen. Merchandise Pharmacy</h1>
        <p><span id="invoice_date">434 JM loyola st. Brgy 4</span><br>
           <span id="gl_date">talldrug.genmdse@yahoo.com</span><br>
		   <span id="invoice_date">(046)4130829</span><br>
        </p>
      </div><!--End Title-->
    </div><!--End InvoiceTop-->
    
    <div id="invoice-bot">
      <div class="header">
        <div class="prepared-by">
          <span id="prepared-name">Prepared By: {{ $fullName }}</span>
        </div>
  
      </div>
      <div class="header">
        <div class="prepared-by">
          <span id="date-created">Date: {{ $dateToday }}</span>
        </div>
      </div>
      <div class="header">
        <div class="prepared-by">
          <span id="date-created">Total Item/s: {{ $returnCount }}</span>
        </div>
      </div>
      <div id="table">
        <h2 class="title-table">Return of Medical Supplies and Product Reports</h2>
        @foreach ($returnStocks as $stock)
        <table class="table-main">
          <thead>    
            <tr class="tabletitle">
              <tr>
                <th colspan="8">{{$stock->reference_no}} | {{$stock->supplier->name }} | {{$stock->delivery_at}}</th>
              </tr>
              <tr>
                <th>PRODUCT NAME</th>
                <th>GENERIC NAME</th>
                <th>SERIAL NUMBER</th>
                <th>RETURNED QUANTITY</th>
                <th>REMARK</th>
                <th>NOTE</th>
              </tr>
            </tr>
          </thead>
          <tbody>
            @foreach ($stock->return_stock_items as $item)
            <tr>
                <td>{{  $item->product->product_name }}</td>
                <td>{{  $item->product->generic_name }}</td>
                <td class="textRight">{{  $item->product->sku }}</td>
                <td class="textRight">{{  $item->qty }}</td>
                <td>{{  ucwords($item->remark ?? "-") }}</td>
                <td>{{  $item->note == "" ? "-" : $item->note }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @endforeach
      </div><!--End Table-->	
      
    </div><!--End InvoiceBot-->
    <footer>
      <div id="legalcopy" class="clearfix">
        <p class="col-right">Our mailing address is:
            <span class="email"><a href="mailto:talldrug.genmdse@yahoo.com">talldrug.genmdse@yahoo.com</a></span>
        </p>
      </div>
    </footer>
  </div><!--End Invoice-->
</div><!-- End Invoice Holder-->
  
  

</body>
</html>