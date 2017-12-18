<?php

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\HttpClient\ClientInterface;

/**
 * Class EntityController.
 *
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
     * @param \Apigee\Edge\Entity\EntityFactoryInterface|null $entityFactory
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
    public function setOrganisation(string $orgName): void
    {
        $this->organization = $orgName;
    }
}
