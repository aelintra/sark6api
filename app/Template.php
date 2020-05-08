<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    //
    protected $table = 'device';
    protected $primaryKey = 'pkey';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $attributes = [
    	'owner' => 'cust'

    ];

    // none user updateable columns
    protected $guarded = [
    	'blfkeys',
    	'device',
    	'fkeys',
    	'imageurl',
    	'legacy',
    	'noproxy',
    	'pkeys',
    	'sipiaxfriend',
    	'tftpname',
    	'zapdevfixed',
    	'z_created',
    	'z_updated'   	
    ];

    // hidden columns (mostly no longer used)
    protected $hidden = [
    	'blfkeys',
    	'device',
    	'fkeys',
    	'imageurl',
    	'legacy',
    	'noproxy',
    	'pkeys',
    	'sipiaxfriend',
    	'tftpname',
    	'zapdevfixed'
    ];
}
