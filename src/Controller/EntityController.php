<?php

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\OrganizationAwareControllerTrait;

/**
 * Class EntityController.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
abstract class EntityController extends AbstractEntityController
{
    use OrganizationAwareControllerTrait;

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
}
