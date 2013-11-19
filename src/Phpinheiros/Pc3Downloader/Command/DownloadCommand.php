<?php
namespace Phpinheiros\Pc3Downloader\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Phpinheiros\Pc3Downloader\Service as Services;

class DownloadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('pc3downloader:download')
            ->setDescription('Baixa todas as musicas de uma determinada pagina do PalcoMP3.')
            ->addArgument(
                    'pagina',
                    InputArgument::REQUIRED,
                    'Qual a página a ser baixada?'
                )
            ->addArgument(
                    'destino',
                    InputArgument::OPTIONAL,
                    'Qual o destino dos arquivos?'
                )
            ->addOption(
                    'playlist',
                    null,
                    InputOption::VALUE_NONE,
                    'Se informada serão baixadas as músicas da playlist.'
                );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $urlParser = new Services\UrlParserService();
        $output->writeln('Buscando');
    }
}