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
        if( empty($urlPagina) ) {
            throw new \InvalidArgumentException('O caminho da pagina deve ser informado.');
        }

        if( empty($classElements) ) {
            throw new \InvalidArgumentException('A classe dos elementos HTML deve ser informada.');
        }

        $document = $this->getDownloaderService()->fetchPageContent($urlPagina);
        $xpath = new \DOMXPath($document);

        $elements = $xpath->query(CssSelector::toXPath('a.' . $classElements));

        $musicas = array();

        foreach($elements as $node) {
            $musicas[] = trim($node->getAttribute('href'), '?');
        }

        return $musicas;
    }
}




























