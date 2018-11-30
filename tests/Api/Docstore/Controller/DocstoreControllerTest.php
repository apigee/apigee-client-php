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
use Apigee\Edge\Api\Docstore\Controller\DocstoreControllerInterface;
use Apigee\Edge\Api\Docstore\Entity\Doc;
use Apigee\Edge\Api\Docstore\Entity\Folder;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\HttpClient\Plugin\Authentication\Oauth;
use Apigee\Edge\Tests\Api\Management\Controller\EntityControllerTestBase;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\DebuggerClient;
use Apigee\Edge\Tests\Test\FileSystemMockClient;
use Apigee\Edge\Tests\Test\HttpClient\DebuggerHttpClient;
use Apigee\Edge\Tests\Test\HttpClient\Plugin\InMemoryOauthTokenStorage;
use Apigee\Edge\Tests\Test\OnlineClientInterface;
use Apigee\Edge\Tests\Test\TestClientFactory;
use Apigee\Edge\Tests\Test\Utility\MarkOnlineTestSkippedAwareTrait;
use Http\Message\Formatter\CurlCommandFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

/**
 * Class DocstoreControllerTest.
 *
 * @group controller
 * @group management
 */
class DocstoreControllerTest extends EntityControllerTestBase
{
    use MarkOnlineTestSkippedAwareTrait;
    use DefaultAPIClientAwareTrait;

    /**
     * Delete all the generated test data.
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::removeTestData();
    }

    /**
     * Test to create a sample folder and verify it exists.
     */
    public function testSampleFolderExists(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folder = new Folder(['name' => static::getSampleFolderName()]);
        static::entityController()->createFolder($folder);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $folders = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Folder) {
                $folders[$c->getName()] = $c->id();
            }
        }
        if ($folders[static::getSampleFolderName()]) {
            static::entityController()->delete($folders[static::getSampleFolderName()]);
        }
        $this->assertArrayHasKey(static::getSampleFolderName(), $folders);
    }

    /**
     * Test to create a Sample doc and verify it was created.
     */
    public function testSampleSpecExists(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $spec = new Doc(['name' => static::getSampleDocName()]);
        static::entityController()->createDoc($spec);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $specs = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Doc) {
                $specs[$c->getName()] = $c->id();
            }
        }
        if ($specs[static::getSampleDocName()]) {
            static::entityController()->delete($specs[static::getSampleDocName()]);
        }
        $this->assertArrayHasKey(static::getSampleDocName(), $specs);
    }

    /**
     * Test create Folder.
     */
    public function testCreateFolder(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->createFolder($folder);
        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Folder) {
                $folderNames[] = $c->getName();
            }
        }
        static::entityController()->delete($folder->id()); //Cleanup the created folder
        $this->assertContains($folderName, $folderNames);
    }

    /**
     * Test DocstoreDateDenormalizer.
     */
    public function testDateCorrectlyParsed(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->createFolder($folder);
        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        /** @var $folders Folder[] */
        $folders = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Folder) {
                $folders[$c->getName()] = $c;
            }
        }
        static::entityController()->delete($folder->id()); //Cleanup the created folder
        $this->assertArrayHasKey($folderName, $folders);
        //Test folder was created right today
        $dateFormat = 'Y-m-d';
        $this->assertEquals(date($dateFormat), $folders[$folderName]->getCreated()->format($dateFormat));
    }

    /**
     * Test create spec.
     */
    public function testCreateSpec(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $specName = static::generateNamesForTest();
        $specObj = new Doc(['name' => $specName]);
        static::entityController()->createDoc($specObj);
        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Doc) {
                $specNames[] = $c->getName();
            }
        }
        static::entityController()->delete($specObj->id()); //Cleanup the created folder
        $this->assertContains($specName, $specNames);
    }

    /**
     * Test delete folder.
     */
    public function testDeleteFolder(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->createFolder($folder);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Folder) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertContains($folderName, $folderNames);

        static::entityController()->delete($folder->id());
        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Folder) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertNotContains($folderName, $folderNames);
    }

    /**
     * Test delete spec.
     */
    public function testDeleteSpec(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $specName = static::generateNamesForTest();
        $specObj = new Doc(['name' => $specName]);
        static::entityController()->createDoc($specObj);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Doc) {
                $specNames[] = $c->getName();
            }
        }
        $this->assertContains($specName, $specNames);

        static::entityController()->delete($specObj->id());
        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Doc) {
                $specNames[] = $c->getName();
            }
        }
        $this->assertNotContains($specName, $specNames);
    }

    /**
     * Test rename folder.
     */
    public function testRenameFolder(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->createFolder($folder);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Folder) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertContains($folderName, $folderNames);
        $folderName2 = static::generateNamesForTest();
        $folder->setName($folderName2);
        static::entityController()->update($folder);
        $updatedFolder = static::entityController()->load($folder->id());
        static::entityController()->delete($updatedFolder->id());
        $this->assertEquals($folderName2, $updatedFolder->getName());
    }

    /**
     * Test rename spec.
     */
    public function testRenameSpec(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $specName = static::generateNamesForTest();
        $specObj = new Doc(['name' => $specName]);
        static::entityController()->createDoc($specObj);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $specNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Doc) {
                $specNames[] = $c->getName();
            }
        }
        $this->assertContains($specName, $specNames);
        $specName2 = static::generateNamesForTest();
        $specObj->setName($specName2);
        static::entityController()->update($specObj);
        $updatedSpec = static::entityController()->load($specObj->id());
        static::entityController()->delete($updatedSpec->id());
        $this->assertEquals($specName2, $updatedSpec->getName());
    }

    /**
     * Test Move folder.
     */
    public function testMoveFolder(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName1 = static::generateNamesForTest();
        $folder1 = new Folder(['name' => $folderName1]);
        static::entityController()->createFolder($folder1);

        $folderName2 = static::generateNamesForTest();
        $folder2 = new Folder(['name' => $folderName2]);
        static::entityController()->createFolder($folder2);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Folder) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertContains($folderName1, $folderNames);
        $this->assertContains($folderName2, $folderNames);

        $folder2->setFolder($folder1->id());
        static::entityController()->update($folder2);

        $contentsCollection = static::entityController()->getFolderContents($folder1);
        $folderIds = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Folder) {
                $folderIds[] = $c->id();
            }
        }
        $this->assertContains($folder2->id(), $folderIds);
        //First Delete child folder and then delete parent
        static::entityController()->delete($folder2->id());
        static::entityController()->delete($folder1->id());
    }

    /**
     * Test move spec to a folder.
     */
    public function testMoveSpecToFolder(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $specName = static::generateNamesForTest();
        $spec = new Doc(['name' => $specName]);
        static::entityController()->createDoc($spec);

        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->createFolder($folder);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $collection = [];
        foreach ($contentsCollection as $c) {
            $collection[$c->getName()] = $c->getKind();
        }
        $this->assertArrayHasKey($specName, $collection);
        $this->assertEquals('Doc', $collection[$specName]);
        $this->assertArrayHasKey($folderName, $collection);
        $this->assertEquals('Folder', $collection[$folderName]);

        $spec->setFolder($folder->id());
        static::entityController()->update($spec);

        $contentsCollection = static::entityController()->getFolderContents($folder);
        $specNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Doc) {
                $specNames[] = $c->id();
            }
        }
        $this->assertContains($spec->id(), $specNames);
        //First Delete child folder and then delete parent
        static::entityController()->delete($spec->id());
        static::entityController()->delete($folder->id());
    }

    /**
     * Test to ensure the correct path is returned for a given Folder.
     */
    public function testGetFolderPath(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName1 = static::generateNamesForTest();
        $folder1 = new Folder(['name' => $folderName1]);
        static::entityController()->createFolder($folder1);

        $folderName2 = static::generateNamesForTest();
        $folder2 = new Folder(['name' => $folderName2]);
        static::entityController()->createFolder($folder2);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Folder) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertContains($folderName1, $folderNames);
        $this->assertContains($folderName2, $folderNames);

        $folder2->setFolder($folder1->id());
        static::entityController()->update($folder2);

        $contentsCollection = static::entityController()->getFolderContents($folder1);
        $folderIds = [];
        foreach ($contentsCollection as $c) {
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

    /**
     * Test to ensure the correct path is returned for a given Spec.
     */
    public function testGetSpecPath(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->createFolder($folder);

        $specName = static::generateNamesForTest();
        $spec = new Doc(['name' => $specName]);
        static::entityController()->createDoc($spec);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $collection = [];
        foreach ($contentsCollection as $c) {
            $collection[] = $c->getName();
        }
        $this->assertContains($folderName, $collection);
        $this->assertContains($specName, $collection);

        $spec->setFolder($folder->id());
        static::entityController()->update($spec);

        $contentsCollection = static::entityController()->getFolderContents($folder);
        $folderIds = [];
        foreach ($contentsCollection as $c) {
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

    /**
     * Test to ensure the correct folder is loaded from a given path.
     */
    public function testLoadFolderByPath(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName1 = static::generateNamesForTest();
        $folder1 = new Folder(['name' => $folderName1]);
        static::entityController()->createFolder($folder1);

        $folderName2 = static::generateNamesForTest();
        $folder2 = new Folder(['name' => $folderName2]);
        static::entityController()->createFolder($folder2);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $folderNames = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Folder) {
                $folderNames[] = $c->getName();
            }
        }
        $this->assertContains($folderName1, $folderNames);
        $this->assertContains($folderName2, $folderNames);

        $folder2->setFolder($folder1->id());
        static::entityController()->update($folder2);

        $contentsCollection = static::entityController()->getFolderContents($folder1);
        $folderIds = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Folder) {
                $folderIds[] = $c->id();
            }
        }
        $this->assertContains($folder2->id(), $folderIds);

        $path = implode('/', [$folder1->getName(), $folder2->getName()]);
        $folderLoadedByPath = static::entityController()->loadByPath($path);

        $this->assertEquals($folderLoadedByPath->id(), $folder2->id());
        //First Delete child folder and then delete parent
        static::entityController()->delete($folder2->id());
        static::entityController()->delete($folder1->id());
    }

    /**
     * Test to ensure the correct spec is loaded from a given path.
     */
    public function testLoadSpecByPath(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $folderName = static::generateNamesForTest();
        $folder = new Folder(['name' => $folderName]);
        static::entityController()->createFolder($folder);

        $specName = static::generateNamesForTest();
        $spec = new Doc(['name' => $specName]);
        static::entityController()->createDoc($spec);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $collection = [];
        foreach ($contentsCollection as $c) {
            $collection[] = $c->getName();
        }
        $this->assertContains($folderName, $collection);
        $this->assertContains($specName, $collection);

        $spec->setFolder($folder->id());
        static::entityController()->update($spec);

        $contentsCollection = static::entityController()->getFolderContents($folder);
        $folderIds = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Doc) {
                $folderIds[] = $c->id();
            }
        }
        $this->assertContains($spec->id(), $folderIds);

        $path = implode('/', [$folder->getName(), $spec->getName()]);
        $folderLoadedByPath = static::entityController()->loadByPath($path);

        $this->assertEquals($folderLoadedByPath->id(), $spec->id());
        //First Delete child folder and then delete parent
        static::entityController()->delete($spec->id());
        static::entityController()->delete($folder->id());
    }

    /**
     * Test to ensure the contents of the uploaded OAS documents are same.
     */
    public function testSpecFileContentMatch(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $docName = static::generateNamesForTest();
        $spec = new Doc(['name' => $docName]);
        static::entityController()->createDoc($spec);
        $fileContents = file_get_contents(__DIR__ . '/../testdata/petstore.swagger.json');
        static::entityController()->uploadJsonSpec($spec, $fileContents);

        $homeFolder = static::entityController()->load('/homeFolder');
        $contentsCollection = static::entityController()->getFolderContents($homeFolder);
        $specs = [];
        foreach ($contentsCollection as $c) {
            if ($c instanceof Doc) {
                $specs[$c->getName()] = $c;
            }
        }
        $this->assertArrayHasKey($docName, $specs);
        $fileFromSpecstore = static::entityController()->getSpecContentsAsJson($specs[$docName]);
        $obj1 = json_decode($fileContents);
        $obj2 = json_decode($fileFromSpecstore);
        //Match title/description and number of Path entries
        $this->assertEquals($obj1->info->title, $obj2->info->title);
        $this->assertEquals($obj1->info->description, $obj2->info->description);
        $this->assertEquals(sizeof($obj1->paths), sizeof($obj2->paths));

        if ($specs[$docName]) {
            static::entityController()->delete($specs[$docName]->id());
        }
    }

    /**
     * @param ClientInterface|null $client
     *
     * @return EntityControllerTesterInterface
     */
    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        static $controller;
        if (!$controller) {
            $client = $client ?? static::defaultAPIClient();

            $controller = new EntityControllerTester(new DocstoreController(static::defaultTestOrganization($client), $client));
        }

        return $controller;
    }

    /**
     * @throws \ReflectionException
     *
     * @return ClientInterface
     */
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
            //$endpoint =  getenv('APIGEE_EDGE_PHP_CLIENT_ENDPOINT') ?: null;
            $endpoint = 'https://apigee.com';
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
     * @return string
     */
    private static function getSampleFolderName()
    {
        return static::generateNamesForTest('SampleFolder');
    }

    /**
     * @return string
     */
    private static function getSampleDocName()
    {
        return static::generateNamesForTest('SampleDoc');
    }

    /**
     * Generate sample name for running tests.
     *
     * @param null $name
     *
     * @return string
     */
    private static function generateNamesForTest($name = null)
    {
        return 'PHP-SDK-TEST-FolderControllerTest-' . ($name ?? static::randomGenerator()->number(1, 1000000));
    }

    /**
     * Cleanup test data.
     *
     * @throws \ReflectionException
     */
    private static function removeTestData($folderId = '/homeFolder'): void
    {
        if (!TestClientFactory::isOfflineClient(static::defaultAPIClient())) {
            /* @var $controller DocstoreControllerInterface */
            $controller = static::entityController();
            $homeFolder = $controller->load($folderId);
            $contentsCollection = $controller->getFolderContents($homeFolder);
            $testDataPrefix = static::generateNamesForTest('');
            foreach ($contentsCollection as $c) {
                if (0 === strpos($c->getName(), $testDataPrefix)) {
                    if ($c instanceof Folder && !empty($controller->getFolderContents($c))) {
                        static::removeTestData($c->id());
                    }
                    $controller->delete($c->id());
                }
            }
        }
    }
}
