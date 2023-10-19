<?php

namespace BookStack\Api;

use BookStack\Activity\ActivityType;
use BookStack\Http\Controller;
use BookStack\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserApiTokenController extends Controller
{
    /**
     * Show the form to create a new API token.
     */
    public function create(Request $request, int $userId)
    {
        $this->checkPermission('access-api');
        $this->checkPermissionOrCurrentUser('users-manage', $userId);
        $this->updateContext($request);

        $user = User::query()->findOrFail($userId);

        $this->setPageTitle(trans('settings.user_api_token_create'));

        return view('users.api-tokens.create', [
            'user' => $user,
            'back' => $this->getRedirectPath($user),
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
            'name'       => ['required', 'max:250'],
            'expires_at' => ['date_format:Y-m-d'],
        ]);

        $user = User::query()->findOrFail($userId);
        $secret = Str::random(32);

        $token = (new ApiToken())->forceFill([
            'name'       => $request->get('name'),
            'token_id'   => Str::random(32),
            'secret'     => Hash::make($secret),
            'user_id'    => $user->id,
            'expires_at' => $request->get('expires_at') ?: ApiToken::defaultExpiry(),
        ]);

        while (ApiToken::query()->where('token_id', '=', $token->token_id)->exists()) {
            $token->token_id = Str::random(32);
        }

        $token->save();

        session()->flash('api-token-secret:' . $token->id, $secret);
        $this->logActivity(ActivityType::API_TOKEN_CREATE, $token);

        return redirect($token->getUrl());
    }

    /**
     * Show the details for a user API token, with access to edit.
     */
    public function edit(Request $request, int $userId, int $tokenId)
    {
        $this->updateContext($request);

        [$user, $token] = $this->checkPermissionAndFetchUserToken($userId, $tokenId);
        $secret = session()->pull('api-token-secret:' . $token->id, null);

        $this->setPageTitle(trans('settings.user_api_token'));

        return view('users.api-tokens.edit', [
            'user'   => $user,
            'token'  => $token,
            'model'  => $token,
            'secret' => $secret,
            'back' => $this->getRedirectPath($user),
        ]);
    }

    /**
     * Update the API token.
     */
    public function update(Request $request, int $userId, int $tokenId)
    {
        $this->validate($request, [
            'name'       => ['required', 'max:250'],
            'expires_at' => ['date_format:Y-m-d'],
        ]);

        [$user, $token] = $this->checkPermissionAndFetchUserToken($userId, $tokenId);
        $token->fill([
            'name'       => $request->get('name'),
            'expires_at' => $request->get('expires_at') ?: ApiToken::defaultExpiry(),
        ])->save();

        $this->logActivity(ActivityType::API_TOKEN_UPDATE, $token);

        return redirect($token->getUrl());
    }

    /**
     * Show the delete view for this token.
     */
    public function delete(int $userId, int $tokenId)
    {
        [$user, $token] = $this->checkPermissionAndFetchUserToken($userId, $tokenId);

        $this->setPageTitle(trans('settings.user_api_token_delete'));

        return view('users.api-tokens.delete', [
            'user'  => $user,
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

        $this->logActivity(ActivityType::API_TOKEN_DELETE, $token);

        return redirect($this->getRedirectPath($user));
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

    /**
     * Update the context for where the user is coming from to manage API tokens.
     * (Track of location for correct return redirects)
     */
    protected function updateContext(Request $request): void
    {
        $context = $request->query('context');
        if ($context) {
            session()->put('api-token-context', $context);
        }
    }

    /**
     * Get the redirect path for the current api token editing session.
     * Attempts to recall the context of where the user is editing from.
     */
    protected function getRedirectPath(User $relatedUser): string
    {
        $context = session()->get('api-token-context');
        if ($context === 'settings' || user()->id !== $relatedUser->id) {
            return $relatedUser->getEditUrl('#api_tokens');
        }

        return url('/my-account/auth#api_tokens');
    }
}
