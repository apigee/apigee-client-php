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

namespace Apigee\Edge\Tests\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerAwareTestTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerAwareTestTrait;
use PHPUnit\Framework\Assert;

trait TimezoneConversionTestTrait
{
    use EntityControllerAwareTestTrait;
    use EntitySerializerAwareTestTrait;
    use DefaultAPIClientAwareTrait;

    /**
     * The difference between Europe/Budapest and Australia/Eucla (used as
     * default org timezone in example JSONs) is +7 hours 45 minutes in New
     * Year's Eve.
     */
    public function testTimezoneConversation(): void
    {
        $original_timezone = date_default_timezone_get();
        $currentTimezone = 'Europe/Budapest';
        // We change the timezone before we would do anything else to ensure
        // any subsequent calls as working properly.
        date_default_timezone_set($currentTimezone);
        $entity = clone $this->getTestEntityForTimezoneConversion();
        $dateProperties = $this->getDateProperties($entity);
        if (empty($dateProperties)) {
            // Inform us that this test trait is being use on an entity
            // that does not have a date property - or something unaccepted
            // happened.
            Assert::markTestSkipped(sprintf('No date property found in %s.', get_class($entity)));
        }
        foreach ($dateProperties as $property) {
            $getter = 'get' . ucfirst($property);
            $setter = 'set' . ucfirst($property);
            // Make sure that even if the timezone of the organization is not
            // the default UTC we parsed the date string with the proper
            // timezone.
            $this->assertEquals($this->getOrganizationTimezoneFromEntity($entity)->getName(), $entity->{$getter}()->getTimezone()->getName());

            // Change value of timezone, use the current timezone instead of
            // the org's timezone.
            $entity->{$setter}(\DateTimeImmutable::createFromFormat(EntityInterface::DATE_FORMAT, '2017-12-31 16:15:59'));
            $this->assertEquals($currentTimezone, $entity->{$getter}()->getTimezone()->getName());
        }
        // Ensure the serializer converts the value of date properties to org's
        // timezone properly.
        $payload = $this->entitySerializer()->serialize($entity, 'json');
        $json = json_decode($payload);
        foreach ($dateProperties as $property) {
            $this->assertEquals('2018-01-01 00:00:59', $json->{$property});
        }
        // Restoring the original timezone.
        date_default_timezone_set($original_timezone);
    }

    abstract protected function getTestEntityForTimezoneConversion(): EntityInterface;

    /**
     * Extracts parent Organization's timezone information from an entity.
     *
     * By default this method assums that the provided entity is an organization
     * aware entity, but it could happen that it does not, ex.: rate plans.
     * On Rate Plans the parent API package contains the organization reference.
     *
     * @param \Apigee\Edge\Api\Monetization\Entity\EntityInterface $entity
     *
     * @return \DateTimeZone
     */
    protected function getOrganizationTimezoneFromEntity(EntityInterface $entity): \DateTimeZone
    {
        /* @var \Apigee\Edge\Api\Monetization\Entity\OrganizationAwareEntityInterface $entity */
        return $entity->getOrganization()->getTimezone();
    }

    /**
     * Collect all date properties from entity (non-recursively) with reflection.
     *
     * @param \Apigee\Edge\Api\Monetization\Entity\EntityInterface $entity
     *
     * @return string[]
     *   Array of property names.
     */
    private function getDateProperties(EntityInterface $entity): array
    {
        $properties = [];

        $ro = new \ReflectionObject($entity);
        foreach ($ro->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($entity);
            if ($value instanceof \DateTimeInterface) {
                $properties[] = $property->getName();
            }
        }

        return $properties;
    }
}
