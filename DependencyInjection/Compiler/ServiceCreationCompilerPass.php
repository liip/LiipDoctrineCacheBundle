<?php

namespace Liip\DoctrineCacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Reference,
    Symfony\Component\DependencyInjection\DefinitionDecorator,
    Symfony\Component\DependencyInjection\Definition;

class ServiceCreationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $namespaces = $container->getParameter('liip_doctrine_cache.namespaces');

        foreach ($namespaces as $name => $config) {
            $id = 'liip_doctrine_cache.'.$config['type'];
            if (!$container->hasDefinition($id)) {
                throw new \InvalidArgumentException('Supplied cache type is not supported: '.$config['type']);
            }

            $namespace = empty($config['namespace']) ? $name : $config['namespace'];
            $service = $container
                ->setDefinition('liip_doctrine_cache.ns.'.$name, new DefinitionDecorator($id))
                ->addMethodCall('setNamespace', array($namespace));

            foreach ($config['alias'] as $alias) {
                $container->setAlias('liip_doctrine_cache.ns.'.$alias, 'liip_doctrine_cache.ns.'.$name);
            }

            switch ($config['type']) {
                case 'memcache':
                    if (empty($config['id'])) {
                        $memcacheHost = !empty($config['host']) ? $config['host'] : '%liip_doctrine_cache.memcache_host%';
                        $memcachePort = isset($config['port']) ? $config['port'] : '%liip_doctrine_cache.memcache_port%';
                        $memcacheTimeout = !empty($config['timeout']) ? $config['timeout'] : '%liip_doctrine_cache.memcache_timeout%';
                        $memcache = new Definition('Memcache');
                        $memcache->addMethodCall('addServer', array(
                            $memcacheHost, $memcachePort, $memcacheTimeout
                        ));
                        $memcache->setPublic(false);
                        $memcacheId = sprintf('liip_doctrine_cache.%s_memcache_instance', $name);
                        $container->setDefinition($memcacheId, $memcache);
                    } else {
                        $memcacheId = $config['id'];
                    }

                    $service->addMethodCall('setMemcache', array(new Reference($memcacheId)));
                    break;
                case 'memcached':
                    if (empty($config['id'])) {
                        $memcachedHost = !empty($config['host']) ? $config['host'] : '%liip_doctrine_cache.memcached_host%';
                        $memcachedPort = !empty($config['port']) ? $config['port'] : '%liip_doctrine_cache.memcached_port%';
                        $memcached = new Definition('Memcached');
                        $memcached->addMethodCall('addServer', array(
                            $memcachedHost, $memcachedPort
                        ));
                        $memcached->setPublic(false);
                        $memcachedId = sprintf('liip_doctrine_cache.%s_memcached_instance', $name);
                        $container->setDefinition($memcachedId, $memcached);
                    } else {
                        $memcachedId = $config['id'];
                    }

                    $service->addMethodCall('setMemcached', array(new Reference($memcachedId)));
                    break;
                case 'redis':
                    if (empty($config['id'])) {
                        $redisHost = !empty($config['host']) ? $config['host'] : '%liip_doctrine_cache.redis_host%';
                        $redisPort = !empty($config['port']) ? $config['port'] : '%liip_doctrine_cache.redis_port%';
                        $redisTimeout = !empty($config['timeout']) ? $config['timeout'] : '%liip_doctrine_cache.redis_timeout%';
                        $redis = new Definition('Redis');
                        $redis->addMethodCall('connect', array(
                            $redisHost, $redisPort, $redisTimeout
                        ));
                        $redis->setPublic(false);
                        $redisId = sprintf('liip_doctrine_cache.%s_redis_instance', $name);
                        $container->setDefinition($redisId, $redis);
                    } else {
                        $redisId = $config['id'];
                    }

                    $service->addMethodCall('setRedis', array(new Reference($redisId)));
                    break;
                case 'file_system':
                case 'php_file':
                    $directory = !empty($config['directory']) ? $config['directory'] : '%kernel.cache_dir%/doctrine/cache';
                    $extension = !empty($config['extension']) ? $config['extension'] : null;

                    $service->setArguments(array($directory, $extension));
                    break;
            }
        }
    }
}
