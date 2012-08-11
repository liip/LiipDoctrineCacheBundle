<?php

namespace Liip\DoctrineCacheBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CacheFlushCommand.
 */
class CacheFlushCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('liip:doctrine-cache:flush');
        $this->setAliases(array('liip:doctrine-cache:clear'));
        $this->setDescription('Clean the given cache');
        $this->addArgument(
            'cache-name', InputArgument::REQUIRED, 'Which cache to clean?'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = 'liip_doctrine_cache.ns.'.$input->getArgument('cache-name');

        $this->getContainer()->get($serviceName)->flushAll();
    }
}
