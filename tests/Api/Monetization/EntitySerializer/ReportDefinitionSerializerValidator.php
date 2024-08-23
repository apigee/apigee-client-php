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

use Apigee\Edge\Entity\EntityInterface;

class ReportDefinitionSerializerValidator extends OrganizationAwareEntitySerializerValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate(\stdClass $input, EntityInterface $entity): void
    {
        // Developer property gets removed from the normalized output so we
        // do not validate it here.
        // \Apigee\Edge\Api\Monetization\Normalizer\LegalEntityReportDefinitionNormalizer::normalize()
        unset($input->developer);
        unset($input->mintCriteria->devCriteria);
        unset($input->mintCriteria->groupBy);
        unset($input->mintCriteria->monetizationPackageIds);
        unset($input->mintCriteria->ratePlanLevels);
        unset($input->mintCriteria->transactionTypes);
        unset($input->mintCriteria->currencyOption);
        parent::validate($input, $entity);
    }
}
