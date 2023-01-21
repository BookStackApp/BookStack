<?php

namespace Tests\Unit;

use BadMethodCallException;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

/**
 * This class tests assumptions we're relying upon in the framework.
 * This is primarily to keep track of certain bits of functionality that
 * may be used in important areas such as to enforce permissions.
 */
class FrameworkAssumptionTest extends TestCase
{
    public function test_scopes_error_if_not_existing()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Call to undefined method BookStack\Entities\Models\Page::scopeNotfoundscope()');
        Page::query()->scopes('notfoundscope');
    }

    public function test_scopes_applies_upon_existing()
    {
        // Page has SoftDeletes trait by default, so we apply our custom scope and ensure
        // it stacks on the global scope to filter out deleted items.
        $query = Page::query()->scopes('visible')->toSql();
        $this->assertStringContainsString('entity_permissions_collapsed', $query);
        $this->assertStringContainsString('`deleted_at` is null', $query);
    }
}
