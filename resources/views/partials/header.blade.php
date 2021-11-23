<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">{{ $totalNotification }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">{{ $totalNotification }} Notifications</span>
          <div class="dropdown-divider"></div>
          @if($totalDeliveries > 0 )
          <a href="{{ route('deliverySchedule') }}" class="dropdown-item">
            <i class="fas fas fa-truck mr-2"></i> {{ $totalDeliveries }} upcoming delivery
          </a>
          @endif
          @if($expiredProducts > 0 )
          <div class="dropdown-divider"></div>
          <a href="{{ route('dailyPreventive') }}" class="dropdown-item">
            <i class="fas fa-box-open mr-2"></i> {{ $expiredProducts }} products will soon be expired
          </a>
          @endif
          <div class="dropdown-divider"></div>
        </div>
      </li>
	    <li class="nav-item d-none d-sm-inline-block">
        <a data-close="true" onclick="event.preventDefault();  document.getElementById('logout-form').submit();" class="nav-link">Logout</a>
      </li>
    </ul>
</nav>
<!-- /.navbar -->