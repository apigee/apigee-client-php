<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Controller\OrganizationController;
use Apigee\Edge\Exception\UnknownEndpointException;
use Apigee\Edge\HttpClient\ClientInterface;

/**
 * Class EntityControllerFactory.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class EntityControllerFactory implements EntityControllerFactoryInterface
{
    /**
     * @var string
     */
    private $organization;
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var EntityFactoryInterface
     */
    private $entityFactory;

    /**
     * EntityControllerFactory constructor.
     *
     * @param string $organization
     * @param ClientInterface $client
     * @param EntityFactoryInterface|null $entityFactory
     */
    public function __construct(
        string $organization,
        ClientInterface $client,
        EntityFactoryInterface $entityFactory = null
    ) {
        $this->organization = $organization;
        $this->client = $client;
        $this->entityFactory = $entityFactory ?: new EntityFactory();
    }

    /**
     * @inheritdoc
     *
     * TODO Add static cache by path.
     */
    public function getControllerByEndpoint(string $path): BaseEntityControllerInterface
    {
        switch ($path) {
            case 'organizations':
                return new OrganizationController($this->client, $this->entityFactory);

            case 'developers':
                return new DeveloperController($this->organization, $this->client, $this->entityFactory);

            default:
                throw new UnknownEndpointException($path);
        }
    }
}
