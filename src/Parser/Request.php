<?php

namespace Crawler\Parser;

class Request
{
    public string $url;
    public int $httpCode;
    public static $userAgent = 'Gyan Bot/0.1';

    public function get($url, $headers = [])
    {

        $ch = curl_init();
        $this->setupCurl($ch, $url, $headers);

        $content = curl_exec($ch);
        if ($content === false) {
            throw new \RuntimeException('cURL error: ' . curl_error($ch));
        }

        $info = curl_getinfo($ch);
        $this->url = $info['url'];
        $this->httpCode = $info['http_code'];

        curl_close($ch);

        if ($this->httpCode >= 400 || !$this->isContentTypeMatch($headers)) {
            throw new \RuntimeException('HTTP error: ' . $this->httpCode);
        }

        return $content;
    }

    public function getHTML($url)
    {
        return $this->get($url, [
            "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
            'Content-Type: application/json',
            "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
            "Accept-Language: en-us,en;q=0.5",
            "Cache-Control: max-age=0",
            "Pragma: "
        ]);
    }

    public function getTxt($url)
    {
        return $this->get($url, ['content_type' => 'html']);
    }

    public function getXML($url)
    {
        return $this->get($url, ['Content-Type: text/xml']);
    }

    private function setupCurl($ch, $url, $options)
    {
        $userAgent = $options['user_agent'];

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_USERAGENT => self::$userAgent,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING => 'gzip,deflate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
        ]);

        // Additional options based on content type
        switch ($options['content_type']) {
            case 'xml':
                // Add XML specific options if needed
                break;
            case 'txt':
                // Add TXT specific options if needed
                break;
                // Add more cases for additional content types
        }
    }

    private function isContentTypeMatch($options)
    {
        $contentType = strtolower($options['content_type'] ?? '');

        switch ($contentType) {
            case 'html':
                return $this->httpCode === 200 && $this->isContentType('text/html');
            case 'txt':
                return $this->httpCode === 200 && $this->isContentType('text/plain');
            case 'xml':
                return $this->httpCode === 200 && $this->isContentType('application/xml');
            default:
                return false;
        }
    }

    private function isContentType($expectedType)
    {
        return stripos($this->getHeader('Content-Type'), $expectedType) !== false;
    }

    private function getHeader($header)
    {
        $headers = get_headers($this->url, 1);
        return $headers[$header] ?? '';
    }
}
