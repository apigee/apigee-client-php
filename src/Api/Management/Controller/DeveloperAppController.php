<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Entity\EntityController;
use Apigee\Edge\Entity\EntityControllerFactoryInterface;
use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\Entity\NonCpsLimitEntityControllerTrait;
use Apigee\Edge\Entity\StatusAwareEntityControllerTrait;
use Apigee\Edge\HttpClient\ClientInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class DeveloperAppController.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class DeveloperAppController extends EntityController implements DeveloperAppControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use NonCpsLimitEntityControllerTrait;
    use StatusAwareEntityControllerTrait;

    /** @var string Developer email or id. */
    protected $developer;

    /**
     * DeveloperAppController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\HttpClient\ClientInterface|null $developer
     * @param \Apigee\Edge\HttpClient\ClientInterface|null $client
     * @param \Apigee\Edge\Entity\EntityFactoryInterface|null $entityFactory
     * @param \Apigee\Edge\Entity\EntityControllerFactoryInterface|null $entityControllerFactory
     */
    public function __construct(
        $organization,
        $developer,
        ClientInterface $client = null,
        EntityFactoryInterface $entityFactory = null,
        EntityControllerFactoryInterface $entityControllerFactory = null
    ) {
        $this->developer = $developer;
        parent::__construct($organization, $client, $entityFactory, $entityControllerFactory);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf('/organizations/%s/developers/%s/apps', $this->organization, $this->developer));
    }
}
