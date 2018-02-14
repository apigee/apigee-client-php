<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Controller;

/**
 * Trait StatusAwareEntityControllerTrait.
 *
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
