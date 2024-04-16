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
            || ($groupSyncActive && empty($this->groups));

        return !$hasEmpty;
    }

    /**
     * Populate user details from OidcIdToken data.
     */
    public static function fromToken(
        OidcIdToken $token,
        string $idClaim,
        string $displayNameClaims,
        string $groupsClaim,
    ): static {
        $id = $token->getClaim($idClaim);

        return new self(
            externalId: $id,
            email: $token->getClaim('email'),
            name: static::getUserDisplayName($displayNameClaims, $token, $id),
            groups: static::getUserGroups($groupsClaim, $token),
        );
    }

    protected static function getUserDisplayName(string $displayNameClaims, OidcIdToken $token, string $defaultValue): string
    {
        $displayNameClaimParts = explode('|', $displayNameClaims);

        $displayName = [];
        foreach ($displayNameClaimParts as $claim) {
            $component = $token->getClaim(trim($claim)) ?? '';
            if ($component !== '') {
                $displayName[] = $component;
            }
        }

        if (count($displayName) === 0) {
            $displayName[] = $defaultValue;
        }

        return implode(' ', $displayName);
    }

    protected static function getUserGroups(string $groupsClaim, OidcIdToken $token): array
    {
        if (empty($groupsClaim)) {
            return [];
        }

        $groupsList = Arr::get($token->getAllClaims(), $groupsClaim);
        if (!is_array($groupsList)) {
            return [];
        }

        return array_values(array_filter($groupsList, function ($val) {
            return is_string($val);
        }));
    }
}
