<?php

namespace Crawler\Parser;

use Crawler\Dataset\Queue;

class SiteMap extends Request
{
    private Queue $restUrl;
    private Queue $crawledUrl;
    private Url $url;

    public function __construct(Queue $crawledUrl, Url $url)
    {
        $this->restUrl = new Queue();
        $this->crawledUrl = $crawledUrl;
        $this->url = $url;
    }

    public function add($url)
    {
        $this->restUrl->enqueue($url);
    }

    public function parse($sitemapUrl)
    {
        $content = $this->getXml($sitemapUrl);
        if ($this->httpCode !== 200) return;

        $xml = simplexml_load_string($content);

        if ($xml !== false) {
            $xml->getNamespaces(true);
            foreach ($xml->url as $sitemapEntry) {
                $loc = (string) $sitemapEntry->loc;
                if (!$this->url->isExternalUrl($loc)) {
                    $this->crawledUrl->enqueue($loc);
                }
            }
        }
    }

    public function run()
    {
        while ($this->restUrl->length > 0) {
            $this->parse($this->restUrl->dequeue());
        }
    }
}
