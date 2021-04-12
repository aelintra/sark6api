<?php

namespace App\Http\Controllers;

use App\Tenant;
use Illuminate\Http\Request;
use Response;
use Validator;

class TenantController extends Controller
{

	private $updateableColumns = [
    		'abstimeout' => 'integer',
    		'clusterclid' => 'integer|nullable',
    		'callgroup' => 'integer|nullable',
    		'chanmax' => 'integer',
			'description' => 'string',
			'include' => 'string|nullable',
			'localarea' => 'numeric|nullable',
			'localdplan' => [
					'regex:/^_X+$/',
					'nullable'
			],
			'masteroclo' => 'in:AUTO,CLOSED',
			'operator' => 'integer',
			'pickupgroup' => 'integer|nullable'
    	];

    //
/**
 * Return Tenant Index in pkey order asc
 * 
 * @return Tenants
 */
    public function index () {

    	return Tenant::orderBy('pkey','asc')->get();
    }

/**
 * Return named Tenant instance
 * 
 * @param  Tenant
 * @return Tenant object
 */
    public function show (Tenant $tenant) {
    	return $tenant;
    }

 /**
 * Save new tenant instance
 * 
 * @param  Tenant
 */
    public function save (Request $request) {

    	$this->updateableColumns['pkey'] = 'required';
    	$this->updateableColumns['description'] = 'string|required';

    	$validator = Validator::make($request->all(),$this->updateableColumns);

    	if ($validator->fails()) {
    		return response()->json($validator->errors(),422);
    	}

        if (Tenant::where('pkey','=',$request->pkey)->count()) {
           return Response::json(['Error' => 'Key already exists'],409); 
        }

    	$tenant = new Tenant;
// Move post variables to the model 

    	move_request_to_model($request,$tenant,$this->updateableColumns); 

// store the model if it has changed
    	try {

    		$tenant->save();

        } catch (\Exception $e) {
    		return Response::json(['Error' => $e->getMessage()],409);
    	}

    	return $tenant;
    }

 /**
 * update tenant instance
 * 
 * @param  Tenant
 * @return tenant object
 */
    public function update(Request $request, Tenant $tenant) {


// Validate         
    	$validator = Validator::make($request->all(),$this->updateableColumns);

    	if ($validator->fails()) {
    		return response()->json($validator->errors(),422);
    	}		

// Move post variables to the model  

		move_request_to_model($request,$tenant,$this->updateableColumns);  	

// store the model if it has changed
    	try {
    		if ($tenant->isDirty()) {
    			$tenant->save();
    		}

        } catch (\Exception $e) {
    		return Response::json(['Error' => $e->getMessage()],409);
    	}

		return response()->json($tenant, 200);
    }   

/**
 * Delete tenant instance
 * @param  Tenant
 * @return [type]
 */
    public function delete(Tenant $tenant) {

// Don't allow deletion of default tenant

        if ($tenant->pkey == 'default') {
           return Response::json(['Error - Cannot delete default tenant!'],409); 
        }

        $tenant->delete();

        return response()->json(null, 204);
    }
    //
}
