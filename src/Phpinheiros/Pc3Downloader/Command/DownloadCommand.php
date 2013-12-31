<?php
namespace Phpinheiros\Pc3Downloader\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Phpinheiros\Pc3Downloader\Service\DownloaderService;
use Phpinheiros\Pc3Downloader\Service\ListagemMusicasService;
use Phpinheiros\Pc3Downloader\Service\UrlParserService;
class DownloadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('download')
            ->setDescription('Baixa todas as musicas de uma determinada pagina do PalcoMP3.')
            ->addArgument(
                    'pagina',
                    InputArgument::REQUIRED,
                    'Qual a página a ser baixada?'
                )
            ->addArgument(
                    'destino',
                    InputArgument::OPTIONAL,
                    'Qual o destino dos arquivos?',
                    $_SERVER['PWD']
                )
        ->addOption('playlist');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Buscando as musicas!');
        $downloader = new DownloaderService();
        $urlParser = new UrlParserService();
        $listagemMusicas = new ListagemMusicasService($downloader);
        try{
            $urlPagina = $urlParser->getUrl($input->getArgument('pagina'));
            
            if( $input->hasOption('playlist') ) {
                $musicas = $listagemMusicas->fetchUrlsPlaylist($urlPagina);
            } else {
                $musicas = $listagemMusicas->fetchUrlsMusicas($urlPagina, 'download');
            }

            if( empty($musicas) ) {
                $output->writeln('Nenhuma musica foi encontrada.');
                return;
            }

            $output->writeln( sprintf('%d musicas foram encontradas.', count($musicas)) );

            $downloader->fetchFiles($musicas, $input->getArgument('destino'), $output);
        } catch (\Exception $e) {
            $output->writeln(
                sprintf('<error>%s</error>', $e->getMessage())
            );
        }
    }
}