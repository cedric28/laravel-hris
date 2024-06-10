<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Employee;
use App\EmploymentHistory;
use App\EducationalBackground;
use Validator;

class EmployeeFetchController extends Controller
{
	public function fetchEmployee(Request $request)
	{
		//column list in the table Employee
		$columns = array(
			0 => 'first_name',
   1 => 'nickname',
			2 => 'reference_no',
			3 => 'contact_number',
			4 => 'email',
			5 => 'unit',
			6 => 'created_at',
			7 => 'action'
		);

		//get the total number of data in Employee table
		$totalData = Employee::count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the Employee datatable
		if (empty($request->input('search.value'))) {
			//get all the Supplier data
			$posts = Employee::whereDoesntHave('deployments', function ($query) {
    $query->where('status', 'new');
				})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Employee::count();
		} else {
			$search = $request->input('search.value');

			$posts = Employee::where(function ($query) use ($search) {
				$query->where('first_name', 'like', "%{$search}%")
					->orWhere('middle_name', 'like', "%{$search}%")
					->orWhere('last_name', 'like', "%{$search}%")
     ->orWhere('nickname', 'like', "%{$search}%")
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('unit', 'like', "%{$search}%")
					->orWhere('lot_block', 'like', "%{$search}%")
					->orWhere('street', 'like', "%{$search}%")
					->orWhere('subdivision', 'like', "%{$search}%")
					->orWhere('municipality', 'like', "%{$search}%")
					->orWhere('barangay', 'like', "%{$search}%")
					->orWhere('province', 'like', "%{$search}%")
					->orWhere('contact_number', 'like', "%{$search}%")
					->orWhere('email', 'like', "%{$search}%");
			})
			->whereDoesntHave('deployments', function ($query) {
    $query->where('status', 'new');
				})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Employee table	
			$totalFiltered = Employee::where(function ($query) use ($search) {
				$query->where('first_name', 'like', "%{$search}%")
					->orWhere('middle_name', 'like', "%{$search}%")
					->orWhere('last_name', 'like', "%{$search}%")
     ->orWhere('nickname', 'like', "%{$search}%")
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('unit', 'like', "%{$search}%")
					->orWhere('lot_block', 'like', "%{$search}%")
					->orWhere('street', 'like', "%{$search}%")
					->orWhere('subdivision', 'like', "%{$search}%")
					->orWhere('municipality', 'like', "%{$search}%")
					->orWhere('barangay', 'like', "%{$search}%")
					->orWhere('province', 'like', "%{$search}%")
					->orWhere('contact_number', 'like', "%{$search}%")
					->orWhere('email', 'like', "%{$search}%");
			})
			->whereDoesntHave('deployments', function ($query) {
    $query->where('status', 'new');
				})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['name'] = $r->last_name.', '.$r->first_name. ' '.$r->middle_name;
    $nestedData['nickname'] = $r->nickname;
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['contact_number'] = $r->contact_number;
				$nestedData['email'] = $r->email;
				$nestedData['address'] = $r->unit.' '.$r->lot_block.' '.$r->street.' '.$r->subdivision.' '.$r->barangay.' '.$r->municipality.' '.$r->province;
				$nestedData['created_at'] =date('M d, Y', strtotime($r->created_at));
				$nestedData['action'] = '
                    <button  data-toggle="tooltip" name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm"><i class="nav-icon fas fa-eye"></i></button>
					<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm"><i class="fas fa-pencil-alt"></i></button>
					<button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm"><i class="fas fa-file-archive"></i></button>
				';
				$data[] = $nestedData;
			}
		}

		$json_data = array(
			"draw"			    => intval($request->input('draw')),
			"recordsTotal"	    => intval($totalData),
			"recordsFiltered"   => intval($totalFiltered),
			"data"			    => $data
		);

		//return the data in json response
		return response()->json($json_data);
	}

	public function fetchInactiveEmployee(Request $request)
	{
		//column list in the table Employee
		$columns = array(
			0 => 'first_name',
   1 => 'nickname',
			2 => 'reference_no',
			3 => 'contact_number',
			4 => 'email',
			5 => 'address',
			6 => 'created_at',
			7 => 'action'
		);

		//get the total number of data in Employee table
		$totalData = Employee::onlyTrashed()->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the Employee datatable
		if (empty($request->input('search.value'))) {
			//get all the Employee data
			$posts = Employee::onlyTrashed()
			->whereDoesntHave('deployments', function ($query) {
    $query->where('status', 'new');
				})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Employee::onlyTrashed()->whereDoesntHave('deployments', function ($query) {
    $query->where('status', 'new');
				})->count();
		} else {
			$search = $request->input('search.value');

			$posts = Employee::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->where('first_name', 'like', "%{$search}%")
						->orWhere('middle_name', 'like', "%{$search}%")
						->orWhere('last_name', 'like', "%{$search}%")
      ->orWhere('nickname', 'like', "%{$search}%")
						->orWhere('reference_no', 'like', "%{$search}%")
						->orWhere('address', 'like', "%{$search}%")
						->orWhere('contact_number', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%");
				})
				->whereDoesntHave('deployments', function ($query) {
					$query->where('status', 'new');
					})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Employee table	
			$totalFiltered = Employee::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->where('first_name', 'like', "%{$search}%")
						->orWhere('middle_name', 'like', "%{$search}%")
						->orWhere('last_name', 'like', "%{$search}%")
      ->orWhere('nickname', 'like', "%{$search}%")
						->orWhere('reference_no', 'like', "%{$search}%")
						->orWhere('address', 'like', "%{$search}%")
						->orWhere('contact_number', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%");
				})
				->whereDoesntHave('deployments', function ($query) {
					$query->where('status', 'new');
					})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['name'] = $r->last_name.', '.$r->first_name. ' '.$r->middle_name;
                $nestedData['nickname'] = $r->nickname;
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['contact_number'] = $r->contact_number;
				$nestedData['email'] = $r->email;
				$nestedData['address'] = $r->address;
				$nestedData['created_at'] =date('M d, Y', strtotime($r->created_at));
				$nestedData['action'] = '
                    <button name="restore" id="restore" data-id="' . $r->id . '" class="btn bg-gradient-success btn-sm">Restore</button>
				';
				$data[] = $nestedData;
			}
		}

		$json_data = array(
			"draw"			    => intval($request->input('draw')),
			"recordsTotal"	    => intval($totalData),
			"recordsFiltered"   => intval($totalFiltered),
			"data"			    => $data
		);

		//return the data in json response
		return response()->json($json_data);
	}


	public function fetchEmploymentHistory(Request $request)
	{
		//column list in the table Employee
		$columns = array(
			0 => 'title',
   1 => 'company',
			2 => 'location',
			3 => 'job_description',
			4 => 'action'
		);

		//get the total number of data in Employee table
		$totalData = EmploymentHistory::where('employee_id',$request->employee_id)->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the Employee datatable
		if (empty($request->input('search.value'))) {
			//get all the Supplier data
			$posts = EmploymentHistory::where('employee_id',$request->employee_id)
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = EmploymentHistory::where('employee_id',$request->employee_id)->count();
		} else {
			$search = $request->input('search.value');

			$posts = EmploymentHistory::where(function ($query) use ($search) {
				$query->where('title', 'like', "%{$search}%")
					->orWhere('company', 'like', "%{$search}%")
					->orWhere('location', 'like', "%{$search}%")
     ->orWhere('start_date', 'like', "%{$search}%")
					->orWhere('end_date', 'like', "%{$search}%")
					->orWhere('job_description', 'like', "%{$search}%");
			})
			->orWhereHas('employment_type', function ($query)  use ($search){
    $query->where('name',  'like', "%{$search}%");
				})
				->orWhereHas('industry', function ($query)  use ($search){
					$query->where('name',  'like', "%{$search}%");
					})
				->where('employee_id',$request->employee_id)
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Employee table	
			$totalFiltered = EmploymentHistory::where(function ($query) use ($search) {
				$query->where('title', 'like', "%{$search}%")
					->orWhere('company', 'like', "%{$search}%")
					->orWhere('location', 'like', "%{$search}%")
     ->orWhere('start_date', 'like', "%{$search}%")
					->orWhere('end_date', 'like', "%{$search}%")
					->orWhere('job_description', 'like', "%{$search}%");
			})
			->orWhereHas('employment_type', function ($query)  use ($search){
    $query->where('name',  'like', "%{$search}%");
				})
				->orWhereHas('industry', function ($query)  use ($search){
					$query->where('name',  'like', "%{$search}%");
					})
					->where('employee_id',$request->employee_id)
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['title'] = $r->title;
    $nestedData['employment_type'] = $r->employment_type->name;
				$nestedData['company'] = $r->company;
				$nestedData['location'] = $r->location;
				$nestedData['start_date'] =date('M d, Y', strtotime($r->start_date));
				$nestedData['end_date'] =date('M d, Y', strtotime($r->created_at));
				$nestedData['industry'] = $r->industry->name;
				$nestedData['job_description'] = $r->job_description;
				$nestedData['action'] = '
					<button name="delete_emp_histories" id="delete_emp_histories" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm"><i class="fas fa-file-archive"></i></button>
				';
				$data[] = $nestedData;
			}
		}

		$json_data = array(
			"draw"			    => intval($request->input('draw')),
			"recordsTotal"	    => intval($totalData),
			"recordsFiltered"   => intval($totalFiltered),
			"data"			    => $data
		);

		//return the data in json response
		return response()->json($json_data);
	}


	public function fetchEmploymentEducation(Request $request)
	{
		//column list in the table Employee
		$columns = array(
			0 => 'school_name',
   1 => 'level',
			2 => 'date_graduated',
			3 => 'action'
		);

		//get the total number of data in Employee table
		$totalData = EducationalBackground::where('employee_id',$request->employee_id)->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the Employee datatable
		if (empty($request->input('search.value'))) {
			//get all the Supplier data
			$posts = EducationalBackground::where('employee_id',$request->employee_id)
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = EducationalBackground::where('employee_id',$request->employee_id)->count();
		} else {
			$search = $request->input('search.value');

			$posts = EducationalBackground::where(function ($query) use ($search) {
				$query->where('school_name', 'like', "%{$search}%")
					->orWhere('level', 'like', "%{$search}%")
					->orWhere('date_graduated', 'like', "%{$search}%");
			})
				->where('employee_id',$request->employee_id)
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Employee table	
			$totalFiltered = EducationalBackground::where(function ($query) use ($search) {
				$query->where('school_name', 'like', "%{$search}%")
					->orWhere('level', 'like', "%{$search}%")
					->orWhere('date_graduated', 'like', "%{$search}%");
			})
				->where('employee_id',$request->employee_id)
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['school_name'] = $r->school_name;
    $nestedData['level'] = $r->level;
				$nestedData['date_graduated'] =date('M d, Y', strtotime($r->date_graduated));
				$nestedData['action'] = '
					<button name="delete_emp_education" id="delete_emp_education" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm"><i class="fas fa-file-archive"></i></button>
				';
				$data[] = $nestedData;
			}
		}

		$json_data = array(
			"draw"			    => intval($request->input('draw')),
			"recordsTotal"	    => intval($totalData),
			"recordsFiltered"   => intval($totalFiltered),
			"data"			    => $data
		);

		//return the data in json response
		return response()->json($json_data);
	}
}