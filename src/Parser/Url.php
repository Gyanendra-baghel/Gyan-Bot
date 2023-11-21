<?php

namespace Crawler\Parser;

class Url
{
    public string $scheme;
    public string $host;
    public string $port;
    public string $path;
    public string $url;

    public function __construct($url)
    {
        $this->url = $url;
        $parsedUrl = parse_url($url);
        $this->scheme = $parsedUrl['scheme'];
        $this->host = $parsedUrl['host'];
        $this->port = $parsedUrl['port'] ?? "";
        $this->path = $parsedUrl['path'] ?? "";
    }
    public function parse($url)
    {
        $parsedUrl = parse_url($url);
        $scheme = $parsedUrl['scheme'] ?? $this->scheme;
        $host = $parsedUrl['host'] ?? $this->host;
        $port = $parsedUrl['port'] ?? $this->port;
        $path = $parsedUrl['path'] ?? "";
        $relativePath = $this->path;
        if (!empty($port)) $port = ':' . $port;
        if ($host !== $this->host) return $scheme . "://" . $host;

        while (substr($path, 0, 3) === '../') {
            $relativePath = dirname($relativePath);
            $path = substr($path, 3);
        }
        if (substr($path, 0, 2) === '//') {
            return $scheme . $path;
        } else if (substr($path, 0, 1) === '/') {
            return $scheme . '://' . $host . $port . $path;
        } else if (substr($path, 0, 2) == './') {
            $path = $relativePath . substr($path, 2);
        }
        $path = trim($path, '/');
        if (!empty($port)) $port = ':' . $port;
        return "{$scheme}://{$host}{$port}/{$path}";
    }
    public function isExternalUrl($url)
    {
        $host = parse_url($url)['host'];
        if (!is_null($host) && $this->host !== $host) return true;
        return false;
    }
    public function isInternalUrl($url)
    {
        $host = parse_url($url)['host'];
        if ($this->host === $host) return true;
        return false;
    }
    public function isInternal()
    {
        return true;
    }
}
