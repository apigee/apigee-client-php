<?php

/*
 * Copyright 2023 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Serializer\AppGroupMembershipSerializer;
use Apigee\Edge\Api\ApigeeX\Structure\AppGroupMembership;
use Apigee\Edge\Api\Management\Serializer\AttributesPropertyAwareEntitySerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\AbstractController;
use Apigee\Edge\Controller\OrganizationAwareControllerTrait;
use Apigee\Edge\Structure\AttributesProperty;
use Psr\Http\Message\UriInterface;

/**
 * Allows to manage appgroup memberships.
 */
class AppGroupMembersController extends AbstractController implements AppGroupMembersControllerInterface
{
    use AppGroupAwareControllerTrait;
    use OrganizationAwareControllerTrait;

    /**
     * @var \Apigee\Edge\Serializer\EntitySerializerInterface
     */
    protected $serializer;

    /** @var string */
    protected $organization;

    /**
     * AppGroupMembersController constructor.
     *
     * @param string $appGroup
     * @param string $organization
     * @param ClientInterface $client
     */
    public function __construct(string $appGroup, string $organization, ClientInterface $client)
    {
        parent::__construct($client);
        $this->appGroup = $appGroup;
        $this->organization = $organization;
        $this->serializer = new AppGroupMembershipSerializer();
    }

    /**
     * {@inheritdoc}
     */
    public function getMembers(): AppGroupMembership
    {
        $response = $this->client->get($this->getBaseEndpointUri());

        return $this->serializer->denormalize($this->responseToArray($response), AppGroupMembership::class);
    }

    /**
     * {@inheritdoc}
     */
    public function setMembers(AppGroupMembership $members): AppGroupMembership
    {
        $members = $this->serializer->normalize($members);

        // We don't have a separate API to get appgroup attributes,
        // that is why we are calling getAppGroupAttributes() method.
        $apigeeReservedMembers = $this->getAppGroupAttributes();
        // Adding the new members into the attribute.
        $apigeeReservedMembers->add('__apigee_reserved__developer_details', json_encode($members));
        $response = $this->client->put(
            $this->getBaseEndpointUri(),
            (string) json_encode((object) [
                'attributes' => $this->serializer->normalize($apigeeReservedMembers),
            ])
        );

        return $this->serializer->denormalize($this->responseToArray($response), AppGroupMembership::class);
    }

    /**
     * {@inheritdoc}
     *
     * TODO: Remove removeMember method as it is not used for AppGroup membership
     * As we are storing the Team members details inside __apigee_reserved__developer_details
     * attribute, we dont have separate API to delete the members from the attribute.
     * So we update the __apigee_reserved__developer_details attribute json at setReservedMembership().
     */
    public function removeMember(string $email): void
    {
        $encoded = rawurlencode($email);
        $this->client->delete($this->getBaseEndpointUri()->withPath("{$this->getBaseEndpointUri()->getPath()}/{$encoded}"));
    }

    /**
     * {@inheritdoc}
     */
    public function getAppGroupAttributes(): AttributesProperty
    {
        $appGroup = $this->responseToArray($this->client->get($this->getBaseEndpointUri()));
        $serializer = new AttributesPropertyAwareEntitySerializer();
        $appGroupAttributes = $serializer->denormalize(
            $appGroup['attributes'],
            AttributesProperty::class
        );

        return $appGroupAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrganisationName(): string
    {
        return $this->organization;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/appgroups/{$this->appGroup}");
    }
}
