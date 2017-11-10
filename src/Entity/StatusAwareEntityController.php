<?php

namespace Apigee\Edge\Entity;

/**
 * Class StatusAwareEntityController.
 *
 * Entity controller for those entities that has "status" property and the value of that property (and with that the
 * status of the entity itself) can be changed only with an additional API call.
 *
 * @link https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D
 * @link https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
abstract class StatusAwareEntityController extends EntityController implements StatusAwareEntityControllerInterface
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
