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

namespace Apigee\Edge\Tests\Api\Monetization\EntitySerializer;

use Apigee\Edge\Api\Monetization\Entity\RatePlanRevisionInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\ApiPackageEntityReferencePropertyValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\CurrencyEntityReferencePropertyValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\DeveloperCategoryEntityReferencePropertyValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\LegalEntityEntityReferencePropertyValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\ParentRatePlanEntityReferencePropertyValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\RatePlanDetailsPropertyValidator;
use stdClass;

class RatePlanSerializerValidator extends OrganizationAwareEntitySerializerValidator
{
    /**
     * RatePlanSerializerValidator constructor.
     *
     * @param EntitySerializerInterface $serializer
     * @param array $propertyValidators
     */
    public function __construct(?EntitySerializerInterface $serializer = null, array $propertyValidators = [])
    {
        $propertyValidators = array_merge($propertyValidators, [
            new CurrencyEntityReferencePropertyValidator(),
            new ApiPackageEntityReferencePropertyValidator(),
            new RatePlanDetailsPropertyValidator(),
            // For developer specific rate plans.
            new LegalEntityEntityReferencePropertyValidator(),
            // For developer category specific rate plans.
            new DeveloperCategoryEntityReferencePropertyValidator(),
            // For rate plan revisions.
            new ParentRatePlanEntityReferencePropertyValidator(),
        ]);
        parent::__construct($serializer, $propertyValidators);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(stdClass $input, EntityInterface $entity): void
    {
        /* @var \Apigee\Edge\Api\Monetization\Entity\StandardRatePlanInterface $entity */
        // According to engineering this is a transient property and it should
        // be ignored.
        unset($input->customPaymentTerm);
        foreach ($entity->getRatePlanDetails() as $id => $detail) {
            unset($input->ratePlanDetails[$id]->customPaymentTerm);
        }
        if ($entity instanceof RatePlanRevisionInterface) {
            // Validate here whether the serializer could deserialize all
            // information from the input about the parent rate plan properly.
            // Testing the serialization of the parent rate plan reference
            // happens in ParentRatePlanEntityReferencePropertyValidator.
            $this->validate($input->parentRatePlan, $entity->getPreviousRatePlanRevision());
        } else {
            // This property can be ignored because its value only matters on
            // rate plan revisions (future rate plans).
            unset($input->keepOriginalStartDate);
        }
        parent::validate($input, $entity);
    }
}
