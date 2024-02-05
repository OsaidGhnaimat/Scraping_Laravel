<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Exception;

class ContentCrawler extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout'   => 10,
            'verify'    => false
        ]);
    }

    public function getCrawlerContent()
    {
        try {
            $response = $this->client->get('https://www.ideal-ways.com/');
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);

            $_this = $this;
            $data = $crawler->filter('.box-all') // Update the selector to target the .box-all elements
                            ->each(function (Crawler $node, $i) use ($_this) {
                                return $_this->getNodeContent($node);
                            });

            dump($data);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function hasContent($node)
    {
        return $node->count() > 0 ? true : false;
    }

    private function getNodeContent($node)
    {
        $array = [
            'image' => $this->hasContent($node->filter('img')) ? 'https://www.ideal-ways.com' . $node->filter('img')->attr('src') : 'empty',
            'text' => $this->hasContent($node->filter('.box-text p')) ? $node->filter('.box-text p')->text() : 'empty',
        ];

        return $array;
    }
}
