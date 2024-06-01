<?php

namespace App\Http\Controllers\FeedBack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Feedback;
use App\Deployment;
use App\Log;
use Validator, Hash, DB;
use Carbon\Carbon;

class FeedBackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $feedbacks = Feedback::all();
        $InactiveFeedback = Feedback::onlyTrashed()->get();

        return view("feedback.index", [
            'feedbacks' => $feedbacks,
            'InactiveFeedback' => $InactiveFeedback
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
         $currentMonth = Carbon::now()->month;
         $deployments = Deployment::where('status','new')->whereDoesntHave('feedbacks', function ($query) use ($currentMonth) {
            $query->whereMonth('created_at', $currentMonth);
        })->get();

        $always_on_time = [ 
            [ 
                'name' => 'yes'
            ],
            [
                'name' => 'no'
            ]
            ];

        $prompt_and_on_time = [ 
            [ 
                'name' => 'yes'
            ],
            [
                'name' => 'no'
            ]
            ];

        $adheres_to_the_schedule = [ 
            [ 
                'name' => 'yes'
            ],
            [
                'name' => 'no'
            ]
            ];


        $very_reliable_at_work = [ 
            [ 
                'name' => 'yes'
            ],
            [
                'name' => 'no'
            ]
            ];

        $inspires_others_to_improve_attendance = [ 
            [ 
                'name' => 'yes'
            ],
            [
                'name' => 'no'
            ]
            ];

        $is_frequently_late_to_work = [ 
            [ 
                'name' => 'yes'
            ],
            [
                'name' => 'no'
            ]
            ];

        $unreliable_about_reporting = [ 
            [ 
                'name' => 'yes'
            ],
            [
                'name' => 'no'
            ]
            ];

        $unwilling_to_work_beyond_scheduled_hours = [ 
            [ 
                'name' => 'yes'
            ],
            [
                'name' => 'no'
            ]
            ];
            
        $not_a_dependable_employee = [ 
            [ 
                'name' => 'yes'
            ],
            [
                'name' => 'no'
            ]
            ];

        $work_results_are_inconsistent = [ 
            [ 
                'name' => 'yes'
            ],
            [
                'name' => 'no'
            ]
            ];

        $rate = [ 
            [ 
                'name' => 0
            ],
            [
                'name' => 1
            ],
            [ 
                'name' => 2
            ],
            [
                'name' => 3
            ],
            [
                'name' => 4
            ],
            [
                'name' => 5
            ],
            [
                'name' => 6
            ],
            [
                'name' => 7
            ],
            [
                'name' => 8
            ],
            [
                'name' => 9
            ],
            [
                'name' => 10
            ]
            ];
 
         return view("feedback.create",[
            'deployments' => $deployments,
            'always_on_time' => $always_on_time,
            'prompt_and_on_time' => $prompt_and_on_time,
            'adheres_to_the_schedule' => $adheres_to_the_schedule,
            'very_reliable_at_work' => $very_reliable_at_work,
            'inspires_others_to_improve_attendance' => $inspires_others_to_improve_attendance,
            'is_frequently_late_to_work' => $is_frequently_late_to_work,
            'unreliable_about_reporting' => $unreliable_about_reporting,
            'unwilling_to_work_beyond_scheduled_hours' => $unwilling_to_work_beyond_scheduled_hours,
            'not_a_dependable_employee' => $not_a_dependable_employee,
            'work_results_are_inconsistent' => $work_results_are_inconsistent,
            'rate' => $rate
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
                'deployment_id.required' => 'Please select a Employee'
            ];
           
            $currentYear = Carbon::now()->year;
            //validate request value
            $validator = Validator::make($request->all(), [
                'deployment_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($currentYear) {

                    $attendance = Feedback::whereYear('created_at',$currentYear)
                    ->where('deployment_id', $value)
                    ->exists();
        
                    if ($attendance) {
                        $fail('Feedback for this Year already assigned to this Employee');
                    }
                },
            ],
                'always_on_time' => 'required|string',
                'prompt_and_on_time' => 'required|string',
                'adheres_to_the_schedule' => 'required|string',
                'very_reliable_at_work' => 'required|string',
                'inspires_others_to_improve_attendance' => 'required|string',
                'is_frequently_late_to_work' => 'required|string',
                'unreliable_about_reporting' => 'required|string',
                'unwilling_to_work_beyond_scheduled_hours' => 'required|string',
                'not_a_dependable_employee' => 'required|string',
                'work_results_are_inconsistent' => 'required|string',
                'rate' => 'required|integer'
            ], $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save feedback
            $feedback = new Feedback();
            $feedback->deployment_id = $request->deployment_id;
            $feedback->always_on_time = $request->always_on_time;
            $feedback->prompt_and_on_time = $request->prompt_and_on_time;
            $feedback->adheres_to_the_schedule = $request->adheres_to_the_schedule;
            $feedback->very_reliable_at_work = $request->very_reliable_at_work;
            $feedback->inspires_others_to_improve_attendance = $request->inspires_others_to_improve_attendance;
            $feedback->is_frequently_late_to_work = $request->is_frequently_late_to_work;
            $feedback->unreliable_about_reporting = $request->unreliable_about_reporting;
            $feedback->unwilling_to_work_beyond_scheduled_hours = $request->unwilling_to_work_beyond_scheduled_hours;
            $feedback->not_a_dependable_employee = $request->not_a_dependable_employee;
            $feedback->work_results_are_inconsistent = $request->work_results_are_inconsistent;
            $feedback->rate = $request->rate;
            $feedback->creator_id = $user;
            $feedback->updater_id = $user;
            $feedback->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " create feedback " . $feedback->id . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('feedback.create')
            ->with('successMsg', 'FeedBack Save Successful');
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

        $feedback = Feedback::withTrashed()->findOrFail($id);
    
        return view('feedback.show', [
            'feedback' => $feedback
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
        $feedback = Feedback::withTrashed()->findOrFail($id);
        $currentYear = Carbon::now()->year;
        $deployments = Deployment::all();


       $always_on_time = [ 
        [ 
            'name' => 'yes'
        ],
        [
            'name' => 'no'
        ]
        ];

    $prompt_and_on_time = [ 
        [ 
            'name' => 'yes'
        ],
        [
            'name' => 'no'
        ]
        ];

    $adheres_to_the_schedule = [ 
        [ 
            'name' => 'yes'
        ],
        [
            'name' => 'no'
        ]
        ];


    $very_reliable_at_work = [ 
        [ 
            'name' => 'yes'
        ],
        [
            'name' => 'no'
        ]
        ];

    $inspires_others_to_improve_attendance = [ 
        [ 
            'name' => 'yes'
        ],
        [
            'name' => 'no'
        ]
        ];

    $is_frequently_late_to_work = [ 
        [ 
            'name' => 'yes'
        ],
        [
            'name' => 'no'
        ]
        ];

    $unreliable_about_reporting = [ 
        [ 
            'name' => 'yes'
        ],
        [
            'name' => 'no'
        ]
        ];

    $unwilling_to_work_beyond_scheduled_hours = [ 
        [ 
            'name' => 'yes'
        ],
        [
            'name' => 'no'
        ]
        ];
        
    $not_a_dependable_employee = [ 
        [ 
            'name' => 'yes'
        ],
        [
            'name' => 'no'
        ]
        ];

    $work_results_are_inconsistent = [ 
        [ 
            'name' => 'yes'
        ],
        [
            'name' => 'no'
        ]
        ];

    $rate = [ 
        [ 
            'name' => 0
        ],
        [
            'name' => 1
        ],
        [ 
            'name' => 2
        ],
        [
            'name' => 3
        ],
        [
            'name' => 4
        ],
        [
            'name' => 5
        ],
        [
            'name' => 6
        ],
        [
            'name' => 7
        ],
        [
            'name' => 8
        ],
        [
            'name' => 9
        ],
        [
            'name' => 10
        ]
        ];


       return view('feedback.edit', [
        'feedback' => $feedback,
        'deployments' => $deployments,
        'always_on_time' => $always_on_time,
        'prompt_and_on_time' => $prompt_and_on_time,
        'adheres_to_the_schedule' => $adheres_to_the_schedule,
        'very_reliable_at_work' => $very_reliable_at_work,
        'inspires_others_to_improve_attendance' => $inspires_others_to_improve_attendance,
        'is_frequently_late_to_work' => $is_frequently_late_to_work,
        'unreliable_about_reporting' => $unreliable_about_reporting,
        'unwilling_to_work_beyond_scheduled_hours' => $unwilling_to_work_beyond_scheduled_hours,
        'not_a_dependable_employee' => $not_a_dependable_employee,
        'work_results_are_inconsistent' => $work_results_are_inconsistent,
        'rate' => $rate
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
            
            //check feedback if exist
            $feedback = Feedback::withTrashed()->findOrFail($id);

   
             //validate request value
             $validator = Validator::make($request->all(), [
                 'always_on_time' => 'required|string',
                 'prompt_and_on_time' => 'required|string',
                 'adheres_to_the_schedule' => 'required|string',
                 'very_reliable_at_work' => 'required|string',
                 'inspires_others_to_improve_attendance' => 'required|string',
                 'is_frequently_late_to_work' => 'required|string',
                 'unreliable_about_reporting' => 'required|string',
                 'unwilling_to_work_beyond_scheduled_hours' => 'required|string',
                 'not_a_dependable_employee' => 'required|string',
                 'work_results_are_inconsistent' => 'required|string',
                 'rate' => 'required|integer'
             ]);
 
             if ($validator->fails()) {
                 return back()->withErrors($validator->errors())->withInput();
             }
 
             //check current user
             $user = \Auth::user()->id;
 
             //save feedback
             $feedback->always_on_time = $request->always_on_time;
             $feedback->prompt_and_on_time = $request->prompt_and_on_time;
             $feedback->adheres_to_the_schedule = $request->adheres_to_the_schedule;
             $feedback->very_reliable_at_work = $request->very_reliable_at_work;
             $feedback->inspires_others_to_improve_attendance = $request->inspires_others_to_improve_attendance;
             $feedback->is_frequently_late_to_work = $request->is_frequently_late_to_work;
             $feedback->unreliable_about_reporting = $request->unreliable_about_reporting;
             $feedback->unwilling_to_work_beyond_scheduled_hours = $request->unwilling_to_work_beyond_scheduled_hours;
             $feedback->not_a_dependable_employee = $request->not_a_dependable_employee;
             $feedback->work_results_are_inconsistent = $request->work_results_are_inconsistent;
             $feedback->rate = $request->rate;
             $feedback->creator_id = $user;
             $feedback->updater_id = $user;
             $feedback->save();
 
             $log = new Log();
             $log->log = "User " . \Auth::user()->email . " edit feedback " .  $feedback->id . " at " . Carbon::now();
             $log->creator_id =  \Auth::user()->id;
             $log->updater_id =  \Auth::user()->id;
             $log->save();
             /*
             | @End Transaction
             |---------------------------------------------*/
             \DB::commit();
 
             return redirect()->route('feedback.edit', $feedback->id)
             ->with('successMsg', 'Feedback Data update Successfully');
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

        //delete Feedback
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete feedback " . $feedback->id . " at " . Carbon::now();
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

            $feedback = Feedback::onlyTrashed()->findOrFail($id);

            /* Restore feedback */
            $feedback->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore feedback " . $feedback->id . " at " . Carbon::now();
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
}
