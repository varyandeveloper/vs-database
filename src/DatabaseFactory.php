<?php

namespace VS\Database;

use VS\Database\Builders\SQL\Filter;
use VS\Database\Drivers\DriverInterface;
use VS\DIContainer\DIContainerInterface;
use VS\DIContainer\InvokableFactoryInterface;

/**
 * Class DatabaseFactory
 * @package VS\Framework\Database
 */
class DatabaseFactory implements InvokableFactoryInterface
{
    /**
     * @param DIContainerInterface $factory
     * @param array $params
     * @return DatabaseInterface
     */
    public function __invoke(DIContainerInterface $factory, string $className, array $params = []): DatabaseInterface
    {
        $configuration = $factory->getConfig(__CLASS__);
        /**
         * @var DriverInterface $driver
         */
        $driver = $factory->get($configuration->getByKey('driver'), $configuration->getConfig());
        $class = $configuration->getByKey('class');
        Filter::setDriver($driver);
        return new $class($driver);
    }
}
