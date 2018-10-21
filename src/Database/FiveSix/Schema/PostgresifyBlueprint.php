<?php


namespace Aejnsn\Postgresify\Database\FiveSix\Schema;

use Aejnsn\Postgresify\Database\Schema\PostgresifyBlueprint as PostgresifyBlueprintOld;
use Closure;
use Illuminate\Database\Schema\PostgresBuilder;

class PostgresifyBlueprint extends PostgresifyBlueprintOld
{

    /**
     * Create a new point column on the table.
     *
     * @param  string $column
     * @param  int|null $srid
     * @return \Illuminate\Support\Fluent
     */
    public function postGistPoint($column, $srid = null)
    {
        return $this->addColumn('postGisPoint', $column, compact('srid'));
    }

    /**
     * Create a new PostGIS polygon column on the table.
     *
     * @param  string $column
     * @return \Illuminate\Support\Fluent
     */
    public function postGistPolygon($column)
    {
        return $this->addColumn('postGisPolygon', $column);
    }
}
