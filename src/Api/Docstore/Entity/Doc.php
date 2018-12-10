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

namespace Apigee\Edge\Api\Docstore\Entity;

/**
 * Doc object from the Docstore.
 */
class Doc extends DocstoreEntity
{
    /**
     * Url to fetch the spec file.
     *
     * @var string
     */
    protected $content;
    /**
     * @var string
     */
    protected $kind = 'Doc';

    /**
     * Get the url to fetch the OpenAPI spec content.
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set the url to fetch the OpenAPI spec content.
     *
     * @param $contentUrl
     */
    public function setContent($contentUrl): void
    {
        $this->content = $contentUrl;
    }
}
