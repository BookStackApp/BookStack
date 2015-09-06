<?php

namespace Oxbow\Http\Controllers;

use Illuminate\Http\Request;

use Oxbow\Http\Requests;
use Oxbow\Http\Controllers\Controller;
use Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the settings.
     *
     * @return Response
     */
    public function index()
    {
        $this->checkPermission('settings-update');
        return view('settings/index');
    }


    /**
     * Update the specified settings in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
        $this->checkPermission('settings-update');
        // Cycles through posted settings and update them
        foreach($request->all() as $name => $value) {
            if(strpos($name, 'setting-') !== 0) continue;
            $key = str_replace('setting-', '', trim($name));
            Setting::put($key, $value);
        }
        session()->flash('success', 'Settings Saved');
        return redirect('/settings');
    }

}
