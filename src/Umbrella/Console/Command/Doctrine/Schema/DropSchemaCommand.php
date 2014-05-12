<?php

namespace Umbrella\Console\Command\Doctrine\Schema;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Tools\SchemaTool;

use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;

class DropSchemaCommand extends DropCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('doctrine:schema:drop');
    }

    /**
     * {@inheritdoc}
     */
    protected function executeSchemaCommand(InputInterface $input, OutputInterface $output, SchemaTool $schemaTool, array $metadatas)
    {
        parent::executeSchemaCommand($input, $output, $schemaTool, $metadatas);
    }
}
