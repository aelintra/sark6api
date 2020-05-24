<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Validator;
use Storage;

class FirewallController extends Controller
{

	private $updateableColumns = [];

    //
/**
 * Return Backup Index in pkey order asc
 * 
 * @return Backups
 */
    public function ipv4 () {

        $file = file("/etc/shorewall/sark_rules",FILE_IGNORE_NEW_LINES) or die("Could not read file $pkey !");

        $i = 1;
        $p_array =array();

        foreach ($file as $line){
            $p_array["rule" . $i] = $line;
            $i++;
        } 
        return response()->json($p_array,200);
    }


 /**
 * instantiate elements of a backup instance
 *
 * The backup contains the entire PBX data.  Choose the restore
 * you want by adding post entries 
 * 
 * POST values are boolean.  They can be true, false, 1, 0, "1", or "0".
 *
 *  resetdb=>true - restore the pbx db
 *  resetasterisk=>true - restore the asterisk files. N.B. be careful with this
 *  resetusergreets=>true - restore usergreetings
 *  resetvmail->true - restore voicemail
 *  resetldap->true - restore ldap contacts database 
 *  
 * 
 * @param  Backup name
 * 
 * @return 200
 */
    public function update(Request $request, $backup) {


// Validate         
    	$validator = Validator::make($request->all(),[         
            'restoredb' => 'boolean',
            'restoreasterisk' => 'boolean',
            'restoreusergreeting' => 'boolean',
            'restorevmail' => 'boolean',
            'restoreldap' => 'boolean'
        ]);

    	if ($validator->fails()) {
    		return response()->json($validator->errors(),422);
    	}		

		if (!file_exists("/opt/sark/bkup/$backup")) {
            return Response::json(['Error' => "backup file not found"],404);
        }   

        $rets = (restore_from_backup($request));

        if ($rets != 200) {
            return Response::json(['Error' => "$backup has errors see logs for details"],$rets); 
        }

		return response()->json(['restored' => $backup], 200);
    }   

/**
 * Delete tenant instance
 * @param  Backup
 * @return [type]
 */
    public function delete($backup) {

// Don't allow deletion of default tenant

        if (!file_exists("/opt/sark/bkup/$backup")) {
           return Response::json(['Error' => "$backup not found in backup set"],404); 
        }

        shell_exec("/bin/rm -r /opt/sark/bkup/$backup");

        return response()->json(null, 204);
    }
    //
}
