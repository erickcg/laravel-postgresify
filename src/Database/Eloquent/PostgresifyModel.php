<?php

namespace MartiMarkov\Postgresify\Database\Eloquent;

use MartiMarkov\Postgresify\PostgresifyTypeCaster;
use MartiMarkov\Postgresify\Types\IntegerRange;
use MartiMarkov\Postgresify\Types\NumericRange;
use MartiMarkov\Postgresify\Types\TimestampRange;
use Illuminate\Database\Eloquent\Model;
use Smiarowski\Postgres\Model\Traits\PostgresArray;

class PostgresifyModel extends Model
{

    use PostgresArray;

    public function __construct(array $attributes = [])
    {
        $casts = $this->getCasts();

        $rangeCasts = [];
        foreach ($casts as $cast => $type) {
            if (strtolower($type) === 'numericrange' || strtolower($type) === 'integerrange') {
                $rangeCasts[$cast] = $type;
            }
        }

        foreach ($rangeCasts as $attribute => $attributeCast) {
            $fromAttributeName = $attribute . '_from';
            $toAttributeName = $attribute . '_to';
            if (array_key_exists($fromAttributeName, $attributes) and array_key_exists($toAttributeName, $attributes)) {
                $from = $attributes[$attribute . '_from'];
                $to = $attributes[$attribute . '_to'];
                switch (strtolower($attributeCast)) {
                    case 'numericrange':
                        $attributes[$attribute] = new NumericRange($from, $to);
                        unset($attributes[$attribute . '_from']);
                        unset($attributes[$attribute . '_to']);
                        break;
                    case 'integerrange':
                        $attributes[$attribute] = new IntegerRange($from, $to);
                        unset($attributes[$attribute . '_from']);
                        unset($attributes[$attribute . '_to']);
                        break;
                }
            }
        }
        parent::__construct($attributes);
    }

    public function setAttribute($key, $value)
    {
        if ($this->hasCast($key)) {
            switch ($this->getCastType($key)) {
                case 'array':
                    $value = ($value == null) ? [] : $value;
                    $value = self::mutateToPgArray($value);
                    break;
            }
        }

        return parent::setAttribute($key, $value);
    }

    protected function castAttribute($key, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($this->getCastType($key)) {
            case 'array':
                return self::accessPgArray($value);
            case 'numericrange':
                if (is_a($value, NumericRange::class)) {
                    return $value;
                }
                return (new NumericRange(0, 0))->fromPgValues($value);
            case 'timestamprange':
                if (is_a($value, TimestampRange::class)) {
                    return $value;
                }
                return (new TimestampRange(0, 0))->fromPgValues($value);
            case 'integerrange':
                if (is_a($value, IntegerRange::class)) {
                    return $value;
                }
                return (new IntegerRange(0, 0))->fromPgValues($value);
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
        return $this->hasCast($key, [
            'json',
            'object',
            'collection',
        ]);
    }
}

