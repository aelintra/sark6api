<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Validator;
use Storage;

class BackupController extends Controller
{

	private $updateableColumns = [];

    //
/**
 * Return Backup Index in pkey order asc
 * 
 * @return Backups
 */
    public function index () {

        $bkup = array();
    	if ($handle = opendir('/opt/sark/bkup')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..') {
                    if (preg_match (' /^sarkbak\.\d+\.zip$/ ', $entry)) {
                        array_push($bkup, $entry);
                    }
                }
            }
            closedir($handle);
            rsort($bkup);
        }
        else {
            return Response::json(['Error' => 'Could not open bkup directory '],509);
        }

        $backups = array ();
        foreach ($bkup as $file ) {
            preg_match( '/\.(\d+).zip$/',$file,$matches);       
            $rdate = date('D d M H:i:s Y', $matches[1]);
            $fsize = filesize("/opt/sark/bkup/".$file);
            $backups[$file]["filesize"] = $fsize;
            $backups[$file]["date"] = $rdate;                
        }

        return response()->json($backups,200);
    }

/**
 * Return named Backup instance
 * 
 * @param  Backup
 * @return Backup zip file
 */
    public function show ($backup) {

        return Storage::disk('backups')->download($backup);

    }

 /**
 * Save new Backup instance
 * 
 * @param  Backup
 */
    public function store (Request $request) {

//        $path = $request->file('backups')->storeAs(
//            'backups', $request()->id
//        );

        $path = Storage::putFileAs(
                'backups', $request->disk('backups'), $request->id
        );
    }

 /**
 * update tenant instance
 * 
 * @param  Backup
 * @return tenant object
 */
    public function update(Request $request, $backup) {


// Validate         
    	$validator = Validator::make($request->all(),$this->updateableColumns);

    	if ($validator->fails()) {
    		return response()->json($validator->errors(),422);
    	}		

// Move post variables to the model  

		move_request_to_model($request,$backup,$this->updateableColumns);  	

// store the model if it has changed
    	try {
    		if ($backup->isDirty()) {
    			$backup->save();
    		}

        } catch (\Exception $e) {
    		return Response::json(['Error' => $e->getMessage()],409);
    	}

		return response()->json($backup, 200);
    }   

/**
 * Delete tenant instance
 * @param  Backup
 * @return [type]
 */
    public function delete($backup) {

// Don't allow deletion of default tenant

        if ($backup->pkey == 'default') {
           return Response::json(['Error - Cannot delete default tenant!'],409); 
        }

        $backup->delete();

        return response()->json(null, 204);
    }
    //
}
