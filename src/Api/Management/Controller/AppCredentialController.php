<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\AppCredentialInterface;
use Apigee\Edge\Entity\EntityController;
use Apigee\Edge\Entity\EntityCrudOperationsTrait;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\StatusAwareEntityControllerTrait;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\KeyValueMapNormalizer;
use Psr\Http\Message\UriInterface;

/**
 * Class AppCredentialController.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * TODO
 * Information about request payload is needed to be implemented:
 *     https://docs.apigee.com/management/apis/delete/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D
 */
class AppCredentialController extends EntityController implements AppCredentialControllerInterface
{
    use StatusAwareEntityControllerTrait {
        // We only alias this to be able to add better documentation to the method.
        StatusAwareEntityControllerTrait::setStatus as protected protectedSetStatus;
    }

    use EntityCrudOperationsTrait {
        // These methods are not supported on this endpoint in the same way as on the others so do not allow to
        // use them here.
        EntityCrudOperationsTrait::create as private privateCreate;
        EntityCrudOperationsTrait::update as private privateUpdate;
    }

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
     * @param null $client
     * @param null $entityFactory
     */
    public function __construct(
        string $organization,
        string $developerId,
        string $appName,
        $client = null,
        $entityFactory = null
    ) {
        parent::__construct($organization, $client, $entityFactory);
        $this->developerId = $developerId;
        $this->appName = $appName;
    }

    /**
     * Returns the API endpoint that the controller communicates with.
     *
     * In case of an entity that belongs to an organisation it should return organization/[orgName]/[endpoint].
     *
     * @return UriInterface
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

    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath(sprintf('%s/keys/%s', $this->getBaseEndpointUri(), $entityId));
    }

    /**
     * Creates a new consumer key and secret for an app.
     *
     * @param string $consumerKey
     * @param string $consumerSecret
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppCredentialInterface
     *
     * @link https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/create
     */
    public function create(string $consumerKey, string $consumerSecret): AppCredentialInterface
    {
        $response = $this->client->post(
            // Just to spare some extra lines of code.
            $this->getEntityEndpointUri('create'),
            json_encode((object)['consumerKey' => $consumerKey, 'consumerSecret' => $consumerSecret])
        );
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            $this->entityFactory->getEntityTypeByController(AppCredentialController::class),
            'json'
        );
    }

    /**
     * Generates a new key pair for an app.
     *
     * @link https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D-0
     *
     * @param string[] $apiProducts
     *   API Product names.
     * @param \Apigee\Edge\Structure\AttributesProperty $attributes
     *   Custom attributes.
     * @param string $keyExpiresIn
     *   In milliseconds. A value of -1 means the key/secret pair never expire.
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppCredentialInterface
     */
    public function generate(
        array $apiProducts,
        AttributesProperty $attributes,
        string $keyExpiresIn = '-1'
    ): AppCredentialInterface {
        $normalizer = new KeyValueMapNormalizer();
        $response = $this->client->post(
            $this->getBaseEndpointUri(),
            json_encode((object)[
                'apiProducts' => $apiProducts,
                'attributes' => $normalizer->normalize($attributes),
                'keyExpiresIn' => $keyExpiresIn
            ])
        );
        // It returns a complete developer app entity, but we only returns the newly created credential for the
        // sake of consistency.
        $responseArray = $this->parseResponseToArray($response);
        $credentialArray = reset($responseArray['credentials']);
        return $this->entitySerializer->denormalize(
            $credentialArray,
            $this->entityFactory->getEntityTypeByController(AppCredentialController::class)
        );
    }

    /**
     * Adds API products to a consumer key.
     *
     * @link https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D
     *
     * Modifying attributes of a consumer key is intentionally separated because attributes can not just be added but
     * existing ones can be removed if they are missing from the payload.
     *
     * @param string $consumerKey
     *   The consumer key to modify.
     * @param string[] $apiProducts
     *   API Product names.
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppCredentialInterface
     */
    public function addProducts(string $consumerKey, array $apiProducts): AppCredentialInterface
    {
        $response = $this->client->post(
            $this->getEntityEndpointUri($consumerKey),
            json_encode((object)['apiProducts' => $apiProducts])
        );
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            $this->entityFactory->getEntityTypeByController(AppCredentialController::class),
            'json'
        );
    }

    /**
     * Modify attributes of a customer key.
     *
     * Existing attributes can be removed if those are not included in the passed $attributes variable!
     *
     * @link https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D
     *
     * @param string $consumerKey
     *   The consumer key to modify.
     * @param \Apigee\Edge\Structure\AttributesProperty $attributes
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppCredentialInterface
     */
    public function modifyAttributes(string $consumerKey, AttributesProperty $attributes): AppCredentialInterface
    {
        $normalizer = new KeyValueMapNormalizer();
        $response = $this->client->post(
            $this->getEntityEndpointUri($consumerKey),
            json_encode((object)['attributes' => $normalizer->normalize($attributes)])
        );
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            $this->entityFactory->getEntityTypeByController(AppCredentialController::class),
            'json'
        );
    }

    /**
     * Approve or revoke API product for an API key.
     *
     * @link https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D/apiproducts/%7Bapiproduct_name%7D
     *
     * @param string $consumerKey
     * @param string $apiProduct
     * @param string $status
     */
    public function setApiProductStatus(string $consumerKey, string $apiProduct, string $status): void
    {
        $uri = $this->getBaseEndpointUri()
            ->withPath(sprintf('%s/keys/%s/apiproducts/%s', $this->getBaseEndpointUri(), $consumerKey, $apiProduct))
            ->withQuery(http_build_query(['action' => $status]));
        $this->client->post($uri, null, ['Content-Type' => 'application/octet-stream']);
    }

    /**
     * Remove API product for a consumer key for an developer app.
     *
     * @link https://docs.apigee.com/management/apis/delete/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D
     *
     * @param string $consumerKey
     * @param string $apiProduct
     *
     * @return \Apigee\Edge\Entity\EntityInterface
     */
    public function deleteApiProduct(string $consumerKey, string $apiProduct): EntityInterface
    {
        $uri = $this->getBaseEndpointUri()
            ->withPath(sprintf('%s/keys/%s/apiproducts/%s', $this->getBaseEndpointUri(), $consumerKey, $apiProduct));
        $response = $this->client->delete($uri);
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            $this->entityFactory->getEntityTypeByController($this),
            'json'
        );
    }
}
