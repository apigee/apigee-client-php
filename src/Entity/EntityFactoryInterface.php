<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity;

/**
 * Interface EntityFactoryInterface.
 */
interface EntityFactoryInterface
{
    /**
     * Returns the FQCN of the entity class that belongs to an entity controller.
     *
     * @param string|\Apigee\Edge\Controller\AbstractEntityController $entityController
     *   Fully qualified class name or an object.
     *
     * @return string
     */
    public function getEntityTypeByController($entityController): string;

    /**
     * Returns a new instance of entity that belongs to an entity controller.
     *
     * @param string|\Apigee\Edge\Controller\AbstractEntityController $entityController
     *   Fully qualified class name or an object.
     *
     * @return \Apigee\Edge\Entity\EntityInterface
     */
    public function getEntityByController($entityController): EntityInterface;
}
