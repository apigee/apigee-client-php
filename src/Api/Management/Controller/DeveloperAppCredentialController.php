<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\AppCredentialInterface;
use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\KeyValueMapNormalizer;
use Psr\Http\Message\UriInterface;

/**
 * Class DeveloperAppCredentialController.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class DeveloperAppCredentialController extends EntityController implements DeveloperAppCredentialControllerInterface
{
    use EntityCrudOperationsControllerTrait {
        // These methods are not supported on this endpoint in the same way as on the others so do not allow to
        // use them here.
        create as private privateCreate;
        update as private privateUpdate;
    }
    use StatusAwareEntityControllerTrait;

    /**
     * String that should be sent to the API to change the status of a credential to approved.
     */
    public const STATUS_APPROVE = 'approve';

    /**
     * String that should be sent to the API to change the status of a credential to revoked.
     */
    public const STATUS_REVOKE = 'revoke';

    /** @var string Developer email or id. */
    protected $developerId;

    /** @var string App name. */
    protected $appName;

    /**
     * AppCredentialController constructor.
     *
     * @param string $organization
     * @param string $developerId
     * @param string $appName
     * @param \Apigee\Edge\HttpClient\ClientInterface|null $client
     * @param \Apigee\Edge\Entity\EntityFactoryInterface $entityFactory
     */
    public function __construct(
        string $organization,
        string $developerId,
        string $appName,
        ClientInterface $client = null,
        EntityFactoryInterface $entityFactory = null
    ) {
        parent::__construct($organization, $client, $entityFactory);
        $this->developerId = $developerId;
        $this->appName = $appName;
    }

    /**
     * @inheritdoc
     */
    public function create(string $consumerKey, string $consumerSecret): AppCredentialInterface
    {
        $response = $this->client->post(
            // Just to spare some extra lines of code.
            $this->getEntityEndpointUri('create'),
            (string) json_encode((object) ['consumerKey' => $consumerKey, 'consumerSecret' => $consumerSecret])
        );

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->entityFactory->getEntityTypeByController(self::class),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function generate(
        array $apiProducts,
        array $scopes = [],
        string $keyExpiresIn = '-1'
    ): AppCredentialInterface {
        $response = $this->client->post(
            $this->getBaseEndpointUri(),
            (string) json_encode((object) [
                'apiProducts' => $apiProducts,
                'scopes' => $scopes,
                'keyExpiresIn' => $keyExpiresIn,
            ])
        );
        // It returns a complete developer app entity, but we only returns the newly created credential for the
        // sake of consistency.
        $responseArray = $this->parseResponseToArray($response);
        $credentialArray = reset($responseArray['credentials']);

        return $this->entitySerializer->denormalize(
            $credentialArray,
            $this->entityFactory->getEntityTypeByController(self::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function addProducts(string $consumerKey, array $apiProducts): AppCredentialInterface
    {
        $response = $this->client->post(
            $this->getEntityEndpointUri($consumerKey),
            (string) json_encode((object) ['apiProducts' => $apiProducts])
        );

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->entityFactory->getEntityTypeByController(self::class),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function overrideAttributes(string $consumerKey, AttributesProperty $attributes): AppCredentialInterface
    {
        $normalizer = new KeyValueMapNormalizer();
        $response = $this->client->post(
            $this->getEntityEndpointUri($consumerKey),
            (string) json_encode((object) ['attributes' => $normalizer->normalize($attributes)])
        );

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->entityFactory->getEntityTypeByController(self::class),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function setApiProductStatus(string $consumerKey, string $apiProduct, string $status): void
    {
        $uri = $this->getBaseEndpointUri()
            ->withPath(sprintf('%s/keys/%s/apiproducts/%s', $this->getBaseEndpointUri(), $consumerKey, $apiProduct))
            ->withQuery(http_build_query(['action' => $status]));
        $this->client->post($uri, null, ['Content-Type' => 'application/octet-stream']);
    }

    /**
     * @inheritdoc
     */
    public function deleteApiProduct(string $consumerKey, string $apiProduct): EntityInterface
    {
        $uri = $this->getBaseEndpointUri()
            ->withPath(sprintf('%s/keys/%s/apiproducts/%s', $this->getBaseEndpointUri(), $consumerKey, $apiProduct));
        $response = $this->client->delete($uri);

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->entityFactory->getEntityTypeByController($this),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function overrideScopes(string $consumerKey, array $scopes): AppCredentialInterface
    {
        $response = $this->client->put(
            $this->getEntityEndpointUri($consumerKey),
            (string) json_encode((object) ['scopes' => $scopes])
        );

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->entityFactory->getEntityTypeByController(self::class),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf(
                '/organizations/%s/developers/%s/apps/%s',
                $this->organization,
                $this->developerId,
                $this->appName
            ));
    }

    /**
     * @inheritdoc
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath(sprintf('%s/keys/%s', $this->getBaseEndpointUri(), $entityId));
    }
}
