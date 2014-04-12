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
     * Curl resource
     * @var resource
     */
    private $ch;
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
        
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        curl_setopt($this->ch, CURLOPT_VERBOSE, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST,'GET');
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 320);
        curl_setopt($this->ch, CURLOPT_VERBOSE, 0);
        
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Connection: Keep-Alive',
            'Keep-Alive: 300'
        ));
        
        curl_setopt($this->ch, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:26.0) Gecko/20100101 Firefox/26.0");
        $httpProxy = getenv('http_proxy');
        
        if ( false !== $httpProxy ) {
            curl_setopt($this->ch, CURLOPT_PROXY, $httpProxy);
        }
        
        $conteudo = curl_exec($this->ch);
        curl_close($this->ch);
        if( false === $conteudo ) {
            throw new RunTimeException(sprintf('A url informada eh invalida. Erro:', curl_error($this->ch)));
        }
        
        $destino->fwrite($conteudo);
    }

    public function fetchFiles(array $urls, $diretorio, $output)
    {
        $album = 0;
        foreach($urls as $url) {
            $nomeArquivo = basename(parse_url($url, PHP_URL_PATH));

            $dadosMusica = explode('-', $nomeArquivo);

            $numeroMusica = is_numeric($dadosMusica[0]) ? $dadosMusica[0] : $dadosMusica[1];

            if( is_numeric($numeroMusica) ) {

                if( 1 == $numeroMusica ) {
                    $album++;
                }

                $diretorioAlbum = $diretorio . DIRECTORY_SEPARATOR . 'Album ' . $album;
            } else {
                $diretorioAlbum = $diretorio . DIRECTORY_SEPARATOR . $dadosMusica[0];
            }

            $output->writeln('Baixando a musica: ' . $nomeArquivo);
            if( ! file_exists($diretorioAlbum) ) {
                mkdir($diretorioAlbum, 0777, true);
            }

            $filename = realpath($diretorioAlbum) . DIRECTORY_SEPARATOR .$nomeArquivo;
            
            if( file_exists($filename) && 1024 < filesize($filename) ) {
                $output->writeln("\t\t\tA musica ja foi baixada.");
                continue;
            }
            
            $this->fetchFile($url, new \SplFileObject($filename, 'x'));
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