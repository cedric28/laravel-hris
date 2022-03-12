<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //if has a user redirect to home page if not redirect to login
    if (Auth::user()) return redirect()->route('home');

    return view('auth.login');
});

//register route disable
Auth::routes(['register' => false, 'reset' => false, 'confirm' => false]);

Route::middleware('auth')->group(function () {
    //Dashboard
    Route::get('/dashboard', 'HomeController@index')->name('home');

    //Product 
    Route::resource('/product', 'Product\ProductController')->middleware('can:isAdmin');
    Route::post('product/fetch/q', 'Product\ProductFetchController@fetchProduct')->name('activeProduct');
    Route::get('product/destroy/{id}', 'Product\ProductController@destroy');

    //Category
    Route::resource('/category', 'Category\CategoryController');
    Route::post('category/{id}/restore', 'Category\CategoryController@restore');
    Route::post('category/fetch/q', 'Category\CategoryFetchController@fetchCategory')->name('activeCategory');
    Route::get('category/destroy/{id}', 'Category\CategoryController@destroy');

    //Customer
    Route::resource('/customer', 'Customer\CustomerController');
    Route::post('customer/{id}/restore', 'Customer\CustomerController@restore');
    Route::post('customer/fetch/q', 'Customer\CustomerFetchController@fetchCustomer')->name('activeCustomer');
    Route::get('customer/destroy/{id}', 'Customer\CustomerController@destroy');

    Route::post('check-customer-info/fetch/q', 'Customer\CustomerFetchController@getCustomerInfo');
    Route::post('get-customer-points/fetch/q', 'Customer\CustomerFetchController@getCustomerPoints')->name('getCustomerPoints');

    //Discount
    Route::resource('/discount', 'Discount\DiscountController');
    Route::post('discount/{id}/restore', 'Discount\DiscountController@restore');
    Route::post('discount/fetch/q', 'Discount\DiscountFetchController@fetchDiscount')->name('activeDiscount');
    Route::get('discount/destroy/{id}', 'Discount\DiscountController@destroy');

    Route::get('normal-discounts/fetch/q', 'Discount\DiscountFetchController@fetchNormalDiscounts');

    //Supplier
    Route::resource('/supplier', 'Supplier\SupplierController');
    Route::post('supplier/fetch/q', 'Supplier\SupplierFetchController@fetchSupplier')->name('activeSupplier');
    Route::get('supplier/destroy/{id}', 'Supplier\SupplierController@destroy');

    //Stock
    Route::resource('/stock', 'Stock\StockController');
    Route::post('stock/add-products', 'Stock\StockController@addProduct')->name('addProduct');
    Route::post('deliveries/fetch/q', 'Stock\StockFetchController@fetchDeliveries')->name('activeDeliveries');
    Route::post('stock/fetch/q', 'Stock\StockFetchController@fetchProductsDelivery')->name('activeProductsDelivery');
    Route::post('delivery-request-stock/fetch/q', 'Stock\StockFetchController@fetchDeliveriesRequest')->name('activeDeliveriesRequest');
    Route::post('delivery-request-stock-item/fetch/q', 'Stock\StockFetchController@fetchProductsDeliveryRequest')->name('activeProductsDeliveryRequest');
    Route::post('return-stock/fetch/q', 'Stock\StockFetchController@fetchReturnStock')->name('activeReturnStock');
    Route::post('return-stock-item/fetch/q', 'Stock\StockFetchController@fetchProductsReturnStock')->name('activeProductsReturnStock');
    Route::get('delivery/destroy/{id}', 'Stock\StockController@destroy');
    Route::get('stock/destroy/{id}', 'Stock\StockController@removeProduct');

    //Stock In History
    Route::resource('/history-stock-in', 'Stock\StockInHistoryController');
    Route::post('/history-stock-in/fetch/q', 'Stock\StockFetchController@fetchStockInHistory')->name('activeStockInHistory');

    //Inventory
    Route::resource('/inventory', 'Inventory\InventoryController');
    Route::post('inventory/fetch/q', 'Inventory\InventoryFetchController@fetchInventory')->name('activeInventory');
    Route::get('inventory/destroy/{id}', 'Inventory\InventoryController@destroy');
    Route::get('inventories/fetch/q', 'Inventory\InventoryFetchController@fetchInventoryProducts');
    Route::get('product-adjustment-logs', 'Inventory\InventoryController@productAdjustmentLogs')->name('productAdjustmentLogs');
    Route::post('product-adjustment-logs/fetch/q', 'Inventory\InventoryFetchController@getInventoryAdjustmentProducts')->name('getInventoryAdjustmentProducts');

    Route::get('sales-logs', 'Inventory\InventoryController@salesLog')->name('salesLog');
    Route::post('sales-logs/fetch/q', 'Inventory\InventoryFetchController@getSalesLogs')->name('getSalesLogs');

    //Users
    Route::resource('/user', 'User\UserController');
    Route::post('user/{id}/restore', 'User\UserController@restore');
    Route::post('user/fetch/q', 'User\UserFetchController@fetchUser')->name('activeUser');
    Route::get('user/destroy/{id}', 'User\UserController@destroy');

    //Profile
    Route::get('/profile', 'User\ProfileController@viewProfile')->name('user-profile');
    Route::patch('/profile-update', 'User\ProfileController@updateProfile')->name('update-profile');
    Route::patch('/notification-update', 'User\ProfileController@updateNotification')->name('update-notification');

    //Points
    Route::resource('/point', 'Points\PointsController');
    Route::post('point/{id}/restore', 'Points\PointsController@restore');
    Route::post('point/fetch/q', 'Points\PointsFetchController@fetchPoint')->name('activePoints');
    Route::get('point/destroy/{id}', 'Points\PointsController@destroy');

    Route::get('customer-points/fetch/q', 'Points\PointsFetchController@fetchPointDiscount');

    //POS
    Route::resource('/pos', 'POS\POSController');

    //PDF
    Route::get('invoice/{id}', 'PDF\PDFController@generateInvoice');
    Route::get('generate-pdf-yearly-sales', 'PDF\PDFController@generateSalesYearly')->name('generateSalesYearly');
    Route::get('generate-pdf-monthy-sales', 'PDF\PDFController@generateSalesMonthly')->name('generateSalesMonthly');
    Route::get('generate-pdf-stocks-medical-goods', 'PDF\PDFController@generateStockMedicalGoods')->name('generateStockMedicalGoods');
    Route::get('generate-pdf-delivery-schedule', 'PDF\PDFController@generateDeliverySchedule')->name('generateDeliverySchedule');
    Route::get('generate-pdf-customer-discount', 'PDF\PDFController@generateCustomerDiscount')->name('generateCustomerDiscount');
    Route::get('generate-pdf-daily-preventive', 'PDF\PDFController@generateDailyPreventive')->name('generateDailyPreventive');
    Route::get('generate-pdf-return-products', 'PDF\PDFController@generateReturnStocks')->name('generateReturnStocks');
    Route::get('generate-pdf-order-report', 'PDF\PDFController@generateOrderReport')->name('generateOrderReport');

    //PRINT
    Route::get('print-yearly-sales', 'PDF\PDFController@printSalesYearly')->name('printSalesYearly');
    Route::get('print-monthly-sales', 'PDF\PDFController@printSalesMonthly')->name('printSalesMonthly');
    Route::get('print-stocks-medical-goods', 'PDF\PDFController@printStockMedicalGoods')->name('printStockMedicalGoods');
    Route::get('print-delivery-schedule', 'PDF\PDFController@printDeliverySchedule')->name('printDeliverySchedule');
    Route::get('print-customer-discount', 'PDF\PDFController@printCustomerDiscount')->name('printCustomerDiscount');
    Route::get('print-daily-preventive', 'PDF\PDFController@printDailyPreventive')->name('printDailyPreventive');
    Route::get('print-return-products', 'PDF\PDFController@printReturnStocks')->name('printReturnStocks');
    Route::get('print-order-report', 'PDF\PDFController@printOrderReport')->name('printOrderReport');
    //Reports
    Route::get('sales-report-yearly', 'Reports\SalesReportController@salesYearly')->name('salesYearly');
    Route::get('sales-report-monthly', 'Reports\SalesReportController@salesMonthly')->name('salesMonthly');

    Route::get('reports-customer-discount', 'Reports\CustomerDiscountReportController@customerDiscount')->name('customerDiscount');
    Route::get('good-stocks-report', 'Reports\StockGoodsController@stockGoods')->name('stockGoods');
    Route::get('schedule-delivery', 'Reports\DeliveryScheduleController@deliverySchedule')->name('deliverySchedule');
    Route::get('daily-preventive-maintenance', 'Reports\DailyPreventiveController@dailyPreventive')->name('dailyPreventive');
    Route::get('return-products', 'Reports\ReturnStocksController@returnStocks')->name('returnStocks');
    Route::get('order-report', 'Reports\OrderController@orderReport')->name('orderReport');

    //Delivery Request
    Route::resource('delivery-request', 'Stock\DeliveryRequestController');
    Route::post('delivery-request/add-products', 'Stock\DeliveryRequestController@addProduct')->name('addDeliveryRequestItem');
    Route::post('delivery-request/update-products', 'Stock\DeliveryRequestController@updateProduct')->name('updateDeliveryRequestItem');
    Route::get('delivery-request/destroy/{id}', 'Stock\DeliveryRequestController@destroy');
    Route::get('delivery-request-item/destroy/{id}', 'Stock\DeliveryRequestController@removeProduct');
    //Return Stock
    Route::resource('return-stock', 'Stock\ReturnStockController');
    Route::post('return-stock/add-products', 'Stock\ReturnStockController@addProduct')->name('addReturnStockItem');
    Route::get('return-stock/destroy/{id}', 'Stock\ReturnStockController@destroy');
    Route::get('return-stock-item/destroy/{id}', 'Stock\ReturnStockController@removeProduct');

    //Inventory Level
    Route::resource('inventories-level', 'Inventory\InventoryLevelController');
});
