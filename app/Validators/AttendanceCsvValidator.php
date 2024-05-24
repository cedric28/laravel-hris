namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceCsvValidator
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