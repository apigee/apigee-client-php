<?php

namespace Apigee\Edge\Exception;

/**
 * Class ClientErrorException.
 *
 * For >= 400 and < 500 HTTP codes.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class ClientErrorException extends ApiResponseException
{
}
