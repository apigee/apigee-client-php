<?php

namespace Apigee\Edge\Controller;

/**
 * Trait StatusAwareEntityControllerTrait.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see StatusAwareEntityControllerInterface
 */
trait StatusAwareEntityControllerTrait
{
    /**
     * @inheritdoc
     */
    public function setStatus(string $entityId, string $status): void
    {
        $uri = $this->getEntityEndpointUri($entityId)->withQuery(http_build_query(['action' => $status]));
        $this->client->post($uri, null, ['Content-Type' => 'application/octet-stream']);
    }
}
