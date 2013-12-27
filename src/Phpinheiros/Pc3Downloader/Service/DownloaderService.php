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

        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_HEADER, 1);
        curl_setopt($oCurl, CURLOPT_VERBOSE, 0);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($oCurl, CURLOPT_CUSTOMREQUEST,'GET');
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_MAXREDIRS, 2);
        curl_setopt($oCurl, CURLOPT_USERAGENT,
    "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7");
        $httpProxy = getenv('http_proxy');

        if ( false !== $httpProxy ) {
            curl_setopt($oCurl, CURLOPT_PROXY, $httpProxy);
        }

        $conteudo = curl_exec($oCurl);

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
            if( ! file_exists($diretorio) ) {
                mkdir($diretorio, 0, true);
            }

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
        var_dump($url);
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