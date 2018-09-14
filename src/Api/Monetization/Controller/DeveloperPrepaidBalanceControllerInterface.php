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

/**
 * Interface DeveloperPrepaidBalanceControllerInterface.
 *
 * @see https://docs.apigee.com/api-platform/monetization/manage-prepaid-balances#settingupautomaticreloadofprepaidaccountbalancesusingtheapi-settingupautomaticreloadofprepaidaccountbalancesforadeveloper
 * @see https://apidocs.apigee.com/monetize/apis/get/organizations/%7Borg_name%7D/developers/%7Bdeveloper_id%7D/developer-balances
 * @see https://apidocs.apigee.com/monetize/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_id%7D/developer-balances
 * @see https://apidocs.apigee.com/monetize/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_id%7D/developer-balances/recurring-setup
 */
interface DeveloperPrepaidBalanceControllerInterface extends PrepaidBalanceControllerInterface
{
}
