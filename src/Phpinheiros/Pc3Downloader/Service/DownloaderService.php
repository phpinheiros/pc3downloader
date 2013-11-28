<?php
namespace Phpinheiros\Pc3Downloader\Service;

use \RunTimeException;

/**
 * Servico para baixar os arquivos
 * @author Vinicius de Sa <viniciusss@me.com>
 */
class DownloaderService
{
    /**
     * Metodo para baixar um arquivo de um determinado local
     * @param string $url Caminho do arquivo
     * @param \SplFileObject $destino Caminho do arquivo
     */
    public function fetchFile($url, \SplFileObject $destino)
    {
        if( false == $destino->isWritable() && ! $destino instanceof \SplTempFileObject ) {
            throw new RunTimeException('O destino informado nao possui permissao de escrita.');
        }

        $conteudo = @file_get_contents($url);

        if( false === $conteudo ) {
            throw new RunTimeException('A url informada eh invalida.');
        }

        $destino->fwrite($conteudo);
    }

    public function fetchFiles(array $urls, $diretorio, $output)
    {
        foreach($urls as $url) {
            $nomeArquivo = basename(parse_url($url, PHP_URL_PATH));
            $output->writeln('Baixando a musica: ' . $nomeArquivo);

            $filename = realpath($diretorio) . DIRECTORY_SEPARATOR .$nomeArquivo;

            $this->fetchFile($url, new \SplFileObject($filename, 'a'));
        }
    }

    /**
     *
     * @param string $url
     * @return \SimpleXMLElement
     */
    public function fetchPageContent($url)
    {
        $conteudo = new \SplTempFileObject();

        $this->fetchFile($url, $conteudo);

        $fileContent = '';

        foreach ($conteudo as $linha) {
            $fileContent .= $conteudo->current();
        }
        libxml_use_internal_errors(true);
        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($fileContent);
        libxml_use_internal_errors(false);
        return $domDocument;
    }
}