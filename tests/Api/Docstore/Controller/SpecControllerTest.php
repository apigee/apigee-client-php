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

namespace Apigee\Edge\Tests\Api\Docstore\Controller;

use Apigee\Edge\Api\Docstore\Controller\SpecController;
use Apigee\Edge\Api\Docstore\Entity\Spec;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\HttpClient\Plugin\Authentication\Oauth;
use Apigee\Edge\Tests\Api\Management\Controller\EntityControllerTestBase;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\DebuggerClient;
use Apigee\Edge\Tests\Test\FileSystemMockClient;
use Apigee\Edge\Tests\Test\HttpClient\DebuggerHttpClient;
use Apigee\Edge\Tests\Test\HttpClient\Plugin\InMemoryOauthTokenStorage;
use Apigee\Edge\Tests\Test\OnlineClientInterface;
use Http\Message\Formatter\CurlCommandFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;


/**
 * Class FolderController
 * @package Apigee\Edge\Tests\Api\Docstore\Controller
 */
class SpecControllerTest extends EntityControllerTestBase
{
    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $spec = new Spec(['name' => static::getSampleFolderName()]);
        static::entityController()->create($spec);
        static::entityController()->uploadJsonSpec($spec, file_get_contents(__DIR__ . "../petstore.swagger.json"));
    }

    private static function getSampleFolderName()
    {
        return static::generateFolderName("SampleFolder");
    }

    private static function generateFolderName($name = null)
    {
        return "PHP-SDK-TEST-SpecControllerTest-" . ($name ?? static::randomGenerator()->number(1, 1000000));
    }

    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new SpecController(static::defaultTestOrganization($client), $client));

    }

    protected static function defaultAPIClient(): ClientInterface
    {
        $fqcn = getenv('APIGEE_EDGE_PHP_CLIENT_API_CLIENT') ?: FileSystemMockClient::class;

        try {
            $clientRC = new \ReflectionClass($fqcn);
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException("Unable to initialize client class with {$fqcn} name.", $e->getCode(), $e);
        }

        if ($clientRC->implementsInterface(OnlineClientInterface::class)) {
            $options = [];
            $endpoint = "https://apigee.com";
            //;
            // getenv('APIGEE_EDGE_PHP_CLIENT_ENDPOINT') ?: null;
            $username = getenv('APIGEE_EDGE_PHP_CLIENT_BASIC_AUTH_USER') ?: '';
            $password = getenv('APIGEE_EDGE_PHP_CLIENT_BASIC_AUTH_PASSWORD') ?: '';
            $httpClientFqcn = getenv('APIGEE_EDGE_PHP_CLIENT_HTTP_CLIENT');

            if ($httpClientFqcn) {
                $httpClientRC = new \ReflectionClass($httpClientFqcn);
                $options[OnlineClientInterface::CONFIG_HTTP_CLIENT] = $httpClientRC->newInstance();
            }

            if (DebuggerClient::class == $clientRC->getName()) {
                $logHandler = new StreamHandler(__DIR__ . '/../../debug.log');
                // Only log the message.
                $logHandler->setFormatter(new LineFormatter('%message%', null, true));
                $logger = new Logger('debuggerClient', [$logHandler], [new PsrLogMessageProcessor()]);
                $formatter = new CurlCommandFormatter();
                $logFormat = "{request_formatted}\nStats: {time_stats}\n\n";
                $options[OnlineClientInterface::CONFIG_HTTP_CLIENT] = new DebuggerHttpClient([], $formatter, $logger, $logFormat);
            }

            /* @var \Apigee\Edge\Tests\Test\OnlineClientInterface $client */
            return $clientRC->newInstance(new Oauth($username, $password, new InMemoryOauthTokenStorage()), $endpoint, $options);
        } else {
            return parent::defaultAPIClient();
        }
    }

    /**
     * Delete all the generated folders
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::removeTestData();
    }

    private static function removeTestData()
    {
        $homeFolder = static::entityController()->load("/homeFolder");
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec && strpos($c->getName(), static::generateFolderName("")) === 0) {
                static::entityController()->delete($c->id());
            }
            if ($c instanceof Spec && strpos($c->getName(), static::generateSpecName("")) === 0) {
                static::entityController()->delete($c->id());
            }
        }
    }

    private static function generateSpecName($name = null)
    {
        return "PHP-SDK-TEST-SpecControllerTest-" . ($name ?? static::randomGenerator()->number(1, 1000000));
    }

    private static function getSampleSpecName()
    {
        return static::generateFolderName("SampleFolder");
    }

    public function testSampleSpecExists()
    {
        $homeFolder = static::entityController()->load("/homeFolder");
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $specs = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec) {
                $specs[$c->getName()] = $c->id();
            }
        }
        if ($specs[static::getSampleFolderName()]) {
            static::entityController()->delete($specs[static::getSampleFolderName()]);
        }
        $this->assertArrayHasKey(static::getSampleFolderName(), $specs);
    }

    public function testCreateSpec()
    {
        $specName = static::generateSpecName();
        $specObj = new Spec(['name' => $specName]);
        static::entityController()->create($specObj);
        $homeFolder = static::entityController()->load("/homeFolder");
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec) {
                $specNames[] = $c->getName();
            }
        }
        static::entityController()->delete($specObj->id()); //Cleanup the created folder
        $this->assertContains($specName, $specNames);
    }

    public function testDeleteSpec()
    {
        $specName = static::generateSpecName();
        $specObj = new Spec(['name' => $specName]);
        static::entityController()->create($specObj);

        $homeFolder = static::entityController()->load("/homeFolder");
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec) {
                $specNames[] = $c->getName();
            }
        }
        $this->assertContains($specName, $specNames);

        static::entityController()->delete($specObj->id());
        $homeFolder = static::entityController()->load("/homeFolder");
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec) {
                $specNames[] = $c->getName();
            }
        }
        $this->assertNotContains($specName, $specNames);
    }

    public function testRenameSpec()
    {
        $specName = static::generateSpecName();
        $specObj = new Spec(['name' => $specName]);
        static::entityController()->create($specObj);


        $homeFolder = static::entityController()->load("/homeFolder");
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec) {
                $specNames[] = $c->getName();
            }
        }
        $this->assertContains($specName, $specNames);
        $specName2 = static::generateSpecName();
        $specObj->setName($specName2);
        static::entityController()->update($specObj);
        $updated_spec = static::entityController()->load($specObj->id());
        static::entityController()->delete($updated_spec->id());
        $this->assertEquals($specName2, $updated_spec->getName());
    }

    public function testMoveSpecToFolder()
    {
        //TODO: Fix this test
        $folderName1 = static::generateFolderName();
        $folder1 = new Spec(['name' => $folderName1]);
        static::entityController()->create($folder1);

        $folderName2 = static::generateFolderName();
        $folder2 = new Spec(['name' => $folderName2]);
        static::entityController()->create($folder2);

        $homeFolder = static::entityController()->load("/homeFolder");
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertContains($folderName1, $folderNames);
        $this->assertContains($folderName2, $folderNames);

        $folder2->setFolder($folder1->id());
        static::entityController()->update($folder2);

        $contents_collection = static::entityController()->getFolderContents($folder1);
        $folderIds = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec) {
                $folderIds[] = $c->id();
            }
        }
        $this->assertContains($folder2->id(), $folderIds);
        //First Delete child folder and then delete parent
        static::entityController()->delete($folder2->id());
        static::entityController()->delete($folder1->id());
    }

    public function testGetSpecPath()
    {
        //TODO: Fix this test
        $folderName1 = static::generateFolderName();
        $folder1 = new Spec(['name' => $folderName1]);
        static::entityController()->create($folder1);

        $folderName2 = static::generateFolderName();
        $folder2 = new Spec(['name' => $folderName2]);
        static::entityController()->create($folder2);

        $homeFolder = static::entityController()->load("/homeFolder");
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertContains($folderName1, $folderNames);
        $this->assertContains($folderName2, $folderNames);

        $folder2->setFolder($folder1->id());
        static::entityController()->update($folder2);

        $contents_collection = static::entityController()->getFolderContents($folder1);
        $folderIds = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec) {
                $folderIds[] = $c->id();
            }
        }
        $this->assertContains($folder2->id(), $folderIds);

        $path = implode("/", [$folder1->getName(), $folder2->getName()]);
        $this->assertEquals($path, static::entityController()->getPath($folder2));
        //First Delete child folder and then delete parent
        static::entityController()->delete($folder2->id());
        static::entityController()->delete($folder1->id());
    }

    public function testLoadSpecByPath()
    {
        //TODO: Fix this test
        $folderName1 = static::generateFolderName();
        $folder1 = new Spec(['name' => $folderName1]);
        static::entityController()->create($folder1);

        $folderName2 = static::generateFolderName();
        $folder2 = new Spec(['name' => $folderName2]);
        static::entityController()->create($folder2);

        $homeFolder = static::entityController()->load("/homeFolder");
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertContains($folderName1, $folderNames);
        $this->assertContains($folderName2, $folderNames);

        $folder2->setFolder($folder1->id());
        static::entityController()->update($folder2);

        $contents_collection = static::entityController()->getFolderContents($folder1);
        $folderIds = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Spec) {
                $folderIds[] = $c->id();
            }
        }
        $this->assertContains($folder2->id(), $folderIds);

        $path = implode("/", [$folder1->getName(), $folder2->getName()]);
        $folder_loaded_by_path = static::entityController()->loadByPath($path);

        $this->assertEquals($folder_loaded_by_path->id(), $folder2->id());
        //First Delete child folder and then delete parent
        static::entityController()->delete($folder2->id());
        static::entityController()->delete($folder1->id());
    }
}
