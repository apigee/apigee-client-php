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

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Api\Management\Controller\CompanyAppController;
use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Entity\EntityDenormalizer;
use Apigee\Edge\Entity\EntityFactory;
use Apigee\Edge\Entity\EntityFactoryInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class AppDenormalizer.
 */
class AppDenormalizer extends EntityDenormalizer implements DenormalizerInterface
{
    /**
     * @var \Apigee\Edge\Entity\EntityFactoryInterface
     */
    protected $entityFactory;

    /**
     * AppDenormalizer constructor.
     *
     * @param \Apigee\Edge\Entity\EntityFactoryInterface|null $entityFactory
     */
    public function __construct(EntityFactoryInterface $entityFactory = null)
    {
        $this->entityFactory = $entityFactory ?: new EntityFactory();
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->developerId)) {
            return parent::denormalize($data, $this->entityFactory->getEntityTypeByController(DeveloperAppController::class));
        } elseif (isset($data->companyName)) {
            return parent::denormalize($data, $this->entityFactory->getEntityTypeByController(CompanyAppController::class));
        }
        throw new UnexpectedValueException(
            'Unable to denormalize because both "developerId" and "companyName" are missing from data.'
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return AppInterface::class === $type || $type instanceof AppInterface || in_array($type, class_implements(AppInterface::class));
    }
}
