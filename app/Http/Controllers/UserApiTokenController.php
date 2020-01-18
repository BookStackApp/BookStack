<?php namespace BookStack\Http\Controllers;

use BookStack\Api\ApiToken;
use BookStack\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserApiTokenController extends Controller
{

    /**
     * Show the form to create a new API token.
     */
    public function create(int $userId)
    {
        // Ensure user is has access-api permission and is the current user or has permission to manage the current user.
        $this->checkPermission('access-api');
        $this->checkPermissionOrCurrentUser('users-manage', $userId);

        $user = User::query()->findOrFail($userId);
        return view('users.api-tokens.create', [
            'user' => $user,
        ]);
    }

    /**
     * Store a new API token in the system.
     */
    public function store(Request $request, int $userId)
    {
        $this->checkPermission('access-api');
        $this->checkPermissionOrCurrentUser('users-manage', $userId);

        $this->validate($request, [
            'name' => 'required|max:250',
            'expires_at' => 'date_format:Y-m-d',
        ]);

        $user = User::query()->findOrFail($userId);
        $secret = Str::random(32);

        $token = (new ApiToken())->forceFill([
            'name' => $request->get('name'),
            'token_id' => Str::random(32),
            'secret' => Hash::make($secret),
            'user_id' => $user->id,
            'expires_at' => $request->get('expires_at') ?: ApiToken::defaultExpiry(),
        ]);

        while (ApiToken::query()->where('token_id', '=', $token->token_id)->exists()) {
            $token->token_id = Str::random(32);
        }

        $token->save();

        session()->flash('api-token-secret:' . $token->id, $secret);
        $this->showSuccessNotification(trans('settings.user_api_token_create_success'));
        return redirect($user->getEditUrl('/api-tokens/' . $token->id));
    }

    /**
     * Show the details for a user API token, with access to edit.
     */
    public function edit(int $userId, int $tokenId)
    {
        [$user, $token] = $this->checkPermissionAndFetchUserToken($userId, $tokenId);
        $secret = session()->pull('api-token-secret:' . $token->id, null);

        return view('users.api-tokens.edit', [
            'user' => $user,
            'token' => $token,
            'model' => $token,
            'secret' => $secret,
        ]);
    }

    /**
     * Update the API token.
     */
    public function update(Request $request, int $userId, int $tokenId)
    {
        $this->validate($request, [
            'name' => 'required|max:250',
            'expires_at' => 'date_format:Y-m-d',
        ]);

        [$user, $token] = $this->checkPermissionAndFetchUserToken($userId, $tokenId);
        $token->fill([
            'name' => $request->get('name'),
            'expires_at' => $request->get('expires_at') ?: ApiToken::defaultExpiry(),
        ])->save();

        $this->showSuccessNotification(trans('settings.user_api_token_update_success'));
        return redirect($user->getEditUrl('/api-tokens/' . $token->id));
    }

    /**
     * Show the delete view for this token.
     */
    public function delete(int $userId, int $tokenId)
    {
        [$user, $token] = $this->checkPermissionAndFetchUserToken($userId, $tokenId);
        return view('users.api-tokens.delete', [
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Destroy a token from the system.
     */
    public function destroy(int $userId, int $tokenId)
    {
        [$user, $token] = $this->checkPermissionAndFetchUserToken($userId, $tokenId);
        $token->delete();

        $this->showSuccessNotification(trans('settings.user_api_token_delete_success'));
        return redirect($user->getEditUrl('#api_tokens'));
    }

    /**
     * Check the permission for the current user and return an array
     * where the first item is the user in context and the second item is their
     * API token in context.
     */
    protected function checkPermissionAndFetchUserToken(int $userId, int $tokenId): array
    {
        $this->checkPermissionOr('users-manage', function () use ($userId) {
            return $userId === user()->id && userCan('access-api');
        });

        $user = User::query()->findOrFail($userId);
        $token = ApiToken::query()->where('user_id', '=', $user->id)->where('id', '=', $tokenId)->firstOrFail();
        return [$user, $token];
    }

}
