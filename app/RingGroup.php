<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RingGroup extends Model
{
    //
    protected $table = 'speed';
    protected $primaryKey = 'pkey';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $attributes = [

	'cluster' => 'default', 
	'devicerec' => 'default',
	'dialparamshunt' => 'cIkt',
	'dialparamsring' => 'ciIkt',
	'divert' => null,
	'greeting' => null,
	'grouptype' => 'Ring',
	'longdesc' => null,
	'obeydnd' => 'NO',
	'out' => null,
	'outcome' => null,
	'outcomerouteclass' => 0,
	'pagegroup' => null,
	'ringdelay' => 20,
	'speedalert' => null
    ];

    // none user updateable columns
    protected $guarded = [
    'callerid' => null,
    'calleridname' => null,
    'greeting',
    'outcomerouteclass',
    'trunk',
	'z_created',
	'z_updated',
	'z_updater'
    ];

    // hidden columns (mostly no longer used)
    protected $hidden = [
    'callerid',
    'calleridname',        
    'greeting',	
    'outcomerouteclass',
    'trunk'

    ];
}
