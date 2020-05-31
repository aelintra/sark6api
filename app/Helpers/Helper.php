<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\CustomClasses\Ami;


if (!function_exists('move_request_to_model')) {
    /**
     * Updates a model ready for saving
     *
     * @param obj $request
     * Input
     *
     * @param obj $model
     * Target
     *
     * @param array $updateableColumns
     * Named columns to move 
     *
     * @return NULL
     *
     * */
    function move_request_to_model($request, $model, $updateableColumns) {

        foreach ($request->post() as $key => $value) {
    		if (array_key_exists($key,$updateableColumns)) {
    			$model->$key = trim($value);
    		} 
    	}
		return;

	}

}

if (!function_exists('valid_ip_or_domain')) {
    /**
     * checks host for valid IP or valid domain name
     *
     * @param host reference
     *
     * @return boolean
     *
     * */
    function valid_ip_or_domain($host) {

        if (filter_var($host, FILTER_VALIDATE_IP)) {
        	return true;
        }

        if  (checkdnsrr($host, "A")   ) {

        	return true;
        } 

		return false;

	}

}


if (!function_exists('get_route_class')) {

/**
 *  This little sub returns a "routeclass" for the open/closed/outcome value you input
 *	The routeclass makes life a lot easier for the AGI when it has
 *	to route a call through the open/closed/outcome vectors

 *	0  => value is "None" for an IVR menu selection
 *	1  => value is a dialable internal number (extension or callgroup)
 *	2  => value is an IVR name
 *	3  => value is the default IVR
 *	4  => value is a queue name
 *	5  => value is DISA
 *	6  => value is CALLBACK
 *	7  => Not Used
 *	8  => value is a sibling
 *	9  => value is a trunk name
 *	10 => value is a custom_app name
 *	11 => value is a trunk group
 *	20 => value is Retrieve Voicemail
 *	21 => value is Leave Voicemail
 *	100 => value is Operator
 *	101 => value is Hangup
 * 
 * @param open or closed route value 
 * 
 * @return a route class (integer) or 404 if not found
 */
	function get_route_class($var) {

		if (empty($var)) {
        	return 0;
        }

// Check abstract (non db entry) types 
        if ($var == "None") {
        	return 0;
        }
        if ($var == "Default IVR") {
        	return 3;
        }
        if ($var == "DISA") {
        	return 5;
        }
        if ($var == "CALLBACK") {
        	return 6;
        }
        if ($var == "Retrieve Voicemail") {
        	return 20;
        }
        if ($var == "Leave Voicemail") {
        	return 21;
        }
        if ($var == "Operator") {
        	return 100;
        }
        if ($var == "Hangup") {
        	return 101;
        }

// if it looks like an extension or mailbox, check the extension exists
        if (preg_match('/^\*?(\d{3,6})$/', $var, $matches)) {

        	if ( DB::table('ipphone')->Where ('pkey', '=', $matches[1])->count() ) {
        		return 1;
        	}
        }

// check if it is a ring group
        if ( DB::table('speed')->Where ('pkey', '=', $var)->count() ) { 
			return 1;
		}

// check if it is a queue
		if ( DB::table('queue')->Where ('pkey', '=', $var)->count() ) {
			return 4;
		}

// check if it is an IVR
        if ( DB::table('ivrmenu')->Where ('pkey', '=', $var)->count() ) {
			return 2;
		}

// check if it is a custom app
        if ( DB::table('appl')->Where ('pkey', '=', $var)->count() ) {		
			return 10;
		}	

// check if it is a trunk or group (ISDN)
		$res = DB::table('lineio')
				->join ('carrier', 'lineio.carrier', '=', 'carrier.pkey')
				->select ('lineio.pkey','carrier.carriertype')
				->Where ('lineio.pkey', '=', $var)
				->first();

        if ( isset($res->pkey) ) { 
			if ($res->carriertype == 'group') {
				return 11;
            }
            else {
        		if (preg_match('/~/', $var)) {
               		return 8;
               	}
               	else {
               		return 9;
               	}
            }
        }	

        return 404;
    }

}


if (!function_exists('create_new_backup')) {
    /**
     * checks host for valid IP or valid domain name
     *
     * @param none
     *
     * @return backup filename
     *
     * */
    function create_new_backup() {

        $backupSet = [
            '/opt/sark/db/sark.db',
            '/usr/share/asterisk/sounds',
            '/var/spool/asterisk/voicemail',
            '/etc/asterisk',
            '/etc/shorewall',
            '/tmp/sark.local.ldif'
        ];

        shell_exec('/usr/sbin/slapcat > /tmp/sark.local.ldif');
        $newBackupName = "sarkbak." . time() . ".zip";
       
        foreach($backupSet as $file) { 
            if(file_exists($file)) {
                Log::info("zipping " . $file); 
                shell_exec("/usr/bin/zip -r /tmp/$newBackupName $file");
            } 
            else {
                Log::info($file . " not found");
            }
        } 
        shell_exec("/bin/mv /tmp/$newBackupName /opt/sark/bkup/");
        shell_exec("/bin/chown www-data:www-data /opt/sark/bkup/$newBackupName ");
        shell_exec("/bin/chmod 664 /opt/sark/bkup/$newBackupName ");
        return $newBackupName;  

    }

}

if (!function_exists('create_new_snapshot')) {
    /**
     * checks host for valid IP or valid domain name
     *
     * @param none
     *
     * @return snap file name
     *
     * */
    function create_new_snapshot() {

        $newSnapshotName = "sark.db." . time();
        shell_exec("/bin/cp /opt/sark/db/sark.db /opt/sark/snap/$newSnapshotName");
        shell_exec("/bin/chown www-data:www-data /opt/sark/snap/$newSnapshotName");
        shell_exec("/bin/chmod 664 /opt/sark/snap/$newSnapshotName");
        return $newSnapshotName;  

    }

}


if (!function_exists('restore_from_backup')) {

function restore_from_backup($request) {
    
/* 
 * Unzip the backup file
 */
    if (!file_exists("/opt/sark/bkup/" . $request->backup)) {
        Log::info("Requested restore set not found");
        return 404;
    }

/* 
 * start restore
 */

    $tempDname = "/tmp/bkup" . time();
    shell_exec("/bin/mkdir $tempDname");
    $unzipCmd = "/usr/bin/unzip /opt/sark/bkup/" . $request->backup . " -d $tempDname";
    shell_exec($unzipCmd);
    if (!file_exists($tempDname)) {
        Log::info("Restore unzip did not create a directory!");
        return 500;
    }
    
/*
 * now we can begin the restore
 */     
    if ( $request->restoredb === true) {
        if (file_exists($tempDname . '/opt/sark/db/sark.db')) {
            Log::info("Restoring the Database from $tempDname/opt/sark/db/sark.db");
            shell_exec("/bin/cp -f $tempDname/opt/sark/db/sark.db  /opt/sark/db/sark.db");
            Log::info("Setting DB ownership");
            shell_exec("/bin/chown www-data:www-data  /opt/sark/db/sark.db");
            Log::info("Running the reloader to sync versions");
            shell_exec("/bin/sh /opt/sark/scripts/srkV4reloader.sh");      
            Log::info("Database restore complete");
            Log::info("Database RESTORED");
        }
        else {
            Log::info("No Database in backup set - request ignored");
            Log::info("Database PRESERVED");
        }           
    }
    else {
        Log::info("Database PRESERVED");  
    }

    if ( $request->restoreasterisk === true ) {
        if (file_exists($tempDname . '/etc/asterisk')) {
            shell_exec("sudo /bin/rm -rf /etc/asterisk/*");
            shell_exec("/bin/cp -a  $tempDname/etc/asterisk/* /etc/asterisk");
            shell_exec("/bin/chown asterisk:asterisk /etc/asterisk/*");
            shell_exec("/bin/chmod 664 /etc/asterisk/*");
            Log::info("Asterisk files RESTORED");
        }
        else {
            Log::info("No Asterisk files in backup set; request ignored");
            Log::info("<p>Asterisk Files PRESERVED");
        }       
    }
    else {
        Log::info("Asterisk Files PRESERVED");    
    }   
                        
    if ( $request->restoreusergreets  === true) {
        if (glob($tempDname . '/usr/share/asterisk/sounds/usergreeting*')) {
            shell_exec("/bin/rm -rf /usr/share/asterisk/sounds/usergreeting*");
            shell_exec("/bin/cp -a  $tempDname/usr/share/asterisk/sounds/usergreeting* /usr/share/asterisk/sounds");
            shell_exec("/bin/chown asterisk:asterisk /usr/share/asterisk/sounds/usergreeting*");
            shell_exec("/bin/chmod 664 /usr/share/asterisk/sounds/usergreeting*");

            Log::info("Greeting files RESTORED");
        }
        else {
            Log::info("No greeting files in backup set; request ignored");
            Log::info("Greeting files PRESERVED");
        }
    }
    else {
        Log::info("Greeting files PRESERVED");    
    }
        
    if ( $request->restorevmail === true) {
        if (file_exists($tempDname . '/var/spool/asterisk/voicemail/default')) {
            shell_exec("/bin/rm -rf /var/spool/asterisk/voicemail/default");
            shell_exec("/bin/cp -a $tempDname/var/spool/asterisk/voicemail/default /var/spool/asterisk/voicemail");
            shell_exec("/bin/chown -R asterisk:asterisk /var/spool/asterisk/voicemail/default");
            shell_exec("/bin/chmod 664 /var/spool/asterisk/voicemail/default");
            Log::info("Voicemail files RESTORED");
        }
        else {
            Log::info("No voicemail files in backup set; request ignored");
            Log::info("Voicemail files PRESERVED");
        }
    }
    else {
        Log::info("Voicemail files PRESERVED");   
    }
    
    if ( $request->restoreldap === true) {
        if (file_exists($tempDname . '/tmp/sark.local.ldif')) {
            shell_exec("sudo /etc/init.d/slapd stop");
            shell_exec("sudo /bin/rm -rf /var/lib/ldap/*");
            shell_exec("sudo /usr/sbin/slapadd -l " . $tempDname . "/tmp/sark.local.ldif");
            shell_exec("sudo /bin/chown openldap:openldap /var/lib/ldap/*");
            shell_exec("sudo /etc/init.d/slapd start");  
            Log::info("LDAP Directory RESTORED");
        }
        else {
            Log::info("No LDAP Directory in backup set; request ignored");
            Log::info("LDAP Directory PRESERVED");
        }
    }
    else {
        Log::info("LDAP Directory PRESERVED");    
    }   
    
    shell_exec("/bin/rm -rf $tempDname");
    Log::info("Temporary work files deleted");
    Log::info("Requesting Asterisk reload");
    shell_exec("/bin/sh /opt/sark/scripts/srkreload");
    Log::info("System Regen complete");

    return 200; 
    } 
}

if (!function_exists('get_ami_handle')) {

/**
 * get_ami_handle get a handle
 * @return object ref AMI
 */
    function get_ami_handle() {

        if  (!`/bin/ps -e | /bin/grep asterisk | /bin/grep -v grep`) {
            Response::make(['message' => 'PBX not running'],503)->send();
        }

        $params = array('server' => '127.0.0.1', 'port' => '5038');
        $amiHandle = new Ami($params);
        $amiconrets = $amiHandle->connect();
        if ( !$amiconrets ) {            
            Response::make(['message' => 'Service Unavailable - Could not connect to the PBX'],503)->send();
        }
        else {
            $amiHandle->login('sark','mysark');
        } 
        return $amiHandle;  
    } 
} 

if (!function_exists('pbx_is_running')) {
    function pbx_is_running() {

        if  (`/bin/ps -e | /bin/grep asterisk | /bin/grep -v grep`) {
            return true;
        }

        return false; 
    }
}


