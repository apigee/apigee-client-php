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

namespace Apigee\Edge\Tests\Api\Specstore;

use Apigee\Edge\Api\Specstore\Controller\FolderController;
use Apigee\Edge\Api\Specstore\Controller\SpecController;
use Apigee\Edge\Api\Specstore\Entity\Spec;
use Apigee\Edge\Client;
use Apigee\Edge\HttpClient\Plugin\Authentication\Oauth;
use Apigee\Edge\Tests\Test\HttpClient\Plugin\InMemoryOauthTokenStorage;
use PHPUnit\Framework\TestCase;

/**
 * Class StatsQueryNormalizerTest.
 *
 * @group normalizer
 * @group query
 * @group offline
 * @small
 */
class SpecstoreTest extends TestCase
{
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public function testSpecStore(): void
    {
        $username = 'some-test-account@google.com';
        $password = 'passw0rd';
        $organization = 'sample-org';

        $auth = new Oauth($username, $password, new InMemoryOauthTokenStorage());

        $client = new Client($auth, 'https://apigee.com');

        $expected = 'Hello World';
        $data = 'Hello World';

        $folderController = new FolderController($organization, $client);
        $specController = new SpecController($organization, $client);
        $homeFolder = $folderController->load('/homeFolder');

//        $spec = new Spec();
//        $spec->setName("petstoreSpec". time());
//        $spec->setFolder($homeFolder->id());
        ////

//        $specController->create($spec);
//        $specController->uploadSpec($spec, file_get_contents(dirname(__FILE__)  . "/petstore.swagger.json"));
//
//        $folder = new Folder();
//        $folder->setName("abcd-" . time());
//        $folder->setFolder($homeFolder->id());
//        $folderController->create($folder);

        foreach ($folderController->getFolderContents($homeFolder) as $obj) {
//            var_dump($obj);
            if ($obj instanceof Spec) {
                var_dump($specController->getSpecContents($obj));
            }
//            if($obj instanceof Folder){
//                $folderController->delete($obj->id());
//            }
        }

//        var_dump($entity);
//        exit;
        $this->assertEquals($expected, $data);
    }
}
