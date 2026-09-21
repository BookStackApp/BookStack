<?php

namespace BookStack\View;

/**
 * Base class for view blocks.
 * This is intended as a base implementation for the ViewBlockInterface, and it's advised to extend
 * this since we will aim to keep child implementations of this class forward compatible with future changes
 * of the interface.
 */
abstract class BaseViewBlock implements ViewBlockInterface
{
}
