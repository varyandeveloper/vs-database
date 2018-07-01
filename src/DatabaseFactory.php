<?php

namespace VS\Database\SQL;

use VS\Container\ContainerInterface;
use VS\Container\FactoryInterface;
use VS\Database\DatabaseInterface;

/**
 * Class DatabaseFactory
 * @package VS\Framework\Database
 */
class DatabaseFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @return DatabaseInterface
     */
    public function __invoke(ContainerInterface $container): DatabaseInterface
    {
        $configuration = $container->getConfiguration(__CLASS__);
        $driver = $container->get($configuration['driver'], $configuration);
        return new $configuration['class']($driver);
    }
}