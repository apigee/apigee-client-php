<?php

/*
 * Copyright 2018 Google LLC
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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface;
use Apigee\Edge\Api\Monetization\Serializer\AcceptedTermsAndConditionsSerializer;
use Apigee\Edge\Api\Monetization\Structure\AcceptedTermsAndConditions;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * Base class for developer- and company accepted terms and conditions.
 */
abstract class AcceptedTermsAndConditionsController extends OrganizationAwareEntityController implements AcceptedTermsAndConditionsControllerInterface
{
    use EntityListingControllerTrait;
    use ListingHelperTrait {
        responseArrayToArrayOfEntities as private traitResponseArrayToArrayOfEntities;
    }

    /**
     * AcceptedTermsAndConditionsController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new AcceptedTermsAndConditionsSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * @inheritdoc
     */
    public function getAcceptedTermsAndConditions(): array
    {
        return $this->listEntities($this->getBaseEndpointUri());
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress PossiblyNullArgument - id can not be null here.
     * @psalm-suppress PossiblyNullReference - it can not be null here.
     */
    public function acceptTermsAndConditions(TermsAndConditionsInterface $tnc, \DateTimeImmutable $auditDate): AcceptedTermsAndConditions
    {
        $response = $this->client->post($this->getAcceptTermsAndConditionsEndpoint($tnc->id()),
            (string) json_encode((object) [
            'action' => AcceptedTermsAndConditions::ACTION_ACCEPTED,
            // Ensure we are sending the audit date in the right timezone.
            'auditDate' => $this->entitySerializer->normalize($auditDate, null, [DateTimeNormalizer::TIMEZONE_KEY => $tnc->getOrganization()->getTimezone()]),
        ]));

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * Returns the accept terms and conditions endpoint URI.
     *
     * @param string $tncId
     *   Terms and Conditions ID.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    abstract protected function getAcceptTermsAndConditionsEndpoint(string $tncId): UriInterface;

    /**
     * @inheritDoc
     */
    protected function responseArrayToArrayOfEntities(array $responseArray, string $keyGetter = 'getId'): array
    {
        // We had to override the default key getter method name because an
        // accepted rate plan is not an entity so it does not have "id" method.
        return $this->traitResponseArrayToArrayOfEntities($responseArray, $keyGetter);
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return AcceptedTermsAndConditions::class;
    }
}
