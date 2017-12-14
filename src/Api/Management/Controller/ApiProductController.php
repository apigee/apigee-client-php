<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Entity\EntityController;
use Apigee\Edge\Entity\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Entity\NonCpsListingEntityControllerTrait;
use Psr\Http\Message\UriInterface;

/**
 * Class ApiProductController.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class ApiProductController extends EntityController implements ApiProductControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use EntityCrudOperationsControllerTrait;
    use NonCpsListingEntityControllerTrait;

    /**
     * @inheritdoc
     */
    public function searchByAttribute(string $attributeName, string $attributeValue): array
    {
        $query_params = [
            'attributename' => $attributeName,
            'attributevalue' => $attributeValue,
        ];
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);

        return $this->parseResponseToArray($response);
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
            ->createUri(sprintf('/organizations/%s/apiproducts', $this->organization));
    }
}
