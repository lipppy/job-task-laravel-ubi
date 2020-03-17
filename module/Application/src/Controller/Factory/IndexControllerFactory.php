<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\IndexController;
use Zend\Db\Adapter\Adapter;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //$config = $container->get("Config");
        //$adapter = new Adapter($config['db']);

        $adapter = $container->get(Adapter::class);

        // Instantiate the controller and inject dependencies
        return new IndexController($adapter);
    }
}