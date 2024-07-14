<?php

namespace BookStack\Access\Oidc;

use Illuminate\Support\Arr;

class OidcUserDetails
{
    public function __construct(
        public ?string $externalId = null,
        public ?string $email = null,
        public ?string $name = null,
        public ?array $groups = null,
    ) {
    }

    /**
     * Check if the user details are fully populated for our usage.
     */
    public function isFullyPopulated(bool $groupSyncActive): bool
    {
        $hasEmpty = empty($this->externalId)
            || empty($this->email)
            || empty($this->name)
            || ($groupSyncActive && $this->groups === null);

        return !$hasEmpty;
    }

    /**
     * Populate user details from the given claim data.
     */
    public function populate(
        ProvidesClaims $claims,
        string $idClaim,
        string $displayNameClaims,
        string $groupsClaim,
    ): void {
        $this->externalId = $claims->getClaim($idClaim) ?? $this->externalId;
        $this->email = $claims->getClaim('email') ?? $this->email;
        $this->name = static::getUserDisplayName($displayNameClaims, $claims) ?? $this->name;
        $this->groups = static::getUserGroups($groupsClaim, $claims) ?? $this->groups;
    }

    protected static function getUserDisplayName(string $displayNameClaims, ProvidesClaims $token): string
    {
        $displayNameClaimParts = explode('|', $displayNameClaims);

        $displayName = [];
        foreach ($displayNameClaimParts as $claim) {
            $component = $token->getClaim(trim($claim)) ?? '';
            if ($component !== '') {
                $displayName[] = $component;
            }
        }

        return implode(' ', $displayName);
    }

    protected static function getUserGroups(string $groupsClaim, ProvidesClaims $token): ?array
    {
        if (empty($groupsClaim)) {
            return null;
        }

        $groupsList = Arr::get($token->getAllClaims(), $groupsClaim);
        if (!is_array($groupsList)) {
            return null;
        }

        return array_values(array_filter($groupsList, function ($val) {
            return is_string($val);
        }));
    }
}
