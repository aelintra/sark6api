<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::get('agents', 'AgentController@index');
Route::get('agents/{agent}', 'AgentController@show');
Route::post('agents', 'AgentController@save');
Route::put('agents/{agent}', 'AgentController@update');
Route::delete('agents/{agent}', 'AgentController@delete');

Route::get('astamis', 'AstAmiController@index');

Route::get('astamis/ConfbridgeList/{id}', 'AstAmiController@mconfbridgelist');
Route::get('astamis/CoreSettings', 'AstAmiController@coresettings');
Route::get('astamis/CoreStatus', 'AstAmiController@corestatus');

Route::get('astamis/DBget/{id}/{key}', 'AstAmiController@dbget');
Route::get('astamis/ExtensionState/{id}{context?}', 'AstAmiController@extensionstate');
Route::get('astamis/MailboxCount/{id}', 'AstAmiController@mailboxcount');
Route::get('astamis/MailboxStatus/{id}', 'AstAmiController@mailboxstatus');
Route::get('astamis/QueueStatus/{id}', 'AstAmiController@queuestatus');
Route::get('astamis/QueueSummary/{id}', 'AstAmiController@queuesummary');
Route::get('astamis/Reload', 'AstAmiController@reload');
Route::get('astamis/SIPshowpeer/{id}', 'AstAmiController@sipshowpeer');
Route::get('astamis/{action}', 'AstAmiController@getlist');

Route::post('astamis/originate', 'AstAmiController@originate');

Route::put('astamis/DBdel/{id}/{key}', 'AstAmiController@dbdel');
Route::put('astamis/DBput/{id}/{key}/{value}', 'AstAmiController@dbput');

Route::get('backups', 'BackupController@index');
Route::get('backups/new', 'BackupController@new');
Route::get('backups/{backup}', 'BackupController@download');
Route::post('backups', 'BackupController@save');
Route::put('backups/{backup}', 'BackupController@update');
Route::delete('backups/{backup}', 'BackupController@delete');

Route::get('coscloses', 'CosCloseController@index');
Route::get('coscloses/{cosclose}', 'CosCloseController@show');
Route::post('coscloses', 'CosCloseController@save');
Route::put('coscloses/{cosclose}', 'CosCloseController@update');
Route::delete('coscloses/{cosclose}', 'CosCloseController@delete');

Route::get('cosopens', 'CosOpenController@index');
Route::get('cosopens/{cosopen}', 'CosOpenController@show');
Route::post('cosopens', 'CosOpenController@save');
Route::put('cosopens/{cosopen}', 'CosOpenController@update');
Route::delete('cosopens/{cosopen}', 'CosOpenController@delete');

Route::get('cosrules', 'ClassOfServiceController@index');
Route::get('cosrules/{classofservice}', 'ClassOfServiceController@show');
Route::post('cosrules', 'ClassOfServiceController@save');
Route::put('cosrules/{classofservice}', 'ClassOfServiceController@update');
Route::delete('cosrules/{classofservice}', 'ClassOfServiceController@delete');

Route::get('customapps', 'CustomAppController@index');
Route::get('customapps/{customapp}', 'CustomAppController@show');
Route::post('customapps', 'CustomAppController@save');
Route::put('customapps/{customapp}', 'CustomAppController@update');
Route::delete('customapps/{customapp}', 'CustomAppController@delete');

Route::get('daytimers', 'DayTimerController@index');
Route::get('daytimers/{daytimer}', 'DayTimerController@show');
Route::post('daytimers', 'DayTimerController@save');
Route::put('daytimers/{daytimer}', 'DayTimerController@update');
Route::delete('daytimers/{daytimer}', 'DayTimerController@delete');

Route::get('destinations', 'DestinationController@index');

Route::get('extensions', 'ExtensionController@index');
Route::get('extensions/{extension}', 'ExtensionController@show');
Route::get('extensions/{extension}/runtime', 'ExtensionController@showruntime');
Route::post('extensions/mailbox', 'ExtensionController@mailbox');
Route::post('extensions/provisioned', 'ExtensionController@provisioned');
Route::post('extensions/vxt', 'ExtensionController@vxt');
Route::post('extensions/unprovisioned', 'ExtensionController@unprovisioned');
Route::put('extensions/{extension}', 'ExtensionController@update');
Route::put('extensions/{extension}/runtime', 'ExtensionController@updateruntime');
Route::delete('extensions/{extension}', 'ExtensionController@delete');

Route::get('firewalls/ipv4', 'FirewallController@ipv4');
Route::get('firewalls/ipv6', 'FirewallController@ipv6');
Route::post('firewalls/ipv4', 'FirewallController@ipv4save');
Route::post('firewalls/ipv6', 'FirewallController@ipv6save');
Route::put('firewalls/ipv4', 'FirewallController@ipv4restart');
Route::put('firewalls/ipv6', 'FirewallController@ipv6restart');

Route::get('greetings', 'GreetingController@index');
Route::get('greetings/{greeting}', 'GreetingController@download');
Route::post('greetings', 'GreetingController@save');
Route::delete('greetings/{greeting}', 'GreetingController@delete');

Route::get('holidaytimers', 'HolidayTimerController@index');
Route::get('holidaytimers/{holidaytimer}', 'HolidayTimerController@show');
Route::post('holidaytimers', 'HolidayTimerController@save');
Route::put('holidaytimers/{holidaytimer}', 'HolidayTimerController@update');
Route::delete('holidaytimers/{holidaytimer}', 'HolidayTimerController@delete');

Route::get('inboundroutes', 'InboundRouteController@index');
Route::get('inboundroutes/{inboundroute}', 'InboundRouteController@show');
Route::post('inboundroutes', 'InboundRouteController@save');
Route::put('inboundroutes/{inboundroute}', 'InboundRouteController@update');
Route::delete('inboundroutes/{inboundroute}', 'InboundRouteController@delete');

Route::get('ivrs', 'IvrController@index');
Route::get('ivrs/{ivr}', 'IvrController@show');
Route::post('ivrs', 'IvrController@save');
Route::put('ivrs/{ivr}', 'IvrController@update');
Route::delete('ivrs/{ivr}', 'IvrController@delete');

Route::get('logs', 'LogController@index');
Route::get('logs/cdrs/{limit?}', 'LogController@showcdr');

Route::get('queues', 'QueueController@index');
Route::get('queues/{queue}', 'QueueController@show');
Route::post('queues', 'QueueController@save');
Route::put('queues/{queue}', 'QueueController@update');
Route::delete('queues/{queue}', 'QueueController@delete');

Route::get('snapshots', 'SnapShotController@index');
Route::get('snapshots/new', 'SnapShotController@new');
Route::get('snapshots/{snapshot}', 'SnapShotController@download');
Route::post('snapshots', 'SnapShotController@save');
Route::put('snapshots/{snapshot}', 'SnapShotController@update');
Route::delete('snapshots/{snapshot}', 'SnapShotController@delete');

Route::get('ringgroups', 'RingGroupController@index');
Route::get('ringgroups/{ringgroup}', 'RingGroupController@show');
Route::post('ringgroups', 'RingGroupController@save');
Route::put('ringgroups/{ringgroup}', 'RingGroupController@update');
Route::delete('ringgroups/{ringgroup}', 'RingGroupController@delete');

Route::get('routes', 'RouteController@index');
Route::get('routes/{route}', 'RouteController@show');
Route::post('routes', 'RouteController@save');
Route::put('routes/{route}', 'RouteController@update');
Route::delete('routes/{route}', 'RouteController@delete');

Route::get('syscommands', 'SysCommandController@index');
Route::get('syscommands/commit', 'SysCommandController@commit');
Route::get('syscommands/reboot', 'SysCommandController@reboot');
Route::get('syscommands/pbxrunstate', 'SysCommandController@pbxrunstate');
Route::get('syscommands/pbxstart', 'SysCommandController@start');
Route::get('syscommands/pbxstop', 'SysCommandController@stop');

Route::get('sysglobals', 'SysglobalController@index');
Route::put('sysglobals', 'SysglobalController@update');

Route::get('templates', 'TemplateController@index');
Route::get('templates/{template}', 'TemplateController@show');
Route::post('templates', 'TemplateController@save');
Route::put('templates/{template}', 'TemplateController@update');
Route::delete('templates/{template}', 'TemplateController@delete');

Route::get('tenants', 'TenantController@index');
Route::get('tenants/{tenant}', 'TenantController@show');
Route::post('tenants', 'TenantController@save');
Route::put('tenants/{tenant}', 'TenantController@update');
Route::delete('tenants/{tenant}', 'TenantController@delete');

Route::get('trunks', 'TrunkController@index');
Route::get('trunks/{trunk}', 'TrunkController@show');
Route::post('trunks', 'TrunkController@save');
Route::put('trunks/{trunk}', 'TrunkController@update');
Route::delete('trunks/{trunk}', 'TrunkController@delete');

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. Check your URI for correctness'], 404);
});