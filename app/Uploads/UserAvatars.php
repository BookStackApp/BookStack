<?php namespace BookStack\Uploads;

use BookStack\Auth\User;
use BookStack\Exceptions\HttpFetchException;
use Exception;
use Illuminate\Support\Facades\Log;

class UserAvatars
{
    protected $imageService;
    protected $http;

    public function __construct(ImageService $imageService, HttpFetcher $http)
    {
        $this->imageService = $imageService;
        $this->http = $http;
    }

    /**
     * Fetch and assign an avatar image to the given user.
     */
    public function fetchAndAssignToUser(User $user): void
    {
        if (!$this->avatarFetchEnabled()) {
            return;
        }

        try {
            $avatar = $this->saveAvatarImage($user);
            $user->avatar()->associate($avatar);
            $user->save();
        } catch (Exception $e) {
            Log::error('Failed to save user avatar image');
        }
    }

    /**
     * Save an avatar image from an external service.
     * @throws Exception
     */
    protected function saveAvatarImage(User $user, int $size = 500): Image
    {
        $avatarUrl = $this->getAvatarUrl();
        $email = strtolower(trim($user->email));

        $replacements = [
            '${hash}' => md5($email),
            '${size}' => $size,
            '${email}' => urlencode($email),
        ];

        $userAvatarUrl = strtr($avatarUrl, $replacements);
        $imageName = str_replace(' ', '-', $user->id . '-avatar.png');
        $imageData = $this->getAvatarImageData($userAvatarUrl);

        $image = $this->imageService->saveNew($imageName, $imageData, 'user', $user->id);
        $image->created_by = $user->id;
        $image->updated_by = $user->id;
        $image->save();

        return $image;
    }

    /**
     * Gets an image from url and returns it as a string of image data.
     * @throws Exception
     */
    protected function getAvatarImageData(string $url): string
    {
        try {
            $imageData = $this->http->fetch($url);
        } catch (HttpFetchException $exception) {
            throw new Exception(trans('errors.cannot_get_image_from_url', ['url' => $url]));
        }
        return $imageData;
    }

    /**
     * Check if fetching external avatars is enabled.
     */
    protected function avatarFetchEnabled(): bool
    {
        $fetchUrl = $this->getAvatarUrl();
        return is_string($fetchUrl) && strpos($fetchUrl, 'http') === 0;
    }

    /**
     * Get the URL to fetch avatars from.
     */
    protected function getAvatarUrl(): string
    {
        $url = trim(config('services.avatar_url'));

        if (empty($url) && !config('services.disable_services')) {
            $url = 'https://www.gravatar.com/avatar/${hash}?s=${size}&d=identicon';
        }

        return $url;
    }

}