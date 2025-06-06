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


Route::get('/reset', 'Auth\ResetPasswordController@index')->name('resetPage');
Route::post('/reset-password', 'Auth\ResetPasswordController@resetPassword')->name('resetPassword');

//register route disable
Auth::routes(['register' => false, 'reset' => false, 'confirm' => false]);

Route::middleware('auth')->group(function () {
    //Dashboard
    Route::get('/dashboard', 'HomeController@index')->name('home');

    Route::get('/download', function () {
        $filename = 'attendance-template.xlsx';
        $path = public_path() . '/attendance_file/' . $filename;
    
        if (file_exists($path)) {
            return response()->download($path, $filename, [], 'inline');
        } else {
            abort(404);
        }
    })->name('download');

    Route::get('/download-overtimefile', function () {
        $filename = 'overtime-template.xlsx';
        $path = public_path() . '/overtime_file/' . $filename;
    
        if (file_exists($path)) {
            return response()->download($path, $filename, [], 'inline');
        } else {
            abort(404);
        }
    })->name('downloadOverTimeFile');

    //Users
    Route::resource('/user', 'User\UserController');
    Route::post('user/fetch/q', 'User\UserFetchController@fetchUser')->name('activeUser');
    Route::post('inactive-user/fetch/q', 'User\UserFetchController@fetchInactiveUser')->name('InactiveUser');
    Route::get('user/destroy/{id}', 'User\UserController@destroy');
    Route::get('user/restore/{id}', 'User\UserController@restore');

    //Profile
    Route::get('/profile', 'User\ProfileController@viewProfile')->name('user-profile');
    Route::patch('/profile-update', 'User\ProfileController@updateProfile')->name('update-profile');
    Route::patch('/notification-update', 'User\ProfileController@updateNotification')->name('update-notification');

    //Client
    Route::resource('/client', 'Client\ClientController');
    Route::post('client/fetch/q', 'Client\ClientFetchController@fetchClient')->name('activeClient');
    Route::post('inactive-client/fetch/q', 'Client\ClientFetchController@fetchInactiveClient')->name('InactiveClient');
    Route::get('client/destroy/{id}', 'Client\ClientController@destroy');
    Route::get('client/restore/{id}', 'Client\ClientController@restore');

    //Employee
    Route::resource('/employee', 'Employee\EmployeeController');
    Route::post('employee/fetch/q', 'Employee\EmployeeFetchController@fetchEmployee')->name('activeEmployee');
    Route::post('inactive-employee/fetch/q', 'Employee\EmployeeFetchController@fetchInactiveEmployee')->name('InactiveEmployee');
    Route::post('employee-histories/fetch/q', 'Employee\EmployeeFetchController@fetchEmploymentHistory')->name('activeEmployeeHistories');
    Route::post('employee-educations/fetch/q', 'Employee\EmployeeFetchController@fetchEmploymentEducation')->name('activeEmployeeEducations');
    Route::get('employee-history/destroy/{id}', 'Employee\EmployeeController@destroyEmploymentHistory');
    Route::get('employee-education/destroy/{id}', 'Employee\EmployeeController@destroyEmploymentEducation');
    Route::get('employee/destroy/{id}', 'Employee\EmployeeController@destroy');
    Route::get('employee/restore/{id}', 'Employee\EmployeeController@restore');


    //Deployment
    Route::resource('/deployment', 'Deployment\DeploymentController');
    Route::post('deployment/fetch/q', 'Deployment\DeploymentFetchController@fetchDeployment')->name('activeDeployment');
    Route::post('inactive-deployment/fetch/q', 'Deployment\DeploymentFetchController@fetchInactiveDeployment')->name('InactiveDeployment');
    Route::get('deployment/destroy/{id}', 'Deployment\DeploymentController@destroy');
    Route::get('deployment/work-details/{id}', 'Deployment\DeploymentController@workDetails')->name('workDetails');
    Route::get('deployment/restore/{id}', 'Deployment\DeploymentController@restore');


    //Schedule
    Route::resource('/schedule', 'Schedule\ScheduleController');
    Route::post('schedule/fetch/q', 'Schedule\ScheduleFetchController@fetchSchedule')->name('activeSchedule');
    Route::post('inactive-schedule/fetch/q', 'Schedule\ScheduleFetchController@fetchInactiveSchedule')->name('InactiveSchedule');
    Route::get('schedule/destroy/{id}', 'Schedule\ScheduleController@destroy');
    Route::get('schedule/restore/{id}', 'Schedule\ScheduleController@restore');

    //Leaves
    Route::resource('/leaves', 'Leaves\LeavesController');
    Route::post('leaves/fetch/q', 'Leaves\LeavesFetchController@fetchLeaves')->name('activeLeaves');
    Route::post('inactive-leaves/fetch/q', 'Leaves\LeavesFetchController@fetchInactiveLeaves')->name('InactiveLeaves');
    Route::get('leaves/destroy/{id}', 'Leaves\LeavesController@destroy');
    Route::get('leaves/restore/{id}', 'Leaves\LeavesController@restore');

    //Attendance
    Route::resource('/attendance', 'Attendance\AttendanceController');
    Route::post('bulk-attendance', 'Attendance\AttendanceController@bulkAttendance')->name('bulkAttendance');
    Route::post('attendance/fetch/q', 'Attendance\AttendanceFetchController@fetchAttendance')->name('activeAttendance');
    Route::post('attendance-bulk/fetch/q', 'Attendance\AttendanceFetchController@fetchAttendanceBulkUpload')->name('activeAttendanceBulkUpload');
    Route::post('inactive-attendance/fetch/q', 'Attendance\AttendanceFetchController@fetchInactiveAttendace')->name('InactiveAttendance');
    Route::get('attendance/destroy/{id}', 'Attendance\AttendanceController@destroy');
    Route::get('attendance/restore/{id}', 'Attendance\AttendanceController@restore');


    //OverTime
    Route::resource('/overtime', 'OverTime\OverTimeController');
    Route::post('bulk-overtime', 'OverTime\OverTimeController@bulkOverTime')->name('bulkOverTime');
    Route::post('overtime/fetch/q', 'OverTime\OverTimeFetchController@fetchOverTime')->name('activeOverTime');
    Route::post('inactive-overtime/fetch/q', 'OverTime\OverTimeFetchController@fetchInactiveAttendace')->name('InactiveOverTime');
    Route::get('overtime/destroy/{id}', 'OverTime\OverTimeController@destroy');
    Route::get('overtime/restore/{id}', 'OverTime\OverTimeController@restore');

    //Salary
    Route::resource('/salary', 'Salary\SalaryController');
    Route::post('compensation/fetch/q', 'Salary\SalaryFetchController@fetchCompensation')->name('activeCompensation');
    Route::get('generate-payslip/{id}', 'PDF\PDFController@generatePayslip')->name('generatePayslip');

    //Late
    Route::post('late/fetch/q', 'Late\LateFetchController@fetchLate')->name('activeLate');


    //Perfect Attendance
    Route::resource('/perfect-attendance', 'PerfectAttendance\PerfectAttendanceController');
    Route::post('perfect-attendance/fetch/q', 'PerfectAttendance\PerfectAttendanceFetchController@fetchPerfectAttendance')->name('activePerfectAttendance');
    Route::get('generate-certificate-perfect-attendance/{id}', 'PDF\PDFController@generatePerfectAttendance')->name('generatePerfectAttendance');

    //For Regularization
    Route::resource('/for-regularization', 'Regularization\RegularizationController');
    Route::post('for-regularization/fetch/q', 'Regularization\RegularizationFetchController@fetchForRegularization')->name('activeForRegularization');
    Route::get('generate-certificate-regularization/{id}', 'PDF\PDFController@generateForRegularization')->name('generateForRegularization');

    //Backup
    Route::resource('/backup-database', 'Backup\BackupController');
    Route::post('/backup-database', 'Backup\BackupController@store')->name('backupDatabase');

    //Best Performer
    Route::resource('/best-performer', 'BestPerformer\BestPerformerController');
    Route::post('best-performer/fetch/q', 'BestPerformer\BestPerformerFetchController@fetchBestPerformer')->name('activeBestPerformer');
    Route::get('generate-certificate-best-performer/{id}', 'PDF\PDFController@generateBestPerformer')->name('generateBestPerformer');
    
    //FeedBack
    Route::resource('/feedback', 'FeedBack\FeedBackController');
    Route::post('feedback/fetch/q', 'FeedBack\FeedBackFetchController@fetchFeedBack')->name('activeFeedBack');
    Route::post('inactive-feedback/fetch/q', 'FeedBack\FeedBackFetchController@fetchInactiveFeedBack')->name('InactiveFeedBack');
    Route::get('feedback/destroy/{id}', 'FeedBack\FeedBackController@destroy');
    Route::get('feedback/restore/{id}', 'FeedBack\FeedBackController@restore');

    
    //For Termination
    Route::resource('/for-termination', 'Disciplinary\DisciplinaryController');
    Route::post('for-termination/fetch/q', 'Disciplinary\DisciplinaryFetchController@fetchForTermination')->name('activeForTermination');


    //Holidays
    Route::resource('/holiday-setting', 'HolidaySetting\HolidaySettingController');
    Route::post('holiday-setting/fetch/q', 'HolidaySetting\HolidaySettingFetchController@fetchHolidays')->name('activeHolidays');
    Route::post('inactive-holiday-setting/fetch/q', 'HolidaySetting\HolidaySettingFetchController@fetchInactiveHolidays')->name('InactiveHolidays');
    Route::get('holiday-setting/destroy/{id}', 'HolidaySetting\HolidaySettingController@destroy');
    Route::get('holiday-setting/restore/{id}', 'HolidaySetting\HolidaySettingController@restore');

    //Payroll
    Route::resource('/payroll', 'Payroll\PayrollController');
    Route::post('payroll/fetch/q', 'Payroll\PayrollFetchController@fetchPayroll')->name('activePayroll');
    Route::post('inactive-payroll/fetch/q', 'Payroll\PayrollFetchController@fetchInactivePayroll')->name('InactivePayroll');
    Route::get('payroll/destroy/{id}', 'Payroll\PayrollController@destroy');
    Route::get('payroll/restore/{id}', 'Payroll\PayrollController@restore');


    //Payslip
    Route::resource('/payslip', 'Payslip\PayslipController');
    Route::post('/check-work-details', 'Payslip\PayslipController@checkWorkDetails')->name('checkWorkDetails');
    Route::post('payslip/fetch/q', 'Payslip\PayslipFetchController@fetchPayslip')->name('activePayslip');
    Route::post('inactive-payslip/fetch/q', 'Payslip\PayslipFetchController@fetchInactivePayslip')->name('InactivePayslip');
    Route::get('payslip/destroy/{id}', 'Payslip\PayslipController@destroy');
    Route::get('payslip/restore/{id}', 'Payslip\PayslipController@restore');


    //General deductions
    Route::resource('/general-deductions', 'GeneralDeduction\GeneralDeductionController');
    Route::post('general-deductions/fetch/q', 'GeneralDeduction\GeneralDeductionFetchController@fetchGeneralDeductions')->name('activeGeneralDeduction');
    Route::post('inactive-general-deductions/fetch/q', 'GeneralDeduction\GeneralDeductionFetchController@fetchInactiveGeneralDeductions')->name('InactiveGeneralDeduction');
    Route::get('general-deductions/destroy/{id}', 'GeneralDeduction\GeneralDeductionController@destroy');
    Route::get('general-deductions/restore/{id}', 'GeneralDeduction\GeneralDeductionController@restore');

    //Logs
    Route::resource('logs', 'Logs\LogController');

    Route::post('logs/fetch/q', 'Logs\LogFetchController@fetchLogs')->name('activityLogs');
});
