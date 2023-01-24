<?php

namespace BookStack\Auth\Permissions;

class PermissionStatus
{
    const IMPLICIT_DENY = 0;
    const IMPLICIT_ALLOW = 1;
    const EXPLICIT_DENY = 2;
    const EXPLICIT_ALLOW = 3;
}
