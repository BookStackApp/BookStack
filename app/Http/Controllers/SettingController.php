<?php

namespace BookStack\Http\Controllers;

use Illuminate\Http\Request;

use BookStack\Http\Requests;
use BookStack\Http\Controllers\Controller;
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
        $this->setPageTitle('Settings');
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
        $this->preventAccessForDemoUsers();
        $this->checkPermission('settings-update');

        // Cycles through posted settings and update them
        foreach($request->all() as $name => $value) {
            if(strpos($name, 'setting-') !== 0) continue;
            $key = str_replace('setting-', '', trim($name));
            if($key == 'app-color') {
                Setting::put('app-color-rgba', $this->hex2rgba($value, 0.15));
            }
            Setting::put($key, $value);
        }

        session()->flash('success', 'Settings Saved');
        return redirect('/settings');
    }

    /**
     * Adapted from http://mekshq.com/how-to-convert-hexadecimal-color-code-to-rgb-or-rgba-using-php/
     * Converts a hex color code in to an RGBA string.
     *
     * @param string $color
     * @param float|boolean $opacity
     * @return boolean|string
     */
    protected function hex2rgba($color, $opacity = false)
    {
        // Return false if no color provided
        if(empty($color)) {
            return false;
        }
        // Trim any whitespace
        $color = trim($color);

        // Sanitize $color if "#" is provided
        if($color[0] == '#' ) {
            $color = substr($color, 1);
        }

        // Check if color has 6 or 3 characters and get values
        if(strlen($color) == 6) {
            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif( strlen( $color ) == 3 ) {
            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
            return false;
        }

        // Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        // Check if opacity is set(rgba or rgb)
        if($opacity) {
            if(abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
            $output = 'rgb('.implode(",",$rgb).')';
        }

        // Return rgb(a) color string
        return $output;
    }

}
