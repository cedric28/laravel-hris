<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Log;

class LogFetchController extends Controller
{
    public function fetchLogs(Request $request)
    {
        //column list in the table Customer
        $columns = array(
            0 => 'log',
            1 => 'created_at',
            2 => 'action'
        );

        //get the total number of data in Customer table
        $totalData = Log::count();
        //total number of data that will show in the datatable default 10
        $limit = $request->input('length');
        //start number for pagination ,default 0
        $start = $request->input('start');
        //order list of the column
        $order = $columns[$request->input('order.0.column')];
        //order by ,default asc 
        $dir = $request->input('order.0.dir');

        //check if user search for a value in the Supplier datatable
        if (empty($request->input('search.value'))) {
            //get all the Supplier data
            $posts = Log::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            //total number of filtered data
            $totalFiltered = Log::count();
        } else {
            $search = $request->input('search.value');

            $posts = Log::where(function ($query) use ($search) {
                $query->where('log', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            //total number of filtered data matching the search value request in the Customer table	
            $totalFiltered = Log::where(function ($query) use ($search) {
                $query->where('log', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%");
            })
                ->count();
        }


        $data = array();

        if ($posts) {
            //loop posts collection to transfer in another array $nestedData
            foreach ($posts as $r) {
                $nestedData['log'] = $r->log;
                $nestedData['created_at'] =date('M d, Y', strtotime($r->created_at));
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"                => intval($request->input('draw')),
            "recordsTotal"        => intval($totalData),
            "recordsFiltered"   => intval($totalFiltered),
            "data"                => $data
        );

        //return the data in json response
        return response()->json($json_data);
    }
}
