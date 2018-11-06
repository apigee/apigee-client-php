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
    static $folderController;
    static $specController;
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        $username = 'some user@google.com';
        $password = 'password of user';
        $organization = 'orgname';

        $auth = new Oauth($username, $password, new InMemoryOauthTokenStorage());

        $client = new Client($auth, 'https://apigee.com');

        static::$folderController = new FolderController($organization, $client);
        static::$specController = new SpecController($organization, $client);

        parent::setUpBeforeClass();
    }

    public function testSpecstoreLoadHomeFolder(): void
    {


        $expected = 'Hello World';
        $data = 'Hello World';
        $folderController = static:: $folderController;
        $specController = static:: $specController;

        $homeFolder = $folderController->load('/homeFolder');
        //var_dump($homeFolder);
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
//$contents_collection = $folderController->getFolderContents($homeFolder);
//        foreach ($contents_collection->getContents() as $obj) {
//            if ($obj instanceof Spec) {
//                var_dump($specController->getSpecContents($obj));
//            }
//        }

//        var_dump($entity);
//        exit;
        $folderObj = $specController->load("/c3Rvc-Zmxk-117786");
        var_dump($folderController->getPath($folderObj));
        var_dump($folderController->loadByPath("abcd/gitesh/spec1"));
        $this->assertEquals($expected, $data);
    }

    public function testSpecstoreUploadTestSpecToHomeFolder()
    {

    }

    public function testSpecstoreDeleteTestSpecToHomeFolder()
    {

    }

    public function testSpecstoreVerifyCreateFolder()
    {

    }

    public function testSpecstoreVerifyRemoveFolder()
    {

    }

    public function testSpecstoreVerifyRecursiveCreateFolder()
    {

    }
}
