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

namespace Apigee\Edge\Api\Monetization\Denormalizer;

use Apigee\Edge\Api\Monetization\Entity\DeveloperReportDefinition;

class DeveloperReportDefinitionDenormalizer extends ReportDefinitionDenormalizer
{
    /**
     * Fully qualified class name of the developer report definition entity.
     *
     * @var string
     */
    protected $developerReportDefinitionClass = DeveloperReportDefinition::class;

    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return parent::denormalize($data, $this->developerReportDefinitionClass, $format, $context);
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if (parent::supportsDenormalization($data, $type, $format)) {
            // Non-developer specific reports API also returns the developer
            // property, but it is always NULL.
            return is_object($data->developer) && !$data->developer->isCompany;
        }

        return false;
    }
}
