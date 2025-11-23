<?php

namespace BookStack\Access\Oidc;

use UnitEnum;

enum OidcJwtSigningKeyAlgorithm: string
{
    case RS256 = 'RS256';
    case RS512 = 'RS512';

    public static function getSupportedAlgorithms(): string
    {
        return join(',', array_map(static fn (UnitEnum $enum) => $enum->value, self::cases()));
    }
}
