<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-success elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="{{ asset('dist/img/logo.png') }}" alt="Logo" class="brand-image elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">REVMAN</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        @can('isAdmin')
        <div class="image">
          <img src="{{ asset('dist/img/avatar.png') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        @endcan
        @can('isHR')
        <div class="image">
          <img src="{{ asset('dist/img/avatar3.png') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        @endcan
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->email }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-header">MAIN</li>
            @can('isAdmin')
            <li class="nav-item">
           
                <a href="{{ route('home') }}" class="nav-link {{ (request()->is('dashboard')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        Dashboard
                    </p>
                </a>
            </li>
            @endcan
            @canany(['isHR','isAdmin'])
            <li class="nav-item">
              <a href="{{ route('client.index')}}" class="nav-link {{ (request()->is('client*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-building"></i>
                    <p>
                        Client
                    </p>
                </a>
            </li>
            
             <li class="nav-item">
                <a href="{{ route('employee.index')}}" class="nav-link {{ (request()->is('employee*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-address-card"></i>
                    <p>Employee</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('deployment.index')}}" class="nav-link {{ (request()->is('deployment*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-user-tag"></i>
                    <p>Deployment</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('attendance.index')}}" class="nav-link {{ (request()->is('attendance*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-file-excel"></i>
                    <p>Bulk Attendance</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('overtime.index')}}" class="nav-link {{ (request()->is('overtime*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-file-excel"></i>
                    <p>Bulk Over-Time</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('holiday-setting.index')}}" class="nav-link {{ (request()->is('holiday-setting*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-calendar"></i>
                    <p>Holiday Setting</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('payroll.index')}}" class="nav-link {{ (request()->is('holiday-setting*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-calendar"></i>
                    <p>Payroll Setting</p>
                </a>
            </li>

            <li class="nav-item">
                    <a href="{{ route('feedback.index')}}" class="nav-link {{ (request()->is('feedback*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-user-times"></i>
                    <p>Performance Evaluation</p>
                </a>
            </li>

             <li class="nav-item">
                    <a href="{{ route('for-termination.index')}}" class="nav-link {{ (request()->is('for-termination*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-book"></i>
                    <p>Disciplinary</p>
                </a>
            </li>
 

            <li class="nav-header">REPORTS</li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                <i class="nav-icon fas fas fa-user-tie"></i>
                <p>
                    Employee Report
                    <i class="fas fa-angle-left right"></i>
                </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                    <a href="{{ route('for-regularization.index')}}" class="nav-link {{ (request()->is('for-regularization*')) ? 'nav-link active' : '' }}">
                            <i class="nav-icon fas fa-clipboard"></i>
                            <p>For Regularization</p>
                        </a>
                    </li>
                    <li class="nav-item">
                     <a href="{{ route('perfect-attendance.index')}}" class="nav-link {{ (request()->is('perfect-attendance*')) ? 'nav-link active' : '' }}">
                            <i class="nav-icon fas fas fa-calendar"></i>
                            <p>Perfect Attendance Report</p>
                        </a>
                    </li>
                    <li class="nav-item">
                         <a href="{{ route('best-performer.index')}}" class="nav-link {{ (request()->is('best-performer*')) ? 'nav-link active' : '' }}">
                            <i class="nav-icon fas fas fa-user-tie"></i>
                            <p>Best Performer Report</p>
                        </a>
                    </li>
                </ul>
            </li>

            @endcan
          
       
            <li class="nav-header">SETTINGS</li>
                @canany(['isHR','isAdmin'])
              <li class="nav-item">
                <a href="{{ route('backup-database.index')}}" class="nav-link {{ (request()->is('backup-database*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-user-shield"></i>
                    <p>Back-up Database</p>
                </a>
            </li>
             @endcan
              @canany(['isHR','isAdmin'])
            <li class="nav-item">
                <a href="{{ route('user-profile')}}" class="nav-link {{ (request()->is('profile*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-user-shield"></i>
                    <p>Profile</p>
                </a>
            </li>
             @endcan
             @can('isAdmin')
            <li class="nav-item">
                <a href="{{ route('user.index')}}" class="nav-link {{ (request()->is('user*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-user-plus"></i>
                    <p>User Management</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('logs.index')}}" class="nav-link {{ (request()->is('logs*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-book"></i>
                    <p>Activity Logs</p>
                </a>
            </li>
            @endcan
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>