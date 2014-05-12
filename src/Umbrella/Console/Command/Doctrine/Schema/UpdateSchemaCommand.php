<?php

namespace Umbrella\Console\Command\Doctrine\Schema;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Tools\SchemaTool;

use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;

class UpdateSchemaCommand extends UpdateCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('doctrine:schema:update');
    }

    /**
     * {@inheritdoc}
     */
    protected function executeSchemaCommand(InputInterface $input, OutputInterface $output, SchemaTool $schemaTool, array $metadatas)
    {
        parent::executeSchemaCommand($input, $output, $schemaTool, $metadatas);
    }
}
