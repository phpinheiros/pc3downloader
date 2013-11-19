<?php
namespace Phpinheiros\Pc3Downloader\Tests\Command;

use Phpinheiros\Pc3Downloader\Command\DownloadCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DownloadCommandTest extends \PHPUnit_Framework_TestCase
{
    private $application;
    private $command;

    public function setUp()
    {
        $this->application = new Application();
        $this->application->add(new DownloadCommand());
        $this->command = $this->application->find('pc3downloader:download');
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testExecuteSemPassarArgumentoPagina()
    {
        $this->commandTester->execute(
            array('command' => $this->command->getName())
        );
    }

    public function testExecutePaginaInvalida()
    {
        $this->commandTester->execute(
            array(
                'command' => $this->command->getName(), 
                'pagina' => 'abc',
            )
        );

        $this->assertRegExp('/Buscando/', $this->commandTester->getDisplay());
    }
}