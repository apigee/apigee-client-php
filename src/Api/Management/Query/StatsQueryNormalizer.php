<?php

namespace Apigee\Edge\Api\Management\Query;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class StatsQueryNormalizer.
 *
 * Normalizes StatsQueryInterface objects to an array that can be passed to the Stats API.
 */
class StatsQueryNormalizer implements NormalizerInterface
{
    const DATE_FORMAT = 'm/d/Y H:i';

    /** @var ObjectNormalizer */
    private $objectNormalizer;

    /** @var DateTimeNormalizer */
    private $dateNormalizer;

    /** @var Serializer */
    private $serializer;

    /**
     * StatsQueryNormalizer constructor.
     */
    public function __construct()
    {
        $this->objectNormalizer = new ObjectNormalizer();
        $this->objectNormalizer->setIgnoredAttributes(['timeRange']);
        $this->serializer = new Serializer([$this->objectNormalizer], [new JsonEncoder()]);
        // Timezone of Apigee Edge is UTC.
        $this->dateNormalizer = new DateTimeNormalizer(self::DATE_FORMAT, new \DateTimeZone('UTC'));
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress InvalidOperand - $this->dateNormalizer->normalize() always returns a string.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var StatsQueryInterface $object */
        // Transform the object to JSON and back to an array to keep boolean values as boolean.
        $json = $this->serializer->serialize($object, 'json');
        $data = $this->serializer->decode($json, 'json');
        // Replace metrics with the required query parameter name and value.
        $data['select'] = implode(',', $data['metrics']);
        unset($data['metrics']);
        // Transforming timeRange to the required format.
        $data['timeRange'] = $this->dateNormalizer->normalize($object->getTimeRange()->getStartDate()) . '~' .
            $this->dateNormalizer->normalize($object->getTimeRange()->getEndDate());
        $data = array_filter($data);
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
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof StatsQuery;
    }
}
