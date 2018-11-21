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

use Apigee\Edge\Api\Docstore\Controller\DocstoreController;
use Apigee\Edge\Api\Docstore\Entity\Doc;
use Apigee\Edge\Api\Docstore\Entity\Folder;
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
use Apigee\Edge\Tests\Test\TestClientFactory;
use Http\Message\Formatter\CurlCommandFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

/**
 * Class DocstoreControllerTest.
 */
class DocstoreControllerTest extends EntityControllerTestBase
{
    /**
     * Delete all the generated test data.
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::removeTestData();
    }

    public function testSampleFolderExists(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folder = new Folder(['name' => static::getSampleFolderName()]);
        static::entityController()->create($folder);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $folders = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Folder) {
                $folders[$c->getName()] = $c->id();
            }
        }
        if ($folders[static::getSampleFolderName()]) {
            static::entityController()->delete($folders[static::getSampleFolderName()]);
        }
        $this->assertArrayHasKey(static::getSampleFolderName(), $folders);
    }

    public function testSampleSpecExists(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $spec = new Doc(['name' => static::getSampleDocName()]);
        static::entityController()->create($spec);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $specs = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Doc) {
                $specs[$c->getName()] = $c->id();
            }
        }
        if ($specs[static::getSampleDocName()]) {
            static::entityController()->delete($specs[static::getSampleDocName()]);
        }
        $this->assertArrayHasKey(static::getSampleDocName(), $specs);
    }

    public function testCreateFolder(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->create($folder);
        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Folder) {
                $folderNames[] = $c->getName();
            }
        }
        static::entityController()->delete($folder->id()); //Cleanup the created folder
        $this->assertContains($folderName, $folderNames);
    }

    public function testCreateSpec(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $specName = static::generateNamesForTest();
        $specObj = new Doc(['name' => $specName]);
        static::entityController()->create($specObj);
        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Doc) {
                $specNames[] = $c->getName();
            }
        }
        static::entityController()->delete($specObj->id()); //Cleanup the created folder
        $this->assertContains($specName, $specNames);
    }

    public function testDeleteFolder(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->create($folder);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Folder) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertContains($folderName, $folderNames);

        static::entityController()->delete($folder->id());
        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Folder) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertNotContains($folderName, $folderNames);
    }

    public function testDeleteSpec(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $specName = static::generateNamesForTest();
        $specObj = new Doc(['name' => $specName]);
        static::entityController()->create($specObj);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Doc) {
                $specNames[] = $c->getName();
            }
        }
        $this->assertContains($specName, $specNames);

        static::entityController()->delete($specObj->id());
        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Doc) {
                $specNames[] = $c->getName();
            }
        }
        $this->assertNotContains($specName, $specNames);
    }

    public function testRenameFolder(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->create($folder);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Folder) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertContains($folderName, $folderNames);
        $folderName2 = static::generateNamesForTest();
        $folder->setName($folderName2);
        static::entityController()->update($folder);
        $updated_folder = static::entityController()->load($folder->id());
        static::entityController()->delete($updated_folder->id());
        $this->assertEquals($folderName2, $updated_folder->getName());
    }

    public function testRenameSpec(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $specName = static::generateNamesForTest();
        $specObj = new Doc(['name' => $specName]);
        static::entityController()->create($specObj);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Doc) {
                $specNames[] = $c->getName();
            }
        }
        $this->assertContains($specName, $specNames);
        $specName2 = static::generateNamesForTest();
        $specObj->setName($specName2);
        static::entityController()->update($specObj);
        $updated_spec = static::entityController()->load($specObj->id());
        static::entityController()->delete($updated_spec->id());
        $this->assertEquals($specName2, $updated_spec->getName());
    }

    public function testMoveFolder(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName1 = static::generateNamesForTest();
        $folder1 = new Folder(['name' => $folderName1]);
        static::entityController()->create($folder1);

        $folderName2 = static::generateNamesForTest();
        $folder2 = new Folder(['name' => $folderName2]);
        static::entityController()->create($folder2);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Folder) {
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
            if ($c instanceof Folder) {
                $folderIds[] = $c->id();
            }
        }
        $this->assertContains($folder2->id(), $folderIds);
        //First Delete child folder and then delete parent
        static::entityController()->delete($folder2->id());
        static::entityController()->delete($folder1->id());
    }

    public function testMoveSpecToFolder(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $specName = static::generateNamesForTest();
        $spec = new Doc(['name' => $specName]);
        static::entityController()->create($spec);

        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->create($folder);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $collection = [];
        foreach ($contents_collection->getContents() as $c) {
            $collection[$c->getName()] = $c->getKind();
        }
        $this->assertArrayHasKey($specName, $collection);
        $this->assertEquals('Doc', $collection[$specName]);
        $this->assertArrayHasKey($folderName, $collection);
        $this->assertEquals('Folder', $collection[$folderName]);

        $spec->setFolder($folder->id());
        static::entityController()->update($spec);

        $contents_collection = static::entityController()->getFolderContents($folder);
        $specNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Doc) {
                $specNames[] = $c->id();
            }
        }
        $this->assertContains($spec->id(), $specNames);
        //First Delete child folder and then delete parent
        static::entityController()->delete($spec->id());
        static::entityController()->delete($folder->id());
    }

    public function testGetFolderPath(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName1 = static::generateNamesForTest();
        $folder1 = new Folder(['name' => $folderName1]);
        static::entityController()->create($folder1);

        $folderName2 = static::generateNamesForTest();
        $folder2 = new Folder(['name' => $folderName2]);
        static::entityController()->create($folder2);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Folder) {
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
            if ($c instanceof Folder) {
                $folderIds[] = $c->id();
            }
        }
        $this->assertContains($folder2->id(), $folderIds);

        $path = implode('/', [$folder1->getName(), $folder2->getName()]);
        $this->assertEquals($path, static::entityController()->getPath($folder2));
        //First Delete child folder and then delete parent
        static::entityController()->delete($folder2->id());
        static::entityController()->delete($folder1->id());
    }

    public function testGetSpecPath(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->create($folder);

        $specName = static::generateNamesForTest();
        $spec = new Doc(['name' => $specName]);
        static::entityController()->create($spec);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $collection = [];
        foreach ($contents_collection->getContents() as $c) {
            $collection[] = $c->getName();
        }
        $this->assertContains($folderName, $collection);
        $this->assertContains($specName, $collection);

        $spec->setFolder($folder->id());
        static::entityController()->update($spec);

        $contents_collection = static::entityController()->getFolderContents($folder);
        $folderIds = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Doc) {
                $folderIds[] = $c->id();
            }
        }
        $this->assertContains($spec->id(), $folderIds);

        $path = implode('/', [$folder->getName(), $spec->getName()]);
        $this->assertEquals($path, static::entityController()->getPath($spec));
        //First Delete child folder and then delete parent
        static::entityController()->delete($spec->id());
        static::entityController()->delete($folder->id());
    }

    public function testLoadFolderByPath(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName1 = static::generateNamesForTest();
        $folder1 = new Folder(['name' => $folderName1]);
        static::entityController()->create($folder1);

        $folderName2 = static::generateNamesForTest();
        $folder2 = new Folder(['name' => $folderName2]);
        static::entityController()->create($folder2);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Folder) {
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
            if ($c instanceof Folder) {
                $folderIds[] = $c->id();
            }
        }
        $this->assertContains($folder2->id(), $folderIds);

        $path = implode('/', [$folder1->getName(), $folder2->getName()]);
        $folder_loaded_by_path = static::entityController()->loadByPath($path);

        $this->assertEquals($folder_loaded_by_path->id(), $folder2->id());
        //First Delete child folder and then delete parent
        static::entityController()->delete($folder2->id());
        static::entityController()->delete($folder1->id());
    }

    public function testLoadSpecByPath(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->create($folder);

        $specName = static::generateNamesForTest();
        $spec = new Doc(['name' => $specName]);
        static::entityController()->create($spec);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $collection = [];
        foreach ($contents_collection->getContents() as $c) {
            $collection[] = $c->getName();
        }
        $this->assertContains($folderName, $collection);
        $this->assertContains($specName, $collection);

        $spec->setFolder($folder->id());
        static::entityController()->update($spec);

        $contents_collection = static::entityController()->getFolderContents($folder);
        $folderIds = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Doc) {
                $folderIds[] = $c->id();
            }
        }
        $this->assertContains($spec->id(), $folderIds);

        $path = implode('/', [$folder->getName(), $spec->getName()]);
        $folder_loaded_by_path = static::entityController()->loadByPath($path);

        $this->assertEquals($folder_loaded_by_path->id(), $spec->id());
        //First Delete child folder and then delete parent
        static::entityController()->delete($spec->id());
        static::entityController()->delete($folder->id());
    }

    public function testSpecFileContentMatch(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $docName = static::generateNamesForTest();
        $spec = new Doc(['name' => $docName]);
        static::entityController()->create($spec);
        $file_contents = file_get_contents(__DIR__ . '/../testdata/petstore.swagger.json');
        static::entityController()->uploadJsonSpec($spec, $file_contents);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contents_collection = static::entityController()->getFolderContents($homeFolder);
        $specs = [];
        foreach ($contents_collection->getContents() as $c) {
            if ($c instanceof Doc) {
                $specs[$c->getName()] = $c;
            }
        }
        $this->assertArrayHasKey($docName, $specs);
        $file_from_specstore = static::entityController()->getSpecContents($specs[$docName]);
        $obj1 = json_decode($file_contents);
        $obj2 = json_decode($file_from_specstore);
        //Match title/description and number of Path entries
        $this->assertEquals($obj1->info->title, $obj2->info->title);
        $this->assertEquals($obj1->info->description, $obj2->info->description);
        $this->assertEquals(sizeof($obj1->paths), sizeof($obj2->paths));

        if ($specs[static::getSampleDocName()]) {
            static::entityController()->delete($specs[$docName]->id());
        }
    }

    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new DocstoreController(static::defaultTestOrganization($client), $client));
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
            $endpoint = 'https://apigee.com';
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

    private static function getSampleFolderName()
    {
        return static::generateNamesForTest('SampleFolder');
    }

    private static function getSampleDocName()
    {
        return static::generateNamesForTest('SampleDoc');
    }

    private static function generateNamesForTest($name = null)
    {
        return 'PHP-SDK-TEST-FolderControllerTest-' . ($name ?? static::randomGenerator()->number(1, 1000000));
    }

    private static function removeTestData(): void
    {
        if (!TestClientFactory::isOfflineClient(static::defaultAPIClient())) {
            $homeFolder = static::entityController()->load('/homeFolder');
            $contents_collection = static::entityController()->getFolderContents($homeFolder);
            $test_data_prefix = static::generateNamesForTest('');
            foreach ($contents_collection->getContents() as $c) {
                if (0 === strpos($c->getName(), $test_data_prefix)) {
                    static::entityController()->delete($c->id());
                }
            }
        }
    }
}
