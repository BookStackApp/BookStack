<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Access\Mfa\MfaValue;
use BookStack\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MfaController extends Controller
{
    use HandlesPartialLogins;

    /**
     * Show the view to setup MFA for the current user.
     */
    public function setup()
    {
        $userMethods = $this->currentOrLastAttemptedUser()
            ->mfaValues()
            ->get(['id', 'method'])
            ->groupBy('method');

        return view('mfa.setup', [
            'userMethods' => $userMethods,
        ]);
    }

    /**
     * Remove an MFA method for the current user.
     *
     * @throws \Exception
     */
    public function remove(string $method)
    {
        if (in_array($method, MfaValue::allMethods())) {
            $value = user()->mfaValues()->where('method', '=', $method)->first();
            if ($value) {
                $value->delete();
                $this->logActivity(ActivityType::MFA_REMOVE_METHOD, $method);
            }
        }

        return redirect('/mfa/setup');
    }

    /**
     * Show the page to start an MFA verification.
     */
    public function verify(Request $request)
    {
        $desiredMethod = $request->get('method');
        $userMethods = $this->currentOrLastAttemptedUser()
            ->mfaValues()
            ->get(['id', 'method'])
            ->groupBy('method');

        // Basic search for the default option for a user.
        // (Prioritises totp over backup codes)
        $method = $userMethods->has($desiredMethod) ? $desiredMethod : $userMethods->keys()->sort()->reverse()->first();
        $otherMethods = $userMethods->keys()->filter(function ($userMethod) use ($method) {
            return $method !== $userMethod;
        })->all();

        return view('mfa.verify', [
            'userMethods'  => $userMethods,
            'method'       => $method,
            'otherMethods' => $otherMethods,
        ]);
    }
}
