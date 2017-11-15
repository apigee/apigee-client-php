<?php

namespace Apigee\Edge\Util;

/**
 * Interface ArrayConversionInterface.
 *
 * Unfortunately PHP still does not have proper support for casting objects to arrays with `(array) $object`. The
 * primary goal of this interface to provide a general solution for us that works on our classes properly, because
 * our classes does not have public properties. Also, we want to have control over how certain properties are being
 * converted to array members.
 *
 * @package Apigee\Edge\Util
 * @author Dezső Biczó <mxr576@gmail.com>
 * @link https://bugs.php.net/bug.php?id=38508
 */
interface ArrayConversionInterface
{
    /**
     * Creates an object from an array.
     *
     * @param array $values
     *
     * @return mixed
     */
    public static function fromArray(array $values) : ArrayConversionInterface;

    /**
     * Converts an object to an array.
     *
     * @return array
     */
    public function toArray() : array;
}
