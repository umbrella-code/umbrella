<?php

namespace Umbrella\Console\Command\Doctrine\Cache;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Cache\ApcCache;

use Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand;

class ClearMetadataCacheCommand extends MetadataCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('doctrine:cache:clear-metadata');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
    }
}
