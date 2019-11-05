<?php

/*
 * Copyright 2019 Google LLC
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

namespace Apigee\Edge\Utility;

use Apigee\Edge\Api\Management\Entity\OrganizationInterface;

final class OrganisationFeatures
{
    /**
     * Checks whether CPS feature is enabled on an organisation.
     *
     * @param \Apigee\Edge\Api\Management\Entity\OrganizationInterface $org
     *
     * @return bool
     */
    public static function isCpsEnabled(OrganizationInterface $org): bool
    {
        return static::isFeatureEnabled('features.isCpsEnabled', $org);
    }

    /**
     * Checks whether hybrid feature is enabled on an organisation.
     *
     * @param \Apigee\Edge\Api\Management\Entity\OrganizationInterface $org
     *
     * @return bool
     */
    public static function isHybridEnabled(OrganizationInterface $org): bool
    {
        return static::isFeatureEnabled('features.hybrid.enabled', $org);
    }

    /**
     * Checks whether monetization feature is enabled on an organisation.
     *
     * @param \Apigee\Edge\Api\Management\Entity\OrganizationInterface $org
     *
     * @return bool
     */
    public static function isMonetizationEnabled(OrganizationInterface $org): bool
    {
        return static::isFeatureEnabled('features.isMonetizationEnabled', $org);
    }

    /**
     * Checks whether a feature is enabled on an organisation.
     *
     * @param string $feature
     *   Name of a "feature' property.
     * @param \Apigee\Edge\Api\Management\Entity\OrganizationInterface $org
     *   The organisation to be checked.
     *
     * @return bool
     *   TRUE if the value of the property is "true", false otherwise.
     */
    private static function isFeatureEnabled(string $feature, OrganizationInterface $org): bool
    {
        // If a property does not exist ($value === NULL) we handle it as
        // if the feature would be disabled.
        $value = $org->getPropertyValue($feature);

        return 'true' === $value;
    }
}
