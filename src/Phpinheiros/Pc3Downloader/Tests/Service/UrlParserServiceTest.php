<?php
namespace Phpinheiros\Pc3Downloader\Tests\Service;

use Phpinheiros\Pc3Downloader\Service\UrlParserService;

class UrlParserServiceTest extends \PHPUnit_Framework_TestCase
{
    private $urlService;

    public function setUp()
    {
        $this->urlService = new UrlParserService();
    }

    public function providerUsuarios()
    {
        return array(
            array(
                'cristianoaraujo',
                UrlParserService::URL_PALCOMP3 . 'cristianoaraujo/musicas.htm'
            ),
            array(
                'viniciusss',
                UrlParserService::URL_PALCOMP3 . 'viniciusss/musicas.htm'
            )
            ,
            array(
                'viniciusss123',
                UrlParserService::URL_PALCOMP3 . 'viniciusss123/musicas.htm'
            )
        );
    }

    public function providerUrlsInvalidas()
    {
        return array(
            array('http://globo.com'),
            array('http://gmail.com'),
            array('ftp://palcomp3.com'),
            array('ftp://www.palcomp3.com')
        );
    }

    public function providerUrlValidas()
    {
        return array(
            array('http://palcomp3.com'),
            array('http://www.palcomp3.com'),
            array('http://www.palcomp3.com/teste'),
        );
    }

    /**
     * @dataProvider providerUrlsInvalidas
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage A url informada deve ser do site palcomp3.com
     */
    public function testExceptionAoPassarUmaUrlDeOutroSite($urlInvalida)
    {
        $this->urlService->isValidUrl($urlInvalida);
    }

    /**
     *
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionComPaginaEmBranco()
    {
        $this->urlService->getUrl('');
    }

    /**
     * @dataProvider providerUrlValidas
     */
    public function testAceitaApenasAUrlPalcomp3dotcomComoValida($urlValida)
    {
        $this->assertTrue(
            $this->urlService->isValidUrl($urlValida)
        );
    }

    /**
     * @dataProvider providerUsuarios
     * @depends testExceptionComPaginaEmBranco
     */
    public function testRetornoUrlPorUsuario($usuario, $urlDesejada)
    {
        $url = $this->urlService->getUrl($usuario);
        $this->assertEquals($urlDesejada, $url);
    }
}