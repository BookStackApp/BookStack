<?php namespace BookStack\Http\Controllers;

use Illuminate\Http\Request;

use BookStack\Http\Requests;
use Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the settings.
     * @return Response
     */
    public function index()
    {
        $this->checkPermission('settings-manage');
        $this->setPageTitle('Settings');

        // Get application version
        $version = trim(file_get_contents(base_path('version')));

        return view('settings/index', ['version' => $version]);
    }

    /**
     * Update the specified settings in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $this->preventAccessForDemoUsers();
        $this->checkPermission('settings-manage');

        // Cycles through posted settings and update them
        foreach ($request->all() as $name => $value) {
            if (strpos($name, 'setting-') !== 0) continue;
            $key = str_replace('setting-', '', trim($name));
            Setting::put($key, $value);
        }

        session()->flash('success', 'Settings Saved');
        return redirect('/settings');
    }

}
