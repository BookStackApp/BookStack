<?php

namespace BookStack\Uploads;

use BookStack\Exceptions\HttpFetchException;
use BookStack\Users\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            $this->destroyAllForUser($user);
            $avatar = $this->saveAvatarImage($user);
            $user->avatar()->associate($avatar);
            $user->save();
        } catch (Exception $e) {
            Log::error('Failed to save user avatar image', ['exception' => $e]);
        }
    }

    /**
     * Assign a new avatar image to the given user using the given image data.
     */
    public function assignToUserFromExistingData(User $user, string $imageData, string $extension): void
    {
        try {
            $this->destroyAllForUser($user);
            $avatar = $this->createAvatarImageFromData($user, $imageData, $extension);
            $user->avatar()->associate($avatar);
            $user->save();
        } catch (Exception $e) {
            Log::error('Failed to save user avatar image', ['exception' => $e]);
        }
    }

    /**
     * Destroy all user avatars uploaded to the given user.
     */
    public function destroyAllForUser(User $user)
    {
        $profileImages = Image::query()->where('type', '=', 'user')
            ->where('uploaded_to', '=', $user->id)
            ->get();

        foreach ($profileImages as $image) {
            $this->imageService->destroy($image);
        }
    }

    /**
     * Save an avatar image from an external service.
     *
     * @throws Exception
     */
    protected function saveAvatarImage(User $user, int $size = 500): Image
    {
        $avatarUrl = $this->getAvatarUrl();
        $email = strtolower(trim($user->email));

        $replacements = [
            '${hash}'  => md5($email),
            '${size}'  => $size,
            '${email}' => urlencode($email),
        ];

        $userAvatarUrl = strtr($avatarUrl, $replacements);
        $imageData = $this->getAvatarImageData($userAvatarUrl);

        return $this->createAvatarImageFromData($user, $imageData, 'png');
    }

    /**
     * Creates a new image instance and saves it in the system as a new user avatar image.
     */
    protected function createAvatarImageFromData(User $user, string $imageData, string $extension): Image
    {
        $imageName = Str::random(10) . '-avatar.' . $extension;

        $image = $this->imageService->saveNew($imageName, $imageData, 'user', $user->id);
        $image->created_by = $user->id;
        $image->updated_by = $user->id;
        $image->save();

        return $image;
    }

    /**
     * Gets an image from url and returns it as a string of image data.
     *
     * @throws HttpFetchException
     */
    protected function getAvatarImageData(string $url): string
    {
        try {
            $imageData = $this->http->fetch($url);
        } catch (HttpFetchException $exception) {
            throw new HttpFetchException(trans('errors.cannot_get_image_from_url', ['url' => $url]), $exception->getCode(), $exception);
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
        $configOption = config('services.avatar_url');
        if ($configOption === false) {
            return '';
        }

        $url = trim($configOption);

        if (empty($url) && !config('services.disable_services')) {
            $url = 'https://www.gravatar.com/avatar/${hash}?s=${size}&d=identicon';
        }

        return $url;
    }
}
