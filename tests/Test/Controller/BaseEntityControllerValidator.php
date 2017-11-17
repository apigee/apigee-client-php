<?php

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\Entity\BaseEntityControllerInterface;
use Apigee\Edge\Entity\EntityFactory;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\FrameWork\Constrait\EntityHasValues;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;

/**
 * Class BaseEntityControllerValidator.
 *
 * Helps in validation of all entity controllers that implements BaseEntityControllerInterface.
 *
 * @package Apigee\Edge\Tests\Test\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see BaseEntityControllerInterface
 */
abstract class BaseEntityControllerValidator extends TestCase
{
    /** @var \Apigee\Edge\HttpClient\ClientInterface */
    protected static $client;

    /** @var \Apigee\Edge\Entity\EntityFactoryInterface */
    protected static $entityFactory;

    /** @var EntityInterface[] */
    protected static $createdEntities;

    /** @var string */
    protected static $onlyOnlineClientSkipMessage = 'Test can be executed only with real Apigee Edge connection.';

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::$entityFactory = new EntityFactory();
        self::$client = (new TestClientFactory())->getClient();
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
     * @return \Apigee\Edge\Entity\BaseEntityControllerInterface
     */
    abstract protected static function getEntityController(): BaseEntityControllerInterface;

    /**
     * Returns test data that can be used to test creation of entity.
     *
     * @return array
     */
    abstract protected function sampleDataForEntityCreate(): array;

    /**
     * Returns test data that can be used to test entity update.
     *
     * (Data should be the altered version of the returned data by sampleDataForEntityCreate)
     *
     * @return array
     */
    abstract protected function sampleDataForEntityUpdate(): array;

    /**
     * Returns the expected values of an entity after it has been created.
     *
     * @return array
     */
    protected function expectedValuesAfterEntityCreate(): array
    {
        return $this->sampleDataForEntityCreate();
    }

    /**
     * @return string
     */
    public function testCreate()
    {
        /** @var EntityInterface $entity */
        $entity = self::$entityFactory->getEntityByController($this->getEntityController());
        // Data providers could not be used instead of directly calling this function, because this function would
        // require two input arguments: the entity values for creation and the expected values after entity has been
        // created. If we would use more than on data provider on this function then it would get the merged result
        // of providers as a _single_ value.
        $entity = $entity::fromArray($this->sampleDataForEntityCreate());
        $entity = $this->getEntityController()->save($entity);
        self::$createdEntities[$entity->id()] = $entity;
        // Validate properties that values are either auto generated or we do not know in the current context.
        $this->assertEntityHasAllPropertiesSet($entity);
        $this->assertEntityHasProperValues($entity, $this->expectedValuesAfterEntityCreate());
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
        $this->assertEntityHasProperValues($entity, $this->expectedValuesAfterEntityCreate());
        return $entityId;
    }

    /**
     * Get the entityID from the testLoad() instead of testCreate() because load should pass before update.
     *
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testUpdate(string $entityId)
    {
        /** @var EntityInterface $entity */
        $entity = $this->getEntityController()->load($entityId);
        // Nested arrays will be overridden by the update data, but it is fine, we do want to test that too.
        // If nested array element should be kept as-is then it should be added to update data.
        $update = array_merge($entity->toArray(), $this->sampleDataForEntityUpdate());
        // Of course, this property's value will change.
        if (isset($update['lastModifiedAt'])) {
            unset($update['lastModifiedAt']);
        }
        $entity = $entity::fromArray($update);
        $entity = $this->getEntityController()->save($entity);
        // Validate properties that values are either auto generated or we do not know in the current context.
        $this->assertEntityHasAllPropertiesSet($entity);
        $this->assertEntityHasProperValues($entity, $update);
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

    /**
     * @param \Apigee\Edge\Entity\EntityInterface $entity
     * @param array $expected
     * @param string $message
     */
    public static function assertEntityHasProperValues(EntityInterface $entity, array $expected, string $message = '')
    {
        $constraint = new EntityHasValues($expected, true);
        static::assertThat($entity->toArray(), $constraint, $message);
    }
}
