<?php

namespace Crawler\Parser;

class Robotxt extends Request
{
    private SiteMap $siteMap;
    private $disAllowedPaths = [];
    private $userAgent = '*';

    public function __construct(SiteMap $sitemap)
    {
        $this->siteMap = $sitemap;
    }

    private function parse($url)
    {
        $content = $this->getTxt($url);
        if ($this->httpCode !== 200) return;

        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] == '#') continue;
            list($directive, $value) = explode(":", trim($line), 2);

            if (strtolower(trim($directive)) === 'user-agent') {
                $this->userAgent = trim($value);
            } else if (in_array($this->userAgent, ['GoogleBot', '*'])) {
                switch (strtolower(trim($directive))) {
                    case 'disallow':
                        $this->addDisallowPath(trim($value));
                        break;
                    case 'sitemap':
                        $this->siteMap->add(trim($value));
                        break;
                }
            }
        }
    }

    public function isAllow($url)
    {
        foreach ($this->disAllowedPaths as $regex) {
            // var_dump($regex);
            if (preg_match($regex, $url)) return false;
        }
        return true;
    }

    public function run($url)
    {
        $url = new Url($url);
        $this->parse($url->parse('/robots.txt'));
    }

    private function addDisallowPath($path)
    {
        $this->disAllowedPaths[] = "/" . str_replace(['*', '?'], ['.*', '.'], preg_quote($path, '/')) . "/i";
    }
}
