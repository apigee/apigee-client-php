<?php

namespace Apigee\Edge\Tests\Test\FrameWork\Constrait;

use PHPUnit\Framework\Constraint\ArraySubset;

/**
 * Class EntityHasValues.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class EntityHasValues extends ArraySubset
{
    public function toString()
    {
        return 'is a subset of ' . $this->exporter->export($this->subset);
    }

    protected function failureDescription($other)
    {
        return 'an entity values ' . $this->toString();
    }
}
