<?php

namespace Illuminate\Database\Events;

class QueryExecuted
{
    /**
     * The SQL query that was executed.
     *
     * @var string
     */
    public $sql;

    /**
     * The array of query bindings.
     *
     * @var array
     */
    public $bindings;

    /**
     * The number of milliseconds it took to execute the query.
     *
     * @var float
     */
    public $time;

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    public $connection;

    /**
     * The database connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * Create a new event instance.
     *
     * @param  string  $sql
     * @param  array  $bindings
     * @param  float  $time
     * @param
     */
    public function __construct($sql, $bindings, $time, $connection)
    {
        $this->sql = $sql;
        $this->time = $time;
        $this->bindings = $bindings;
        $this->connection = $connection;
        $this->connectionName = $connection->getName();
    }
}
