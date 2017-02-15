<?php

namespace Aejnsn\Postgresify\Database\Eloquent;

use Aejnsn\Postgresify\PostgresifyTypeCaster;
use Illuminate\Database\Eloquent\Model;
use Smiarowski\Postgres\Model\Traits\PostgresArray;

class PostgresifyModel extends Model
{
    use PostgresArray;

    protected $postgresifyCasts = [];

    protected $postgresifyPrimitiveCasts = ['array'];

    public function setAttribute($key, $value)
    {
        if ($this->hasCast($key, $this->postgresifyPrimitiveCasts)) {
            $value = self::mutateToPgArray($value);
        }

        return parent::setAttribute($key, $value);
    }

    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);

        if (!is_null($value) && in_array($key, array_keys($this->postgresifyCasts))) {
            $postgresifyTypeCaster = new PostgresifyTypeCaster();
            return $postgresifyTypeCaster->cast(
                $key,
                $value,
                $this->postgresifyCasts[$key]
            );
        }

        return $value;
    }

    protected function castAttribute($key, $value)
    {
        if ($this->hasCast($key, $this->postgresifyPrimitiveCasts)) {
            return self::accessPgArray($value);
        }

        return parent::castAttribute($key, $value);
    }

    /**
     * Override to exclude array as JSON castable.
     *
     * @param  string $key
     * @return bool
     */
    protected function isJsonCastable($key)
    {
        return $this->hasCast($key, ['json', 'object', 'collection']);
    }
}

