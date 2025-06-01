<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Log;
use App\Employee;
use App\EmploymentType;
use App\Industry;
use Carbon\Carbon;
use App\Gender;
use App\CivilStatus;
use App\EmploymentHistory;
use App\EducationalBackground;
use Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::all();
        $InactiveEmployee = Employee::onlyTrashed()->get();
        $imagePath = public_path('assets/img/logo.png');
        $base64Logo = 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath));
        $currentUser = \Auth::user()->first_name . ' ' . \Auth::user()->last_name;
        return view("employee.index", [
            'employees' => $employees,
            'InactiveEmployee' => $InactiveEmployee,
            'base64Logo' => $base64Logo,
            'currentUser' => $currentUser
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //prevent other user to access to this page
        $this->authorize("isHROrAdmin");

        $civilStatus =  CivilStatus::all();
        $gender = Gender::all();

        return view("employee.create",[
            'civilStatus' => $civilStatus,
            'gender' => $gender
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         //prevent other user to access to this page
         $this->authorize("isHROrAdmin");
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {

            $messages = [
                'gender_id.required' => 'Please select a Sex',
                'civil_status_id.required' => 'Please select a Civil Status'
            ];
            //validate request value
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:50',
                'middle_name' => 'string|max:50',
                'last_name' => 'required|string|max:50',   
                'nickname' => 'required|string|max:10',
                'gender_id' => 'required|integer',
                'birthdate' => 'required|string',
                'civil_status_id' => 'required|integer',
                'unit' => 'required|string|max:50',
                'lot_block' => 'required|string|max:50',
                'street' => 'required|string|max:50',
                'subdivision' => 'required|string|max:50',
                'municipality' => 'required|string|max:50',
                'barangay' => 'required|string|max:50',
                'province' => 'required|string|max:50',
                'zip' => 'required|string|max:50',
                'nationality' => 'required|string|max:50',
                'religion' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'email' => 'required|email|max:50',
                'sss_file' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'pagibig_file' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'tin_file' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'philhealth_file' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'nbi_file' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'emergency_contact_name' => 'required|string|max:50',
                'emergency_relationship' => 'required|string|max:10',
                'emergency_address' => 'required|string|max:50',
                'emergency_contact_number' => 'required|digits:10',
                'sss' => 'required|digits:12',
                'pagibig' => 'required|digits:12',
                'philhealth' => 'required|digits:12',
                'tin' =>  'required|digits:12',
                'nbi' =>  'required|digits:12'
            ], $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            $sssOriginalImage = $request->file('sss_file');
            $sssFile = time() . $sssOriginalImage->getClientOriginalName();

            $pagibigOriginalImage = $request->file('pagibig_file');
            $pagibigFile = time() . $pagibigOriginalImage->getClientOriginalName();

            $tinOriginalImage = $request->file('tin_file');
            $tinFile = time() . $tinOriginalImage->getClientOriginalName();

            $philhealthOriginalImage = $request->file('philhealth_file');
            $philhealthFile = time() . $philhealthOriginalImage->getClientOriginalName();

            $nbiOriginalImage = $request->file('nbi_file');
            $nbiFile = time() . $nbiOriginalImage->getClientOriginalName();

            //save employee
            $employee = new Employee();
            $employee->reference_no = $this->generateUniqueCode();
            $employee->first_name = $request->first_name;
            $employee->middle_name = $request->middle_name ?? '';
            $employee->last_name = $request->last_name;
            $employee->nickname = $request->nickname;
            $employee->nationality = $request->nationality;
            $employee->religion = $request->religion;
            $employee->gender_id = $request->gender_id;
            $employee->civil_status_id = $request->civil_status_id;
            $employee->birthdate = $request->birthdate;
            $employee->unit = $request->unit;
            $employee->lot_block = $request->lot_block;
            $employee->street = $request->street;
            $employee->subdivision = $request->subdivision;
            $employee->municipality = $request->municipality;
            $employee->barangay = $request->barangay;
            $employee->province = $request->province;
            $employee->zip = $request->zip;
            $employee->contact_number = $request->contact_number;
            $employee->email = $request->email;
            $employee->sss = $request->sss;
            $employee->sss_file = $sssFile;
            $employee->pagibig = $request->pagibig;
            $employee->pagibig_file = $pagibigFile;
            $employee->tin = $request->tin;
            $employee->tin_file = $tinFile;
            $employee->philhealth = $request->philhealth;
            $employee->philhealth_file = $philhealthFile;
            $employee->nbi = $request->nbi;
            $employee->nbi_file = $nbiFile;
            $employee->emergency_contact_name = $request->emergency_contact_name;
            $employee->emergency_relationship = $request->emergency_relationship;
            $employee->emergency_address = $request->emergency_address;
            $employee->emergency_contact_number = $request->emergency_contact_number;
            $employee->creator_id = $user;
            $employee->updater_id = $user;
            if ($employee->save()) {
                //sss
                $photoPathSSS = public_path('images/sss/' . $employee->id . '/');

                if (!file_exists($photoPathSSS)) {
                    mkdir($photoPathSSS, 0777, true);
                }
                // create instance
                $imgSSS = \Image::make($sssOriginalImage->getRealPath());

                // resize image to fixed size
                $imgSSS->resize(500, 500);
                $imgSSS->save($photoPathSSS . $sssFile);

                //pagibig
                $photoPathPagibig = public_path('images/pagibig/' . $employee->id . '/');

                if (!file_exists($photoPathPagibig)) {
                    mkdir($photoPathPagibig, 0777, true);
                }
                // create instance
                $imgPagibig = \Image::make($pagibigOriginalImage->getRealPath());

                // resize image to fixed size
                $imgPagibig->resize(500, 500);
                $imgPagibig->save($photoPathPagibig . $pagibigFile);

                //tin
                $photoPathTin = public_path('images/tin/' . $employee->id . '/');

                if (!file_exists($photoPathTin)) {
                    mkdir($photoPathTin, 0777, true);
                }
                // create instance
                $imgTin = \Image::make($tinOriginalImage->getRealPath());

                // resize image to fixed size
                $imgTin->resize(500, 500);
                $imgTin->save($photoPathTin . $tinFile);


                //philhealth
                $photoPathPhilhealth = public_path('images/philhealth/' . $employee->id . '/');

                if (!file_exists($photoPathPhilhealth)) {
                    mkdir($photoPathPhilhealth, 0777, true);
                }
                // create instance
                $imgPhilhealth = \Image::make($philhealthOriginalImage->getRealPath());

                // resize image to fixed size
                $imgPhilhealth->resize(500, 500);
                $imgPhilhealth->save($photoPathPhilhealth . $philhealthFile);


                //nbi
                $photoPathNBI = public_path('images/nbi/' . $employee->id . '/');

                if (!file_exists($photoPathNBI)) {
                    mkdir($photoPathNBI, 0777, true);
                }
                // create instance
                $imgNBI = \Image::make($nbiOriginalImage->getRealPath());

                // resize image to fixed size
                $imgNBI->resize(500, 500);
                $imgNBI->save($photoPathNBI . $nbiFile);
            }

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " create employee " . $employee->reference_no . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('employee.edit', $employee->id);
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         //prevent other user to access to this page
         $this->authorize("isHROrAdmin");

        $employee = Employee::withTrashed()->findOrFail($id);
        $employmentTypes = EmploymentType::all();
        $industries = Industry::all();
        $civilStatus =  CivilStatus::all();
        $gender = Gender::all();

        $employment_histories = $employee->employment_histories;
        $educ_backgrounds = $employee->educ_backgrounds;

        $birthdate = Carbon::createFromFormat('Y-m-d', $employee->birthdate);

        $age = $birthdate->diffInYears();

        return view('employee.show', [
            'employee' => $employee,
            'employmentTypes' => $employmentTypes,
            'industries' => $industries,
            'civilStatus' => $civilStatus,
            'gender' => $gender,
            'employment_histories' => $employment_histories,
            'educ_backgrounds' => $educ_backgrounds,
            'age' => $age
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         //prevent other user to access to this page
         $this->authorize("isHROrAdmin");

        $employee = Employee::withTrashed()->findOrFail($id);
        $employmentTypes = EmploymentType::all();
        $industries = Industry::all();
        $civilStatus =  CivilStatus::all();
        $gender = Gender::all();

        $employment_histories = $employee->employment_histories;
        $educ_backgrounds = $employee->educ_backgrounds;


        $educationLevel = [ 
            [ 
                'name' => 'Primary'
            ],
            [
                'name' => 'Secondary'
            ],
            [
                'name' => 'Tertiary'
            ], 
            [
                'name' => 'Vocational'
            ]
        ];
   
    
        return view('employee.edit', [
            'employee' => $employee,
            'employmentTypes' => $employmentTypes,
            'industries' => $industries,
            'civilStatus' => $civilStatus,
            'gender' => $gender,
            'employment_histories' => $employment_histories,
            'educ_backgrounds' => $educ_backgrounds,
            'educationLevel' => $educationLevel
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         //prevent other user to access to this page
         $this->authorize("isHROrAdmin");

        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            //check employee if exist
            $employee = Employee::withTrashed()->findOrFail($id);

            $messages = [
                'gender_id.required' => 'Please select a Sex',
                'civil_status_id.required' => 'Please select a Civil Status',
                'employment_histories.*.required' => 'Please Add atleast 1 Employment History',
                'educational_histories.*.required' => 'Please Add atleast 1 Educational Background',
                'employmentTypes.*.integer' => 'Please select Employment Type',
                'startdate.*.required' => 'Please select Start Date',
                'enddate.*.required' => 'Please select Start Date',
                'level.*.required' => 'Please select Education Level',
                'date_graduated.*.required' => 'Please select Date Graduated',
                'industries*.required' => 'Please select Nature of Work'
            ];

            //validate the request value
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'middle_name' => 'nullable|string|max:50',
                'last_name' => 'required|string',
                'nickname' => 'required|string|max:10',
                'gender_id' => 'required|integer',
                'birthdate' => 'required|string',
                'civil_status_id' => 'required|integer',
                'unit' => 'required|string|max:50',
                'lot_block' => 'required|string|max:50',
                'street' => 'required|string|max:50',
                'subdivision' => 'required|string|max:50',
                'municipality' => 'required|string|max:50',
                'barangay' => 'required|string|max:50',
                'province' => 'required|string|max:50',
                'zip' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'email' => 'required|email|max:50',
                'employment_histories' => 'array',
                'educational_histories' => 'array',
                'title.*' => 'nullable|string',
                'employmentTypes.*' => 'nullable|integer',
                'company.*' => 'nullable|string',
                'location.*' => 'nullable|string',
                'startdate.*' => 'nullable|string',
                'enddate.*' => 'nullable|string',
                'industries.*' => 'nullable|integer',
                'job_description.*' => 'nullable|string',
                'school_name.*' => 'nullable|string',
                'level.*' => 'nullable|string',
                'date_graduated.*' => 'nullable|string',
                'nationality' => 'required|string|max:50',
                'religion' => 'required|string|max:50',
                'emergency_contact_name' => 'required|string|max:50',
                'emergency_relationship' => 'required|string|max:10',
                'emergency_address' => 'required|string|max:50',
                'emergency_contact_number' => 'required|digits:10',
                'sss' => 'required|digits:12',
                'pagibig' => 'required|digits:12',
                'philhealth' => 'required|digits:12',
                'tin' =>  'required|digits:12',
                'nbi' =>  'required|digits:12',
                'sss_file' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'pagibig_file' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'tin_file' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'philhealth_file' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'nbi_file' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ],$messages);
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            $originalImageSSS = $request->file('sss_file');
            $currentPhotoSSS = $employee->sss_file;

            $originalImagePagibig = $request->file('pagibig_file');
            $currentPhotoPagibig = $employee->pagibig_file;

            $originalImageTin = $request->file('tin_file');
            $currentPhotoTin = $employee->tin_file;

            $originalImagePhilhealth = $request->file('philhealth_file');
            $currentPhotoPhilhealth = $employee->philhealth_file;

            $originalImageNbi = $request->file('nbi_file');
            $currentPhotoNbi = $employee->nbi_file;

            $sssPhoto = "";
            if ($originalImageSSS) {
                $sssPhoto = time() . $originalImageSSS->getClientOriginalName();
                $sssPhotos = public_path('images/sss/' . $employee->id . '/') . $currentPhotoSSS;
                $photoPath = public_path('images/sss/' . $employee->id . '/');
                if (!file_exists($sssPhotos)) {
                    mkdir($photoPath, 0777, true);
                } else {
                    @unlink($sssPhotos);
                }
                // create instance
                $img = \Image::make($originalImageSSS->getRealPath());

                // resize image to fixed size
                $img->resize(500, 500);
                $img->save($photoPath . $sssPhoto);
            } else {
                $sssPhoto = $currentPhotoSSS;
            }

            // 'pagibig_file' 
            $pagibigPhoto = "";
            if ($originalImagePagibig) {
                $pagibigPhoto = time() . $originalImagePagibig->getClientOriginalName();
                $pagibigPhotos = public_path('images/pagibig/' . $employee->id . '/') . $currentPhotoPagibig;
                $photoPath = public_path('images/pagibig/' . $employee->id . '/');
                if (!file_exists($pagibigPhotos)) {
                    mkdir($photoPath, 0777, true);
                } else {
                    @unlink($pagibigPhotos);
                }
                // create instance
                $img = \Image::make($originalImagePagibig->getRealPath());

                // resize image to fixed size
                $img->resize(500, 500);
                $img->save($photoPath . $pagibigPhoto);
            } else {
                $pagibigPhoto = $currentPhotoPagibig;
            }

            // 'tin_file' 
            $tinPhoto = "";
            if ($originalImageTin) {
                $tinPhoto = time() . $originalImageTin->getClientOriginalName();
                $tinPhotos = public_path('images/tin/' . $employee->id . '/') . $currentPhotoTin;
                $photoPath = public_path('images/tin/' . $employee->id . '/');
                if (!file_exists($tinPhotos)) {
                    mkdir($photoPath, 0777, true);
                } else {
                    @unlink($tinPhotos);
                }
                // create instance
                $img = \Image::make($originalImageTin->getRealPath());

                // resize image to fixed size
                $img->resize(500, 500);
                $img->save($photoPath . $tinPhoto);
            } else {
                $tinPhoto = $currentPhotoTin;
            }

            // 'philhealth_file' 
            $philhealthPhoto = "";
            if ($originalImagePhilhealth) {
                $philhealthPhoto = time() . $originalImagePhilhealth->getClientOriginalName();
                $phihealthPhotos = public_path('images/philhealth/' . $employee->id . '/') . $currentPhotoTin;
                $photoPath = public_path('images/philhealth/' . $employee->id . '/');
                if (!file_exists($phihealthPhotos)) {
                    mkdir($photoPath, 0777, true);
                } else {
                    @unlink($phihealthPhotos);
                }
                // create instance
                $img = \Image::make($originalImagePhilhealth->getRealPath());

                // resize image to fixed size
                $img->resize(500, 500);
                $img->save($photoPath . $philhealthPhoto);
            } else {
                $philhealthPhoto = $currentPhotoPhilhealth;
            }


            // 'nbi_file' 
            $nbiPhoto = "";
            if ($originalImagePhilhealth) {
                $nbiPhoto = time() . $originalImageNbi->getClientOriginalName();
                $nbiPhotos = public_path('images/nbi/' . $employee->id . '/') . $currentPhotoNbi;
                $photoPath = public_path('images/nbi/' . $employee->id . '/');
                if (!file_exists($nbiPhotos)) {
                    mkdir($photoPath, 0777, true);
                } else {
                    @unlink($nbiPhotos);
                }
                // create instance
                $img = \Image::make($originalImageNbi->getRealPath());

                // resize image to fixed size
                $img->resize(500, 500);
                $img->save($photoPath . $nbiPhoto);
            } else {
                $nbiPhoto = $currentPhotoNbi;
            }

            //save the update value
            $employee->first_name = $request->first_name;
            $employee->middle_name = $request->middle_name ?? '';
            $employee->last_name = $request->last_name;
            $employee->nickname = $request->nickname;
            $employee->gender_id = $request->gender_id;
            $employee->civil_status_id = $request->civil_status_id;
            $employee->religion = $request->religion;
            $employee->nationality = $request->nationality;
            $employee->birthdate = $request->birthdate;
            $employee->unit = $request->unit;
            $employee->lot_block = $request->lot_block;
            $employee->street = $request->street;
            $employee->subdivision = $request->subdivision;
            $employee->municipality = $request->municipality;
            $employee->barangay = $request->barangay;
            $employee->province = $request->province;
            $employee->zip = $request->zip;
            $employee->contact_number = $request->contact_number;
            $employee->emergency_contact_name = $request->emergency_contact_name;
            $employee->emergency_relationship = $request->emergency_relationship;
            $employee->emergency_address = $request->emergency_address;
            $employee->emergency_contact_number = $request->emergency_contact_number;
            $employee->sss = $request->sss;
            $employee->sss_file = $sssPhoto;
            $employee->pagibig = $request->pagibig;
            $employee->pagibig_file = $pagibigPhoto;
            $employee->tin = $request->tin;
            $employee->tin_file = $tinPhoto;
            $employee->philhealth = $request->philhealth;
            $employee->philhealth_file = $philhealthPhoto;
            $employee->nbi = $request->nbi;
            $employee->nbi_file = $nbiPhoto;
            $employee->email = $request->email;
            $employee->updater_id = $user;
            $employee->update();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edit employee " . $employee->reference_no . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            $titles = $request->input('title', []);
            $employmentTypes = $request->input('employmentTypes', []);
            $company = $request->input('company', []);
            $location = $request->input('location', []);
            $startdate = $request->input('startdate', []);
            $enddate = $request->input('enddate', []);
            $industries = $request->input('industries', []);
            $job_description = $request->input('job_description', []);
            for ($i = 0; $i < count($titles); $i++) {
                if ($titles[$i] != '') {
                    $employment = EmploymentHistory::firstOrNew([
                        'employee_id' => $employee->id,
                    ]);
                    $employment->employment_type_id = $employmentTypes[$i] ?? '';
                    $employment->title = $titles[$i] ?? '';
                    $employment->company = $company[$i] ?? '';
                    $employment->location = $location[$i] ?? '';
                    $employment->start_date = Carbon::parse($startdate[$i])->format('Y-m-d');
                    $employment->end_date = Carbon::parse($enddate[$i])->format('Y-m-d');
                    $employment->industry_id = $industries[$i] ?? '';
                    $employment->job_description = $job_description[$i] ?? '';
                    $employment->creator_id = $user;
                    $employment->updater_id = $user;
                    $employment->save();

                    $log = new Log();
                    $log->log = "User " . \Auth::user()->email . " add employment history " . $employment->title . " at " . Carbon::now();
                    $log->creator_id =  \Auth::user()->id;
                    $log->updater_id =  \Auth::user()->id;
                    $log->save();
                }
            }


            $school_names = $request->input('school_name', []);
            $date_graduated = $request->input('date_graduated', []);
            $level = $request->input('level', []);
            for ($i = 0; $i < count($school_names); $i++) {
                if ($school_names[$i] != '') {
                    $education = EducationalBackground::firstOrNew([
                        'employee_id' => $employee->id,
                        'level' => $employee->level
                    ]);
                    $education->school_name = $school_names[$i];
                    $education->date_graduated = Carbon::parse($date_graduated[$i])->format('Y-m-d');
                    $education->level = $level[$i];
                    $education->creator_id = $user;
                    $education->updater_id = $user;
                    $education->save();

                    $log = new Log();
                    $log->log = "User " . \Auth::user()->email . " add educational background " . $education->school_name . " at " . Carbon::now();
                    $log->creator_id =  \Auth::user()->id;
                    $log->updater_id =  \Auth::user()->id;
                    $log->save();
                }
            }
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg", "Employee Personal Information Update Successfully");
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
          //prevent other user to access to this page
          $this->authorize("isHROrAdmin");

        //delete employee
        $employee = Employee::findOrFail($id);
        $employee->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete employee " . $employee->reference_no . " at " . Carbon::now();
        $log->creator_id =  \Auth::user()->id;
        $log->updater_id =  \Auth::user()->id;
        $log->save();
    }

    public function destroyEmploymentHistory($id)
    {
          //prevent other user to access to this page
          $this->authorize("isHROrAdmin");
        //delete employee
        $employeeHistory = EmploymentHistory::findOrFail($id);
        $employeeHistory->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete employment history " . $employeeHistory->id . " at " . Carbon::now();
        $log->creator_id =  \Auth::user()->id;
        $log->updater_id =  \Auth::user()->id;
        $log->save();
    }

    public function destroyEmploymentEducation($id)
    {
          //prevent other user to access to this page
          $this->authorize("isHROrAdmin");
        //delete employee
        $employeeEducation = EducationalBackground::findOrFail($id);
        $employeeEducation->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete employment educational background " . $employeeEducation->id . " at " . Carbon::now();
        $log->creator_id =  \Auth::user()->id;
        $log->updater_id =  \Auth::user()->id;
        $log->save();
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        \DB::beginTransaction();
        try {

            $employee = Employee::onlyTrashed()->findOrFail($id);

            /* Restore employee */
            $employee->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore employee " . $employee->reference_no . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            \DB::commit();
            return back()->with("successMsg", "Successfully Restore the data");
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }

    public function generateUniqueCode()
    {
        do {
            $reference_no = random_int(1000000000, 9999999999);
        } while (Employee::where("reference_no", "=", $reference_no)->first());

        return $reference_no;
    }
}