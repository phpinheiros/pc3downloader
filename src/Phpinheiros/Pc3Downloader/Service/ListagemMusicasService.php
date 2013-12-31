<?php
namespace Phpinheiros\Pc3Downloader\Service;

use Symfony\Component\CssSelector\CssSelector;

class ListagemMusicasService
{
    /**
     *
     * @var DownloaderService;
     */
    private $downloader;

    public function __construct(DownloaderService $downloader)
    {
        $this->downloader = $downloader;
    }

    /**
     * @return DownloaderService
     */
    public function getDownloaderService()
    {
        return $this->downloader;
    }

    /**
     * Retorna a lista de urls encontradas para uma determinada pagina
     * @param string $urlPagina Caminho da pagina
     * @param string $classElements Classe dos elementos HTML
     * @return array
     */
    public function fetchUrlsMusicas($urlPagina, $classElements)
    {
        $elements = $this->fetchElementsUrl($urlPagina, $classElements);

        $musicas = array();

        foreach($elements as $node) {
            $musicas[] = trim($node->getAttribute('href'), '?');
        }

        return $musicas;
    }
    
    public function fetchUrlsPlaylist($urlPagina)
    {
        $listaMusica = $this->fetchElementsUrl($urlPagina, 'li[data-servidor!=""]');
        $musicas = [];
        foreach($listaMusica as $node) {
            $nomeArquivo = $node->getAttribute('data-arquivo');
            $servidor = $node->getAttribute('data-servidor');
            $hash = md5($nomeArquivo);
            $musicas[] = trim(sprintf('%s/%s/%s/%s/%s/%s', $servidor, $hash[0], $hash[1], $hash[2], $hash[3], $nomeArquivo));
        }
        
        return $musicas;
    }
    
    protected function fetchElementsUrl($url, $classElements)
    {
        if( empty($url) ) {
            throw new \InvalidArgumentException('O caminho da pagina deve ser informado.');
        }
        
        if( empty($classElements) ) {
            throw new \InvalidArgumentException('A classe dos elementos HTML deve ser informada.');
        }
        
        $document = $this->getDownloaderService()->fetchPageContent($url);
        
        $xpath = new \DOMXPath($document);
        
        return $xpath->query(CssSelector::toXPath($classElements));
    }
}




























