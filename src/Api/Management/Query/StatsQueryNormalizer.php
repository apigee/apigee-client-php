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

namespace Apigee\Edge\Api\Management\Query;

use Apigee\Edge\Serializer\JsonEncoder;
use DateTimeZone;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class StatsQueryNormalizer.
 *
 * Normalizes StatsQueryInterface objects to an array that can be passed to the Stats API as query parameter.
 */
class StatsQueryNormalizer implements NormalizerInterface
{
    public const DATE_FORMAT = 'm/d/Y H:i';

    /** @var ObjectNormalizer */
    private $objectNormalizer;

    /** @var Serializer */
    private $serializer;

    /**
     * StatsQueryNormalizer constructor.
     */
    public function __construct()
    {
        $this->objectNormalizer = new ObjectNormalizer();
        $this->serializer = new Serializer([$this->objectNormalizer], [new JsonEncoder()]);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var StatsQueryInterface $object */
        // Transform the object to JSON and back to an array to keep boolean values as boolean.
        $json = $this->serializer->serialize($object, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['timeRange']]);
        $data = $this->serializer->decode($json, 'json');
        // Replace metrics with the required query parameter name and value.
        $data['select'] = implode(',', $data['metrics']);
        unset($data['metrics']);
        // Transform timeRange to the required format and time zone.
        $utc = new DateTimeZone('UTC');
        $data['timeRange'] = $object->getTimeRange()->startDate->setTimezone($utc)->format(self::DATE_FORMAT) . '~' .
            $object->getTimeRange()->endDate->setTimezone($utc)->format(self::DATE_FORMAT);
        // Remove null values.
        $data = array_filter($data, function ($value) {
            return !is_null($value);
        });
        // Fix boolean values.
        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $data[$key] = $value ? 'true' : 'false';
            }
        }
        // Following parameter names should be passed in lowercase format.
        // (We solve this problem in place instead of creating a name converter.)
        if (isset($data['sortBy'])) {
            $data['sortby'] = $data['sortBy'];
            unset($data['sortBy']);
        }
        if (isset($data['topK'])) {
            $data['topk'] = $data['topK'];
            unset($data['topK']);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof StatsQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            StatsQuery::class => true,
        ];
    }
}
