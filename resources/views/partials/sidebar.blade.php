<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="{{ asset('dist/img/logo-drug.png') }}" alt="Logo" class="brand-image elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">TALL DRUG</span>
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
        @can('isCashier')
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
                <a href="{{ route('home') }}" class="nav-link {{ (request()->is('home')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        Dashboard
                    </p>
                </a>
            </li>
            @endcan
            <li class="nav-item">
                <a href="{{ route('pos.index') }}" class="nav-link {{ (request()->is('pos*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-shopping-cart"></i>
                    <p>
                        POS
                    </p>
                </a>
            </li>
            @can('isAdmin')
            <li class="nav-item">
                <a href="{{ route('product.index')}}" class="nav-link {{ (request()->is('product*')) ? 'nav-link active' : '' }}">
                <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                    <p>Product</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('category.index')}}" class="nav-link {{ (request()->is('category*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-object-ungroup"></i>
                    <p>Category</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('supplier.index')}}" class="nav-link {{ (request()->is('supplier*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-truck-loading"></i>
                    <p>Supplier</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>
                    Stocks
                    <i class="fas fa-angle-left right"></i>
                </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('stock.index')}}" class="nav-link {{ (request()->is('stock*')) ? 'nav-link active' : '' }}">
                                <i class="nav-icon fas fa-box-open"></i>
                                <p>Stock Entry</p>
                            </a>
                    </li>
    
                    <li class="nav-item">
                        <a href="{{ route('history-stock-in.index')}}" class="nav-link {{ (request()->is('history-stock-in*')) ? 'nav-link active' : '' }}">
                            <i class="fas fa-book nav-icon"></i>
                            <p>Stock In History</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('return-stock.index') }}" class="nav-link {{ (request()->is('return-stock*')) ? 'nav-link active' : '' }}">
                            <i class="fas fa-truck-moving nav-icon"></i>
                            <p>Return Stocks</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('delivery-request.index') }}" class="nav-link {{ (request()->is('delivery-request*')) ? 'nav-link active' : '' }}">
                            <i class="fas fa-truck nav-icon"></i>
                            <p>Delivery Request</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('inventory.index')}}" class="nav-link {{ (request()->is('inventory*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-box"></i>
                    <p>Inventory</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('inventories-level.index')}}" class="nav-link {{ (request()->is('inventories-level*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-chart-line"></i>
                    <p>Inventory Level</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('discount.index')}}" class="nav-link {{ (request()->is('discount*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-ticket-alt"></i>
                    <p>Discount</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('point.index')}}" class="nav-link {{ (request()->is('point*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-ticket-alt"></i>
                    <p>Points Discount</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('customer.index')}}" class="nav-link {{ (request()->is('customer*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-user-friends"></i>
                    <p>Customer</p>
                </a>
            </li>
    
            @endcan
            @can('isAdmin')
            <li class="nav-header">REPORTS</li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>
                    Sales
                    <i class="fas fa-angle-left right"></i>
                </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('salesYearly') }}" class="nav-link {{ (request()->is('sales-report-yearly*')) ? 'nav-link active' : '' }}">
                            <i class="nav-icon fas fa-table"></i>
                                <p>Yearly Sales</p>
                        </a>
                    </li>
    
                    <li class="nav-item">
                        <a href="{{ route('salesMonthly') }}" class="nav-link {{ (request()->is('sales-report-monthly*')) ? 'nav-link active' : '' }}">
                            <i class="nav-icon fas fa-table"></i>
                            <p>Monthly Sales</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('customerDiscount') }}" class="nav-link {{ (request()->is('reports-customer-discount*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-table"></i>
                    <p>Customers Discount</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('stockGoods') }}" class="nav-link {{ (request()->is('good-stocks-report*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-table"></i>
                    <p>Stock of Medical and Healthcare Goods</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('deliverySchedule') }}" class="nav-link {{ (request()->is('schedule-delivery*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-table"></i>
                    <p>Delivery Schedule</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('dailyPreventive') }}" class="nav-link {{ (request()->is('daily-preventive-maintenance*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-table"></i>
                    <p>Daily Preventive Maintenance</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('returnStocks') }}" class="nav-link {{ (request()->is('return-products*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-table"></i>
                    <p>Return Products</p>
                </a>
            </li>
            <li class="nav-header">SETTINGS</li>
            <li class="nav-item">
                <a href="{{ route('user-profile')}}" class="nav-link {{ (request()->is('profile*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-user-shield"></i>
                    <p>Profile</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.index')}}" class="nav-link {{ (request()->is('user*')) ? 'nav-link active' : '' }}">
                    <i class="nav-icon fas fa-user-plus"></i>
                    <p>User Management</p>
                </a>
            </li>
            @endcan
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>