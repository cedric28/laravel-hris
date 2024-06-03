<?php

namespace App\Validators;
use App\Attendance;

use Carbon\Carbon;

class AttendanceCsvValidator extends \Illuminate\Validation\Validator
{
   public function validateNotWeekend($attribute, $value, $parameters, $validator)
   {
       $date = Carbon::parse($value);
       if ($date->isWeekend()) {
           return false;
       }
       return true;
   }

   public function validateNotDuplicate($attribute, $value, $parameters, $validator)
   {
       $attendance = Attendance::where('attendance_date', $value)->first();
       if ($attendance) {
           return false;
       }
       return true;
   }
}