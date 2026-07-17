<?php

namespace BookStack\Util;

use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Run the given code within a database transactions.
 * Wraps Laravel's own transaction method, but sets a specific runtime isolation method.
 * This sets a session level since this won't cause issues if already within a transaction,
 * and this should apply to the next transactions anyway.
 *
 * "READ COMMITTED" ensures that changes from other transactions can be read within
 * a transaction, even if started afterward (and for example, it was blocked by the initial
 * transaction). This is quite important for things like permission generation, where we would
 * want to consider the changes made by other committed transactions by the time we come to
 * regenerate permission access.
 *
 * @throws Throwable
 * @template TReturn of mixed
 */
class DatabaseTransaction
{
    /**
     * @param  (Closure(static): TReturn)  $callback
     */
    public function __construct(
        protected Closure $callback
    ) {
    }

    /**
     * @return TReturn
     */
    public function run(): mixed
    {
        DB::statement('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
        return DB::transaction($this->callback);
    }
}
