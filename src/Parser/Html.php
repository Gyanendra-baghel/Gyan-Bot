<?php

namespace Crawler\Parser;

use Crawler\Dataset\WebData;

class HTML
{
    public $isFollow = true;
    public $isIndex = true;
    public $webData;
    public $anchors = ['internal' => [], 'external' => []];
    private $doc;
    private Request $request;
    private $url;
    public function __construct($url)
    {
        $this->url = new Url($url);
        $this->webData = new WebData();
        $this->doc = new \DOMDocument();
        $this->request = new Request($url);
    }
    public function load($url)
    {
        $content = $this->request->getHtml($url);
        if ($this->request->httpCode !== 200) return false;
        $this->url = new Url($this->request->url);
        $this->doc->strictErrorChecking = false;
        @$this->doc->loadHTML($content);
        $this->webData->url = $this->request->url;
        $this->webData->text = $this->htmlToText($this->doc);
        return true;
    }
    private function htmlToText($node, $parentName = null)
    {
        if ($node->nodeType === 3) return trim($node->textContent);
        $text = '';
        if (isset($node->childNodes)) {
            $childNodes = $node->childNodes;
            while ($childNodes->length > 0) {
                $childNode = $childNodes->item(0);
                if ($childNode instanceof \DOMNodeList) continue;
                $text .= $this->htmlToText($childNode, $node->tagName ?? null);
                $node->removeChild($childNode);
            }
        }
        if ($node->nodeType !== 1) return $text;
        if ($parentName == 'head') {
            switch ($node->nodeName) {
                case 'meta':
                    $name = strtolower($node->getAttribute("name"));
                    $content = $node->getAttribute("content");
                    switch ($name) {
                        case 'keywords':
                            $this->webData->keywords = $content;
                            break;
                        case 'description':
                            $this->webData->description = $content;
                            break;
                        case 'viewport':
                            break;
                    }
                    break;
                case 'title':
                    $this->webData->title = $text;
                    break;
                case 'link':
                    $rel = $node->getAttribute("rel");
                    $href = $node->getAttribute("href");
                    if ($rel == 'icon') $this->webData->favicon = $href;
                    break;
            }
            return "";
        }
        // Body tag parse
        switch ($node->nodeName) {
            case 'a':
                $href = $this->url->parse($node->getAttribute("href"));
                if ($this->url->isInternal($href)) $this->anchors['internal'][] = $href;
                else $this->anchors['external'][] = $href;
                break;
        }
        return $text;
    }
}
