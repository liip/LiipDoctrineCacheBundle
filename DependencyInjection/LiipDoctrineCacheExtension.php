<?php

namespace Liip\DoctrineCacheBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Reference,
    Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * LiipDoctrineCacheExtension is an extension for the Doctrine\Common\Cache interface.
 */
class LiipDoctrineCacheExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader =  new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        foreach ($config['namespaces'] as $name => $config) {
            $id = $this->getAlias().'.'.$config['type'];
            if (!$container->hasDefinition($id)) {
                throw new \InvalidArgumentException('Supplied cache type is not supported: '.$config['type']);
            }

            $namespace = empty($config['namespace']) ? $name : $config['namespace'];
            $service = $container
                ->setDefinition($this->getAlias().'.ns.'.$name, new DefinitionDecorator($id))
                ->addMethodCall('setNamespace', array($namespace));

            switch ($config['type']) {
                case 'memcache':
                    if (empty($config['id'])) {
                        throw new \InvalidArgumentException('Service id for memcache missing');
                    }
                    $service->addMethodCall('setMemcache', array(new Reference($config['id'])));
                    break;
                case 'memcached':
                    if (empty($config['id'])) {
                        throw new \InvalidArgumentException('Service id for memcached missing');
                    }
                    $service->addMethodCall('setMemcached', array(new Reference($config['id'])));
                    break;
            }
        }
    }
}
