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

use Apigee\Edge\Api\Monetization\Serializer\LegalEntityTermsAndConditionsSerializer;
use Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use DateTimeImmutable;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * Base class for developer- and company accepted terms and conditions.
 */
abstract class LegalEntityTermsAndConditionsController extends OrganizationAwareEntityController implements LegalEntityTermsAndConditionsControllerInterface
{
    use EntityListingControllerTrait;
    use ListingHelperTrait {
        EntityListingControllerTrait::responseArrayToArrayOfEntities as private traitResponseArrayToArrayOfEntities;
    }

    /**
     * AcceptedTermsAndConditionsController constructor.
     *
     * @param string $organization
     * @param ClientInterface $client
     * @param EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new LegalEntityTermsAndConditionsSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getTermsAndConditionsHistory(): array
    {
        return $this->listEntities($this->getBaseEndpointUri());
    }

    /**
     * {@inheritdoc}
     */
    public function acceptTermsAndConditionsById(string $tncId): LegalEntityTermsAndConditionsHistoryItem
    {
        $response = $this->client->post($this->getAcceptTermsAndConditionsEndpoint($tncId),
            (string) json_encode((object) [
                'action' => LegalEntityTermsAndConditionsHistoryItem::ACTION_ACCEPTED,
                // It does not matter what time we send here and in which
                // timezone.
                // @see \Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem::$auditDate
                'auditDate' => $this->entitySerializer->normalize(new DateTimeImmutable('now'), null, [DateTimeNormalizer::TIMEZONE_KEY => 'UTC']),
            ]));

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function declineTermsAndConditionsById(string $tncId): LegalEntityTermsAndConditionsHistoryItem
    {
        $response = $this->client->post($this->getAcceptTermsAndConditionsEndpoint($tncId),
            (string) json_encode((object) [
                'action' => LegalEntityTermsAndConditionsHistoryItem::ACTION_DECLINED,
                // It does not matter what time we send here and in which
                // timezone.
                // @see \Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem::$auditDate
                'auditDate' => $this->entitySerializer->normalize(new DateTimeImmutable('now'), null, [DateTimeNormalizer::TIMEZONE_KEY => 'UTC']),
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
     * @return UriInterface
     */
    abstract protected function getAcceptTermsAndConditionsEndpoint(string $tncId): UriInterface;

    /**
     * {@inheritdoc}
     */
    protected function responseArrayToArrayOfEntities(array $responseArray, string $keyGetter = 'getId'): array
    {
        // We had to override the default key getter method name because an
        // accepted rate plan is not an entity so it does not have the
        // "id" method.
        return $this->traitResponseArrayToArrayOfEntities($responseArray, $keyGetter);
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return LegalEntityTermsAndConditionsHistoryItem::class;
    }
}
