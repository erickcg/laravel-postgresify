<?php

namespace Aejnsn\Postgresify\Database\FiveSix\Schema;

use Closure;
use Illuminate\Database\Schema\PostgresBuilder;

class PostgresifyBuilder extends PostgresBuilder
{
    /**
     * Override Illuminate's Schema Builder by creating the Postgresify Blueprint.
     *
     * @param string $table
     * @param Closure|null $callback
     *
     * @return PostgresifyBlueprint
     */
    protected function createBlueprint($table, Closure $callback = null)
    {
        return new PostgresifyBlueprint($table, $callback);
    }
}
