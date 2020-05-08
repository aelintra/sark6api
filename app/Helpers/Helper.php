<?php

use Illuminate\Support\Facades\DB;

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








