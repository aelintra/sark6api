<?php

namespace App\Http\Controllers;

use App\Template;
use Illuminate\Http\Request;
use Response;
use Validator;

class TemplateController extends Controller
{
    //

 	protected $updateableColumns = [
    		'blfkeyname' => 'exists:device,pkey|nullable',
    		'desc' => 'string',
    		'provision' => 'string|nullable',
    		'technology' => 'in:SIP,Descriptor,BLF Template',
    ];

/**
 * Return Template Index in pkey order asc
 * 
 * @return Templates
 */
    public function index () {

    	return Template::whereNull('legacy')->orderBy('pkey','asc')->get();
    }

/**
 * Return named template instance
 * 
 * @param  Template
 * @return template object
 */
    public function show (Template $template) {
    	return $template;
    }

 /**
 * Save new template instance
 * 
 * @param  Template
 * @return template object
 */
    public function save (Request $request) {

    	$this->updateableColumns['pkey'] = 'required';
    	$this->updateableColumns['technology'] = 'required|in:SIP,Descriptor,BLF Template';

    	$validator = Validator::make($request->all(),$this->updateableColumns);

    	$validator->after(function ($validator) use ($request) {
			// only allow blfkeyname if template type is SIP

			if ( $request->post('blfkeyname') != NULL && $request->post('technology') != 'SIP')  {
				$validator->errors()->add('blfkeyname', "blfkeyname only allowed for template type of SIP! " );
			}

			// Only allow referenced blfkeyname to have type 'BLF Template'

			if ( Template::select('technolgy')->where('pkey', $request->post('blfkeyname')) != 'BLF Template' ) {
				$validator->errors()->add('blfkeyname', "blfkeyname reference must be of type 'BLF Template'!" );				
			}

		});

    	if ($validator->fails()) {
    		return response()->json($validator->errors(),422);
    	}

    	$template = new Template;

// Move post variables to the model 
    	move_request_to_model($request,$template,$this->updateableColumns); 

// store the model if it has changed
    	try {

    		$template->save();

        } catch (\Exception $e) {
    		return Response::json(['Error' => $e->getMessage()],409);
    	}

    	return $template;
    }


 /**
 * update template instance
 * 
 * @param  Template
 * @return template object
 */
    public function update(Request $request, Template $template) {




// Validate         
    	$validator = Validator::make($request->all(),$this->updateableColumns);

    	$validator->after(function ($validator) use ($request, $template) {
			// only allow blfkeyname if template type is SIP

			if ( $request->post('blfkeyname') != NULL && $template->technology != 'SIP')  {
				$validator->errors()->add('blfkeyname', "blfkeyname only allowed in template type of SIP! " );
			}

			// Only allow referenced blfkeyname to have type 'BLF Template'
			// fetch the row('technology') the blfkeyname points to
			$candidateTechnology = $template->where('pkey', $request->post('blfkeyname'))->pluck('technology')->first();
			// check it
			if ( $candidateTechnology != 'BLF Template' ) {
				$validator->errors()->add('blfkeyname', "blfkeyname reference must be of type 'BLF Template'!" );				
			}

		});

    	if ($validator->fails()) {
    		return response()->json($validator->errors(),422);
    	}		

// Move post variables to the model    	
    	move_request_to_model($request,$template,$this->updateableColumns); 

// store the model if it has changed
    	try {
    		if ($template->isDirty()) {
    			$template->save();
    		}

        } catch (\Exception $e) {
    		return Response::json(['Error' => $e->getMessage()],409);
    	}

		return response()->json($template, 200);

    }   

/**
 * Delete template instance
 * @param  Template
 * @return [type]
 */
    public function delete(Template $template) {

        $template->delete();

        return response()->json(null, 204);
    }


}
