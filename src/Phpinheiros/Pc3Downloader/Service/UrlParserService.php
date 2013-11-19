<?php
namespace Phpinheiros\Pc3Downloader\Service;

/**
 * Servico para tratar qual é a url a ser usada para baixar as músicas
 * @author Vinicius de Sa <viniciusss@me.com>
 */
class UrlParserService
{
    const URL_PALCOMP3 = 'http://www.palcomp3.com/';

    /**
     * Retorna a url a ser utilizada
     * @param string $pagina
     * @return string
     */
    public function getUrl($pagina)
    {
        if( empty( $pagina ) ) {
            throw new \InvalidArgumentException('A pagina nao pode ser vazia.');
        }

        return $this->parser($pagina);
    }

    public function isValidUrl($url)
    {
        if(false === filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parserUri = parse_url($url);

        if( 'www.' != substr($parserUri['host'], 0, 4) ) {
            $parserUri['host'] = 'www.' . $parserUri['host'];
        }

        if($this->fetchParserUri()['host'] != $parserUri['host']) {
            throw new \InvalidArgumentException('A url informada deve ser do site palcomp3.com');
        }

        if($this->fetchParserUri()['scheme'] != $parserUri['scheme']) {
            throw new \InvalidArgumentException('A url informada deve ser do site palcomp3.com');
        }

        return true;
    }

    protected function fetchParserUri()
    {
        return parse_url(self::URL_PALCOMP3);
    }

    protected function parser($pagina)
    {
        if( true == $this->isValidUrl($pagina) )
            return $pagina;

        $dadosPagina = explode('/', $pagina);

        return self::URL_PALCOMP3 . trim($dadosPagina[0], '/') . '/';
    }
}