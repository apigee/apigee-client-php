<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\Api\Management\Exception\DeveloperNotFoundException;
use Apigee\Edge\Entity\CpsLimitEntityController;
use Apigee\Edge\Entity\StatusAwareEntityControllerTrait;
use Psr\Http\Message\UriInterface;

/**
 * Class DeveloperController.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class DeveloperController extends CpsLimitEntityController implements DeveloperControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use StatusAwareEntityControllerTrait;

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
            ->createUri(sprintf('/organizations/%s/developers', $this->organization));
    }

    /**
     * @inheritdoc
     */
    public function getDeveloperByApp(string $appName): DeveloperInterface
    {
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query(['app' => $appName]));
        $responseArray = $this->parseResponseToArray($this->client->get($uri));
        // When developer has not found by app we are still getting back HTTP 200 with an empty developer array.
        if (empty($responseArray['developer'])) {
            throw new DeveloperNotFoundException(
                $this->client->getJournal()->getLastResponse(),
                $this->client->getJournal()->getLastRequest()
            );
        }
        $values = reset($responseArray['developer']);
        return $this->entitySerializer->denormalize($values, $this->entityFactory->getEntityByController($this));
    }
}
