<?php

namespace Crawler;

use Crawler\Parser\HTML;
use Crawler\Parser\SiteMap;
use Crawler\Parser\Robotxt;
use Crawler\Parser\Url;
use Crawler\Dataset\Queue;
use Crawler\Dataset\Set;

class Crawler
{
    private Queue $restUrl;
    private Set $crawledUrl;
    private SiteMap $siteMap;
    private Robotxt $roboTxt;
    public Url $url;
    private $Indexer;
    public function __construct(string $url)
    {
        $this->url = new Url($url);
        $this->restUrl = new Queue();
        $this->crawledUrl = new Set();
        $this->siteMap = new SiteMap($this->restUrl, $this->url);
        $this->roboTxt = new RoboTxt($this->siteMap);
    }
    private function get($url)
    {
        exit;
        echo "$url is crawling\n";
        $this->crawledUrl->add($url);
        $dom = new HTML($url);
        if ($dom->load($url)) {
            if ($dom->isFollow && $this->roboTxt->isAllow($url)) $this->follow($dom->anchors);
            if ($dom->isIndex) $this->index($dom->webData);
        }
    }
    public function follow($anchors)
    {
        foreach ($anchors['internal'] as $link) {
            if (!$this->crawledUrl->has($link) && !$this->restUrl->has($link)) $this->restUrl->enqueue($link);
        }
    }
    public function index($result)
    {
        echo "----------------\n";
        var_dump($result);
        echo "----------------\n";
        // $this->Indexer->web($result);
    }
    public function run()
    {
        $this->roboTxt->run($this->url->url);
        $this->restUrl->enqueue($this->url->url);
        while ($this->restUrl->size() > 0) {
            $this->get($this->restUrl->dequeue());
        }
    }
}
