<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Trait EnvironmentsPropertyAwareTrait.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
trait EnvironmentsPropertyAwareTrait
{
    /** @var string[] */
    protected $environments = [];

    /**
     * @inheritdoc
     */
    public function getEnvironments(): array
    {
        return $this->environments;
    }

    /**
     * @param array $environments
     */
    public function setEnvironments(array $environments): void
    {
        $this->environments = $environments;
    }
}
