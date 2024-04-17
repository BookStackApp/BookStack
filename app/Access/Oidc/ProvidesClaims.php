<?php

namespace BookStack\Access\Oidc;

interface ProvidesClaims
{
    /**
     * Fetch a specific claim.
     * Returns null if it is null or does not exist.
     */
    public function getClaim(string $claim): mixed;

    /**
     * Get all contained claims.
     */
    public function getAllClaims(): array;
}
