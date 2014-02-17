<?php

namespace Liip\DoctrineCacheBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Cache\CacheProvider;

/**
 * CacheDeleteAllCommand
 * Allows to launch a delete all on the underlying cache provider
 * the main difference with a flush is that only the given namespace 
 * is affected by the delete command (especially useful when a single
 * cache provider hosts multiple namespaces)
 */
class CacheDeleteAllCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('liip:doctrine-cache:delete-all');
        $this->setDescription('Clean the given cache');
        $this->addArgument(
            'cache-name', InputArgument::REQUIRED, 'Which cache to clean?'
        );
        $this->addArgument(
            'use-namespace',
            InputArgument::OPTIONAL,
            'Which namespace to use (defaults to the one specified in the container configuration)'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Retrieve the cache provider associated to the given cache-name
        $cacheName = $input->getArgument('cache-name');
        $serviceName = 'liip_doctrine_cache.ns.'.$cacheName;
        $cacheProvider = $this->getContainer()->get($serviceName);

        if ($cacheProvider instanceof CacheProvider) {
            // In case we force another namespace, force it in the cache provider
            $namespace = $input->getArgument('use-namespace');
            if ('' != $namespace) {
                $cacheProvider->setNamespace($namespace);
            } else {
                $namespace = $cacheProvider->getNamespace();
            }

            // Do the actual cache invalidation for the given namespace
            $cacheProvider->deleteAll();
        } else {
            // Should not happen...
            throw new \RuntimeException(sprintf(
                'Unable to get a cache named %s from the container',
                $cacheName
            ));
        }

        $output->writeln(sprintf(
            'The namespace %s of cache %s has been fully invalidated',
            $namespace,
            $cacheName
        ));
    }
}
