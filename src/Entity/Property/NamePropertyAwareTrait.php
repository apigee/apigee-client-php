<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Trait NamePropertyAwareTrait.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 */
trait NamePropertyAwareTrait
{
    /** @var string */
    protected $name;

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
