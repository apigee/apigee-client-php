<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Entity\EntityController;
use Apigee\Edge\Entity\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\Entity\NonCpsListingEntityControllerTrait;
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
    use EntityCrudOperationsControllerTrait;
    use NonCpsListingEntityControllerTrait;
    use StatusAwareEntityControllerTrait;

    /** @var string Developer email or id. */
    protected $developerId;

    /**
     * DeveloperAppController constructor.
     *
     * @param string $organization
     * @param string $developerId
     * @param \Apigee\Edge\HttpClient\ClientInterface|null $client
     * @param \Apigee\Edge\Entity\EntityFactoryInterface|null $entityFactory
     */
    public function __construct(
        string $organization,
        string $developerId,
        ClientInterface $client = null,
        EntityFactoryInterface $entityFactory = null
    ) {
        $this->developerId = $developerId;
        parent::__construct($organization, $client, $entityFactory);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf('/organizations/%s/developers/%s/apps', $this->organization, $this->developerId));
    }
}
