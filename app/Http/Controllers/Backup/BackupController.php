<?php

namespace App\Http\Controllers\Backup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Console\Scheduling\Schedule;


class BackupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tables = DB::select('SHOW TABLES');

        $tableCounts = [];
        
        $databaseName = env('DB_DATABASE');
        
        foreach ($tables as $table) {
            // Get the table name dynamically using the property that holds the table name
            $tableNameField = 'Tables_in_' . $databaseName;
            if (property_exists($table, $tableNameField)) {
                $tableName = $table->{$tableNameField};
                $tableCounts[$tableName] = DB::table($tableName)->count();
            }
        }
        
        return view("backup-database.index",['tableCounts' => count($tableCounts)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
         
        Artisan::call('db:backup');
         // Get the output (if needed)
         $output = Artisan::output();

         // You can also check the exit code to see if it was successful
         $exitCode = Artisan::call('db:backup');
        
         if ($exitCode === 0) {
                // Return a success response with a Swal alert message
            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully! Kindly check in the backups folder.',
            ]);
         } else {
            return response()->json(['error' => 'Backup command failed'], 500);
         }

        } catch (ProcessFailedException $e) {
            // Handle the exception
            Log::error($e->getMessage());
            // You can also return an error response, throw a custom exception, etc.
            return response()->json(['error' => 'Backup command failed'], 500);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
