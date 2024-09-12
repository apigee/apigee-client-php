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

namespace Apigee\Edge\Api\Monetization\Structure\Reports\Criteria;

use Apigee\Edge\Structure\ObjectCopyHelperTrait;

/**
 * Base class for all supported Monetization reports.
 *
 * @internal
 *
 * @see https://docs.apigee.com/api-platform/monetization/create-reports#repdefconfigapi
 */
abstract class AbstractCriteria
{
    use ObjectCopyHelperTrait;

    /**
     * "appCriteria" in API request.
     *
     * @var string[]
     *   Array of app ids.
     */
    protected $apps = [];

    /**
     * "currCriteria" in API request.
     *
     * @var string[]
     */
    protected $currencies = [];

    /**
     * @var string|null
     */
    protected $currencyOption;

    /**
     * "devCriteria" in API request.
     *
     * @var string[]
     */
    protected $developers = [];

    /**
     * "monetizationPackageId" in API request, also covers "pkgCriteria".
     *
     * @var string[]
     */
    protected $apiPackages = [];

    /**
     * "prodCriteria" in API request, also covers "productIds".
     *
     * @var string[]
     */
    protected $apiProducts = [];

    /**
     * @var string[]
     */
    protected $pricingTypes = [];

    /**
     * @var string[]
     */
    protected $ratePlanLevels = [];

    /**
     * "showRevSharePct" in the API request.
     *
     * @var bool
     */
    protected $showRevenueSharePercentage = false;

    /**
     * @var bool
     */
    protected $showSummary = false;

    /**
     * "showTxDetail" in the API request.
     *
     * @var bool
     */
    protected $showTransactionDetail = false;

    /**
     * "showTxType" in the API request.
     *
     * @var bool
     */
    protected $showTransactionType = false;

    /**
     * @return string[]
     */
    public function getApps(): array
    {
        return $this->apps;
    }

    /**
     * @param string ...$appIds
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function apps(string ...$appIds): self
    {
        $this->apps = $appIds;

        return $this;
    }

    /**
     * @param string ...$appIds
     *
     * @return self
     */
    public function setApps(string ...$appIds): self
    {
        $this->apps = $appIds;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    /**
     * @param string ...$currencyIds
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function currencies(string ...$currencyIds): self
    {
        $this->currencies = $currencyIds;

        return $this;
    }

    /**
     * @param string ...$currencyIds
     *
     * @return self
     */
    public function setCurrencies(string ...$currencyIds): self
    {
        $this->currencies = $currencyIds;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrencyOption(): ?string
    {
        return $this->currencyOption;
    }

    /**
     * @param string|null $currencyOption
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function currencyOption(?string $currencyOption): self
    {
        // This tweak allows to reset the previously configured currency option
        // by calling this method with an empty string or null.
        $this->currencyOption = $currencyOption;

        return $this;
    }

    /**
     * @param string|null $currencyOption
     *
     * @return self
     */
    public function setCurrencyOption(?string $currencyOption): self
    {
        // This tweak allows to reset the previously configured currency option
        // by calling this method with an empty string or null.
        $this->currencyOption = $currencyOption;

        return $this;
    }

    /**
     * @param string ...$developerIds
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function developers(string ...$developerIds): self
    {
        $this->developers = $developerIds;

        return $this;
    }

    /**
     * @param string ...$developerIds
     *
     * @return self
     */
    public function setDevelopers(string ...$developerIds): self
    {
        $this->developers = $developerIds;

        return $this;
    }

    /**
     * @return array
     */
    public function getDevelopers(): array
    {
        return $this->developers;
    }

    /**
     * @return string[]
     */
    public function getApiPackages(): array
    {
        return $this->apiPackages;
    }

    /**
     * @param string ...$apiPackageIds
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function apiPackages(string ...$apiPackageIds): self
    {
        $this->apiPackages = $apiPackageIds;

        return $this;
    }

    /**
     * @param string ...$apiPackageIds
     *
     * @return self
     */
    public function setApiPackages(string ...$apiPackageIds): self
    {
        $this->apiPackages = $apiPackageIds;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getApiProducts(): array
    {
        return $this->apiProducts;
    }

    /**
     * @param string ...$apiProductIds
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function apiProducts(string ...$apiProductIds): self
    {
        $this->apiProducts = $apiProductIds;

        return $this;
    }

    /**
     * @param string ...$apiProductIds
     *
     * @return self
     */
    public function setApiProducts(string ...$apiProductIds): self
    {
        $this->apiProducts = $apiProductIds;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getPricingTypes(): array
    {
        return $this->pricingTypes;
    }

    /**
     * @param string ...$pricingTypes
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function pricingTypes(string ...$pricingTypes): self
    {
        $this->pricingTypes = $pricingTypes;

        return $this;
    }

    /**
     * @param string ...$pricingTypes
     *
     * @return self
     */
    public function setPricingTypes(string ...$pricingTypes): self
    {
        $this->pricingTypes = $pricingTypes;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRatePlanLevels(): array
    {
        return $this->ratePlanLevels;
    }

    /**
     * @param string ...$ratePlanLevels
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function ratePlanLevels(string ...$ratePlanLevels): self
    {
        $this->ratePlanLevels = $ratePlanLevels;

        return $this;
    }

    /**
     * @param string ...$ratePlanLevels
     *
     * @return self
     */
    public function setRatePlanLevels(string ...$ratePlanLevels): self
    {
        $this->ratePlanLevels = $ratePlanLevels;

        return $this;
    }

    /**
     * @return bool
     */
    public function getShowRevenueSharePercentage(): bool
    {
        return $this->showRevenueSharePercentage;
    }

    /**
     * @return bool
     */
    public function getShowSummary(): bool
    {
        return $this->showSummary;
    }

    /**
     * @return bool
     */
    public function getShowTransactionDetail(): bool
    {
        return $this->showTransactionDetail;
    }

    /**
     * @return bool
     */
    public function getShowTransactionType(): bool
    {
        return $this->showTransactionType;
    }

    /**
     * @param bool $show
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function showRevenueSharePercentage(bool $show): self
    {
        $this->showRevenueSharePercentage = $show;

        return $this;
    }

    /**
     * @param bool $show
     *
     * @return self
     */
    public function setShowRevenueSharePercentage(bool $show): self
    {
        $this->showRevenueSharePercentage = $show;

        return $this;
    }

    /**
     * @param bool $show
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function showSummary(bool $show): self
    {
        $this->showSummary = $show;

        return $this;
    }

    /**
     * @param bool $show
     *
     * @return self
     */
    public function setShowSummary(bool $show): self
    {
        $this->showSummary = $show;

        return $this;
    }

    /**
     * @param bool $show
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function showTransactionDetail(bool $show): self
    {
        $this->showTransactionDetail = $show;

        return $this;
    }

    /**
     * @param bool $show
     *
     * @return self
     */
    public function setShowTransactionDetail(bool $show): self
    {
        $this->showTransactionDetail = $show;

        return $this;
    }

    /**
     * @param bool $show
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function showTransactionType(bool $show): self
    {
        $this->showTransactionType = $show;

        return $this;
    }

    /**
     * @param bool $show
     *
     * @return self
     */
    public function setShowTransactionType(bool $show): self
    {
        $this->showTransactionType = $show;

        return $this;
    }
}
