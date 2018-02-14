<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Exception;

/**
 * Class ClientErrorException.
 *
 * For >= 400 and < 500 HTTP codes.
 */
class ClientErrorException extends ApiResponseException
{
}
