<?php

namespace UpsFreeVendor\WPDesk\Composer\Codeception\Commands;

use UpsFreeVendor\Symfony\Component\Console\Input\InputArgument;
use UpsFreeVendor\Symfony\Component\Console\Input\InputInterface;
use UpsFreeVendor\Symfony\Component\Console\Output\OutputInterface;
use UpsFreeVendor\Symfony\Component\Yaml\Exception\ParseException;
use UpsFreeVendor\Symfony\Component\Yaml\Yaml;
/**
 * Codeception tests run command.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
class RunLocalCodeceptionTests extends \UpsFreeVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests
{
    use LocalCodeceptionTrait;
    /**
     * Configure command.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('run-local-codeception-tests')->setDescription('Run local codeception tests.')->setDefinition(array(new \UpsFreeVendor\Symfony\Component\Console\Input\InputArgument(self::SINGLE, \UpsFreeVendor\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Name of Single test to run.', ' ')));
    }
    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(\UpsFreeVendor\Symfony\Component\Console\Input\InputInterface $input, \UpsFreeVendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $configuration = $this->getWpDeskConfiguration();
        $this->prepareWpConfig($output, $configuration);
        $singleTest = $input->getArgument(self::SINGLE);
        $sep = \DIRECTORY_SEPARATOR;
        $codecept = "vendor{$sep}bin{$sep}codecept";
        $cleanOutput = $codecept . ' clean';
        $this->execAndOutput($cleanOutput, $output);
        $runLocalTests = $codecept . ' run -f --steps --html --verbose acceptance ' . $singleTest;
        $this->execAndOutput($runLocalTests, $output);
    }
}
