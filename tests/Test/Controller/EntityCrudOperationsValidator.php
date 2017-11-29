<?php

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\Entity\EntityCrudOperationsInterface;
use Apigee\Edge\Entity\EntityFactory;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class EntityCrudOperationsValidator.
 *
 * Helps in validation of all entity controllers that implements EntityCrudOperationsInterface.
 *
 * @package Apigee\Edge\Tests\Test\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see EntityCrudOperationsInterface
 */
abstract class EntityCrudOperationsValidator extends TestCase
{
    /** @var \Apigee\Edge\HttpClient\ClientInterface */
    protected static $client;

    /** @var \Apigee\Edge\Entity\EntityFactoryInterface */
    protected static $entityFactory;

    /** @var ObjectNormalizer */
    protected static $objectNormalizer;

    /** @var EntityInterface[] */
    protected static $createdEntities = [];

    /** @var string */
    protected static $onlyOnlineClientSkipMessage = 'Test can be executed only with real Apigee Edge connection.';

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::$entityFactory = new EntityFactory();
        self::$client = (new TestClientFactory())->getClient();
        self::$objectNormalizer = new ObjectNormalizer();
        self::$objectNormalizer->setSerializer(new Serializer());
        parent::setUpBeforeClass();
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass()
    {
        if (strpos(self::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === 0) {
            return;
        }

        // Remove created entities on Apigee Edge.
        try {
            foreach (self::$createdEntities as $entity) {
                static::getEntityController()->delete($entity->id());
                unset(static::$createdEntities[$entity->id()]);
            }
        } catch (Exception $e) {
            printf("Unable to delete %s entity with %s id.\n", strtolower(get_class($entity)), $entity->id());
        }
    }

    /**
     * Returns the entity controller that is tested.
     *
     * It is recommended to use static cache on the controller instance, however it should not be added as an
     * attribute of a test class because it can be misleading later whether the self::$controller should be called in
     * a test method or this getter.
     *
     * @return \Apigee\Edge\Entity\EntityCrudOperationsInterface
     */
    abstract protected static function getEntityController(): EntityCrudOperationsInterface;

    /**
     * Returns test data that can be used to test creation of entity.
     *
     * @return EntityInterface
     */
    abstract protected function sampleDataForEntityCreate(): EntityInterface;

    /**
     * Returns test data that can be used to test entity update.
     *
     * Data should be the altered version of the returned data by sampleDataForEntityCreate.
     *
     * @return EntityInterface
     */
    abstract protected function sampleDataForEntityUpdate(): EntityInterface;

    /**
     * Returns the expected values of an entity after it has been created.
     *
     * @return EntityInterface
     */
    protected function expectedAfterEntityCreate(): EntityInterface
    {
        return $this->sampleDataForEntityCreate();
    }

    /**
     * @return string
     */
    public function testCreate()
    {
        // Data providers could not be used instead of directly calling this function, because this function would
        // require two input arguments: the entity values for creation and the expected values after entity has been
        // created. If we would use more than on data provider on this function then it would get the merged result
        // of providers as a _single_ value.
        /** @var EntityInterface $entity */
        $entity = $this->sampleDataForEntityCreate();
        $entity = $this->getEntityController()->create($entity);
        self::$createdEntities[$entity->id()] = $entity;
        // Validate properties that values are either auto generated or we do not know in the current context.
        $this->assertEntityHasAllPropertiesSet($entity);
        $this->assertArraySubset(
            array_filter(self::$objectNormalizer->normalize($this->expectedAfterEntityCreate())),
            self::$objectNormalizer->normalize($entity)
        );
        return $entity->id();
    }

    /**
     * @depends testCreate
     *
     * @param string $entityId
     *
     * @return string
     */
    public function testLoad(string $entityId)
    {
        $entity = $this->getEntityController()->load($entityId);
        // Validate properties that values are either auto generated or we do not know in the current context.
        $this->assertEntityHasAllPropertiesSet($entity);
        $this->assertArraySubset(
            array_filter(self::$objectNormalizer->normalize($this->expectedAfterEntityCreate())),
            self::$objectNormalizer->normalize($entity)
        );
        return $entityId;
    }

    /**
     * Get the entityID from the testLoad() instead of testCreate() because load should pass before update.
     *
     * @depends testLoad
     *
     * @param string $entityId
     *
     * @return string
     */
    public function testUpdate(string $entityId)
    {
        /** @var EntityInterface $entity */
        $entity = $this->sampleDataForEntityUpdate();
        call_user_func([$entity, 'set' . ucfirst($this->sampleDataForEntityUpdate()->idProperty())], $entityId);
        $entity = $this->getEntityController()->update($entity);
        // Validate properties that values are either auto generated or we do not know in the current context.
        $this->assertEntityHasAllPropertiesSet($entity);
        $entityAsArray = self::$objectNormalizer->normalize($entity);
        $changesAsArray = array_filter(self::$objectNormalizer->normalize($this->sampleDataForEntityUpdate()));
        $expectedToRemainTheSame = array_diff_key($entityAsArray, $changesAsArray);
        // Of course, this property's value will change.
        if (isset($expectedToRemainTheSame['lastModifiedAt'])) {
            unset($expectedToRemainTheSame['lastModifiedAt']);
        }
        $this->assertArraySubset(
            $changesAsArray,
            $entityAsArray
        );
        $this->assertArraySubset(
            $expectedToRemainTheSame,
            $entityAsArray
        );
        return $entityId;
    }

    /**
     * @param \Apigee\Edge\Entity\EntityInterface $entity
     */
    protected function assertEntityHasAllPropertiesSet(EntityInterface $entity): void
    {
        $ro = new \ReflectionClass(get_class($entity));
        foreach ($ro->getProperties() as $property) {
            $getter = 'get' . ucfirst($property->getName());
            if ($ro->hasMethod($getter)) {
                $this->assertObjectHasAttribute($property->getName(), $entity);
            }
        }
    }
}
