<?php

namespace App\Http\Controllers;

use App\RingGroup;
use Illuminate\Http\Request;
use Response;
use Validator;
use DB;

class RingGroupController extends Controller
{
    //
        //
    	private $updateableColumns = [

			'cluster' => 'exists:cluster,pkey', 
			'devicerec' => 'in:default,None,OTR,OTRR,Inbound.Outbound,Both',
			'dialparamshunt' => 'alpha|nullable',
			'dialparamsring' => 'alpha|nullable',
			'divert' => 'integer|nullable',
			'greeting' => [
				'regex:/^usergreeting\d{4}$/',
				'nullable'
			],
			'grouptype' => 'in:Ring,Hunt,Alias,Page',
			'longdesc' => 'string|nullable',
			'obeydnd' => 'in:YES,NO|nullable',
			'out' => [
					'regex:/^[@A-Za-z0-9-_\/\s]{2,1024}$/',
					'nullable'
			],
			'outcome' => 'string',
			'pagegroup' => 'integer',
			'ringdelay' => 'integer',
			'speedalert' => 'string'
    	];

/**
 *
 * @return Ring Groups
 */
    public function index (RingGroup $ringgroup) {

    	return RingGroup::orderBy('pkey','asc')->get();
    }

/**
 * Return named ring group model instance
 * 
 * @param  RingGroup
 * @return ringgroup object
 */
    public function show (RingGroup $ringgroup) {

    	return response()->json($ringgroup, 200);
    }

/**
 * Create a new ring group instance
 * 
 * @param  Request
 * @return New Did
 */
    public function save(Request $request) {

// validate 
        $this->updateableColumns['pkey'] = 'required';
        $this->updateableColumns['cluster'] = 'required|exists:cluster,' . $request->cluster;
        $this->updateableColumns['outcome'] = 'required';


        $ringgroup = new RingGroup;
        $ringgroup->outcome = 'None';

        $validator = Validator::make($request->all(),$this->updateableColumns);

        $validator->after(function ($validator) use ($request,$ringgroup) {

//Check if key exists
            if ($ringgroup->where('pkey','=',$request->pkey)->count()) {
                    $validator->errors()->add('save', "Duplicate Key - " . $request->pkey);
                    return;
            } 
// check outcome and get outcomerouteclass
            $this->check_outcome($request, $ringgroup, $validator);                      
        });

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }
    
// Move post variables to the model 
        move_request_to_model($request,$ringgroup,$this->updateableColumns); 


// create the model         
        try {
            $ringgroup->save();
        } catch (\Exception $e) {
            return Response::json(['Error' => $e->getMessage()],409);
        }

        return $ringgroup;
    }



/**
 * @param  Request
 * @param  RingGroup
 * @return json response
 */
    public function update(Request $request, RingGroup $ringgroup) {

// Validate   
        $validator = Validator::make($request->all(),$this->updateableColumns);

// Check if outcome target has changed and set the routeclass if it has
        $validator->after(function ($validator) use ($request,$ringgroup) {

            $this->check_outcome($request, $ringgroup, $validator);

        });

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

// Move post variables to the model   
        move_request_to_model($request,$ringgroup,$this->updateableColumns);


// store the model if it has changed
        try {
            if ($ringgroup->isDirty()) {
                $ringgroup->update();
            }

        } catch (\Exception $e) {
            return Response::json(['Error' => $e->getMessage()],409);
        }

        return response()->json($ringgroup, 200);
        
    } 


/**
 * Delete  RingGroup instance
 * @param  RingGroup
 * @return 204
 */
    public function delete(RingGroup $ringgroup) {
        $ringgroup->delete();

        return response()->json(null, 204);
    }


/**
 * @param  $request
 * @param  $ringgroup
 * @return NULL
 */
    private function check_outcome($request, $ringgroup, $validator) {

            if (isset($request->outcome)) {
                $ringgroup->outcomerouteclass = get_route_class($request->outcome);
            }

            if ($ringgroup->outcomerouteclass == 404) {
                $validator->errors()->add('outcome', "The target could not be resolved " . $request->outcome);               
            }                        

    }
}
