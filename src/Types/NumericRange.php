<?php

namespace Aejnsn\Postgresify\Types;

class NumericRange extends RangeType
{
    public function __construct($pgValue)
    {
        if (empty($pgValue)
            || ($pgValue[0] != '[' && $pgValue[0] != '(')
            || ($pgValue[strlen($pgValue) - 1] != ']' && $pgValue[strlen($pgValue) - 1] != ')')
        ) {
            throw new \Exception("Not valid Postgres numrange data - bounds");
        }
        if ($pgValue[0] == '[') {
            $this->lowerBoundInclusive = true;
        } else {
            $this->lowerBoundInclusive = false;
        }

        if ($pgValue[strlen($pgValue) - 1] == ']') {
            $this->upperBoundInclusive = true;
        } else {
            $this->upperBoundInclusive = false;
        }

        $values = explode(',', substr($pgValue, 1, -1));

        if (count($values) != 2) {
            throw new \Exception("Not valid Postgres numrange data - values");
        }
        $this->lowerBound = $values[0];
        $this->upperBound = $values[1];
    }
    //
}
