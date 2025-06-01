<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Log;
use App\Client;
use Carbon\Carbon;
use Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::all();
        $InactiveClient = Client::onlyTrashed()->get();
        $imagePath = public_path('assets/img/logo.png');
        $base64Logo = 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath));
        $currentUser = \Auth::user()->first_name . ' ' . \Auth::user()->last_name;
        return view("client.index", [
            'clients' => $clients,
            'InactiveClient' => $InactiveClient,
            'base64Logo'=> $base64Logo,
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

        return view("client.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Prevent unauthorized access
        $this->authorize("isHROrAdmin");
    
        // Begin Transaction
        \DB::beginTransaction();
    
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:clients,name',
                'short_name' => 'required|string|max:10',
                'address' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'email' => 'required|email|max:50',
                'contract' => 'required|mimes:pdf|max:10240', // PDF only, max 10MB
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
    
            // Get current user ID
            $user = \Auth::user()->id;
    
            // Handle PDF file
            $originalFile = $request->file('contract');
            $fileName = time() . '_' . $originalFile->getClientOriginalName();
    
            // Save client
            $client = new Client();
            $client->reference_no = $this->generateUniqueCode();
            $client->name = $request->name;
            $client->short_name = $request->short_name;
            $client->address = $request->address;
            $client->contact_number = $request->contact_number;
            $client->email = $request->email;
            $client->contract = $fileName;
            $client->creator_id = $user;
            $client->updater_id = $user;
    
            if ($client->save()) {
                $filePath = public_path('files/' . $client->id . '/');
    
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0777, true);
                }
    
                // Move PDF to folder
                $originalFile->move($filePath, $fileName);
            }
    
            // Save log
            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " created client " . $client->reference_no . " at " . \Carbon\Carbon::now();
            $log->creator_id = $user;
            $log->updater_id = $user;
            $log->save();
    
            // Commit transaction
            \DB::commit();
    
            return redirect()->route('client.create')->with('successMsg', 'Client Save Successful');
        } catch (\Exception $e) {
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

        $client = Client::withTrashed()->findOrFail($id);

        return view('client.show', [
            'client' => $client
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

        $client = Client::withTrashed()->findOrFail($id);


        return view('client.edit', [
            'client' => $client
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
        // Prevent unauthorized access
        $this->authorize("isHROrAdmin");
    
        \DB::beginTransaction();
    
        try {
            // Check if client exists
            $client = Client::withTrashed()->findOrFail($id);
    
            // Validate input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:clients,name,' . $client->id,
                'short_name' => 'required|string|max:10',
                'address' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'email' => 'required|email|max:50',
                'contract' => 'nullable|mimes:pdf|max:10240', // PDF only, max 10MB
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
    
            $user = \Auth::user()->id;
            $newFile = $request->file('contract');
            $currentFile = $client->contract;
            $fileName = $currentFile;
    
            if ($newFile) {
                $fileName = time() . '_' . $newFile->getClientOriginalName();
                $filePath = public_path('files/' . $client->id . '/');
    
                // Create folder if it doesn't exist
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0777, true);
                }
    
                // Delete old file if it exists
                $oldFilePath = $filePath . $currentFile;
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
    
                // Move new file
                $newFile->move($filePath, $fileName);
            }
    
            // Update client info
            $client->name = $request->name;
            $client->short_name = $request->short_name;
            $client->address = $request->address;
            $client->contact_number = $request->contact_number;
            $client->email = $request->email;
            $client->contract = $fileName;
            $client->updater_id = $user;
            $client->update();
    
            // Log the update
            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edited client " . $client->reference_no . " at " . \Carbon\Carbon::now();
            $log->creator_id = $user;
            $log->updater_id = $user;
            $log->save();
    
            \DB::commit();
    
            return back()->with("successMsg", "Client updated successfully");
        } catch (\Exception $e) {
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

        //delete category
        $client = Client::findOrFail($id);
        $client->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete client " . $client->reference_no . " at " . Carbon::now();
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

            $client = Client::onlyTrashed()->findOrFail($id);

            /* Restore client */
            $client->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore client " . $client->reference_no . " at " . Carbon::now();
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
        } while (Client::where("reference_no", "=", $reference_no)->first());

        return $reference_no;
    }
}