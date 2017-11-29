<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\HttpClient\ClientInterface;

/**
 * Class EntityController.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
abstract class EntityController extends AbstractEntityController
{
    /** @var string Name of the organization that the entity belongs to. */
    protected $organization;

    /**
     * EntityController constructor.
     *
     * @param string $organization
     *   Name of the organization that the entities belongs to.
     * @param ClientInterface|null $client
     * @param EntityFactoryInterface|null $entityFactory
     */
    public function __construct(
        string $organization,
        ClientInterface $client = null,
        EntityFactoryInterface $entityFactory = null
    ) {
        $this->organization = $organization;
        parent::__construct($client, $entityFactory);
    }

    /**
     * @inheritdoc
     */
    public function getOrganisation(): string
    {
        return $this->organization;
    }

    /**
     * @inheritdoc
     */
    public function setOrganisation(string $orgName): string
    {
        $this->organization = $orgName;
        return $this->organization;
    }
}
