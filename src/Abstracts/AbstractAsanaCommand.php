<?php

namespace CarloNicora\Minimalism\Services\Asana\Abstracts;

use Asana\Client;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Interfaces\SimpleObjectInterface;
use CarloNicora\Minimalism\Services\Asana\Asana;
use CarloNicora\Minimalism\Services\Asana\Factories\AsanaObjectFactory;
use Exception;

abstract class AbstractAsanaCommand implements SimpleObjectInterface
{
    /** @var Client  */
    protected Client $client;

    /** @var AsanaObjectFactory  */
    protected AsanaObjectFactory $factory;

    /**
     * @param Asana $asana
     * @param ObjectFactory $objectFactory
     * @throws Exception
     */
    public function __construct(
        Asana $asana,
        ObjectFactory $objectFactory,
    )
    {
        $this->client = $asana->getClient();
        $this->factory = $objectFactory->create(AsanaObjectFactory::class);
    }
}