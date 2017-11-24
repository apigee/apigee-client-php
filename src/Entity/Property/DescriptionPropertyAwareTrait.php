<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Trait DescriptionPropertyAwareTrait.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\Property\DescriptionPropertyInterface
 */
trait DescriptionPropertyAwareTrait
{
    /** @var string */
    protected $description = '';

    /**
     * @inheritdoc
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
