<?php

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\Entity\EntityControllerInterface;
use Apigee\Edge\Entity\EntityFactory;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class EntityControllerValidator.
 *
 * Base class that helps validation of entity controllers.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
abstract class EntityControllerValidator extends TestCase
{
    /** @var \Apigee\Edge\HttpClient\ClientInterface */
    protected static $client;

    /** @var \Apigee\Edge\Entity\EntityFactoryInterface */
    protected static $entityFactory;

    /** @var \Symfony\Component\Serializer\Normalizer\ObjectNormalizer */
    protected static $objectNormalizer;

    /** @var \Apigee\Edge\Entity\EntityInterface[] */
    protected static $createdEntities = [];

    /** @var string */
    protected static $onlyOnlineClientSkipMessage = 'Test can be executed only with real Apigee Edge connection.';

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        static::$entityFactory = new EntityFactory();
        static::$client = (new TestClientFactory())->getClient();
        static::$objectNormalizer = new ObjectNormalizer();
        static::$objectNormalizer->setSerializer(new Serializer([static::$objectNormalizer]));
        parent::setUpBeforeClass();
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass()
    {
        if (0 === strpos(static::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX)) {
            return;
        }

        // Remove created entities on Apigee Edge.
        try {
            foreach (static::$createdEntities as $entity) {
                static::getEntityController()->delete($entity->id());
                unset(static::$createdEntities[$entity->id()]);
            }
        } catch (\Exception $e) {
            printf("Unable to delete %s entity with %s id.\n", strtolower(get_class($entity)), $entity->id());
        }
    }

    /**
     * Returns the entity controller that is tested.
     *
     * It is recommended to use static cache on the controller instance, however it should not be added as an
     * attribute of a test class because it can be misleading later whether the static::$controller should be called in
     * a test method or this getter.
     *
     * @return \Apigee\Edge\Entity\EntityControllerInterface
     */
    abstract protected static function getEntityController(): EntityControllerInterface;
}
