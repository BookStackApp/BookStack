<?php

namespace BookStack\Settings;

use BookStack\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class StatusController extends Controller
{
    /**
     * Show the system status as a simple json page.
     */
    public function show()
    {
        $statuses = [
            'database' => $this->trueWithoutError(function () {
                return DB::table('migrations')->count() > 0;
            }),
            'cache' => $this->trueWithoutError(function () {
                $rand = Str::random();
                Cache::add('status_test', $rand);

                return Cache::pull('status_test') === $rand;
            }),
            'session' => $this->trueWithoutError(function () {
                $rand = Str::random();
                Session::put('status_test', $rand);

                return Session::get('status_test') === $rand;
            }),
        ];

        $hasError = in_array(false, $statuses);

        return response()->json($statuses, $hasError ? 500 : 200);
    }

    /**
     * Check the callable passed returns true and does not throw an exception.
     */
    protected function trueWithoutError(callable $test): bool
    {
        try {
            return $test() === true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
