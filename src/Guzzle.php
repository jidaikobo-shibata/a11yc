<?php

namespace Jidaikobo\A11yc;

class Guzzle
{
    protected $cons = array();
    protected $errors = array();
    protected $cons_tmp = array();
    protected $url;
    protected $real_url;
    protected $headers;
    protected $status_code;
    protected $body;
    protected $is_exists;
    protected $is_html;
    protected $is_xml;
    protected $is_pdf;
    protected $is_other;

    public static function init()
    {
        if (! static::envCheck()) {
            return;
        }

        if (! class_exists('\GuzzleHttp\Client')) {
            Util::error('Guzzle is not found');
        }
    }

    private static function defaultOptions(): array
    {
        return array(
            'http_errors' => false,
            'verify' => true,
            'timeout' => 10,
            'connect_timeout' => 5,
            'read_timeout' => 10,
        );
    }

    public static function envCheck()
    {
        if (version_compare(PHP_VERSION, '5.6.0') <= 0) {
            return false;
        }
        return true;
    }

    public static function forge($url, $cons = array()): self
    {
        return new self($url, $cons);
    }

    public function __construct($url, $cons = array())
    {
        $this->url = $url;
        $this->cons = $cons;
    }

    private function head()
    {
        if (isset($this->status_code) && $this->status_code) {
            return true;
        }
        $url = $this->url;
        $cons = $this->getConfig();
        $client = new \GuzzleHttp\Client(static::defaultOptions());

        try {
            try {
                $response = $client->head($url, $cons);
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                $this->errors[] = 'ConnectException';
                return null;
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errors[] = 'RequestException';
            return null;
        }

        if ($response->getStatusCode() == '405') {
            return self::get();
        }

        $this->status_code = $response->getStatusCode();
        $this->headers     = $response->getHeaders();
        $this->is_exists   = ($this->status_code == 200);
        $this->is_html     = $this->isHtml($response);
        return true;
    }

    private function get()
    {
        if (isset($this->body) && $this->body) {
            return true;
        }
        $url = $this->url;
        $cons = $this->getConfig();

        $stack = \GuzzleHttp\HandlerStack::create();
        $lastRequest = null;
        $stack->push(\GuzzleHttp\Middleware::mapRequest(
            function (\Psr\Http\Message\RequestInterface $request) use (&$lastRequest) {
                $lastRequest = $request;
                return $request;
            }
        ));

        $client = new \GuzzleHttp\Client(array_merge(
            static::defaultOptions(),
            array(
                'handler' => $stack,
                \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => true,
            )
        ));

        $request = new \GuzzleHttp\Psr7\Request('GET', $url, $cons);

        try {
            try {
                $response = $client->send($request, $cons);
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                $this->errors[] = 'ConnectException';
                return false;
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errors[] = 'RequestException';
            return false;
        }

        $this->real_url = $lastRequest->getUri()->__toString();
        $this->body     = $this->encoding($response->getBody()->getContents());
        $this->status_code = $response->getStatusCode();
        $this->headers     = $response->getHeaders();
        $this->is_exists   = ($this->status_code == 200);
        $this->is_html     = $this->isHtml($response);
        return true;
    }

    private function encoding($body)
    {
        if (strpos($body, 'charset="UTF-8"') !== false) {
            return $body;
        }

        $encodes = array("ASCII", "SJIS-win", "SJIS", "ISO-2022-JP", "EUC-JP");
        $encode = mb_detect_encoding($body, array_merge($encodes, array("UTF-8")));

        if (in_array($encode, $encodes)) {
            $body = mb_convert_encoding($body, "UTF-8", $encode);
        }
        return $body;
    }

    private function isHtml($response)
    {
        return strpos($response->getHeaderLine('Content-Type'), 'html') !== false;
    }

    public function setConfig($name, $val)
    {
        $this->cons = array_merge($this->cons, array($name => $val));
        $this->status_code = '';
        $this->body = '';
    }

    private function getConfig()
    {
        return $this->cons;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            if ($name == 'errors') {
                return $this->errors;
            } elseif (in_array($name, array('status_code', 'headers', 'is_exists', 'is_html'))) {
                if (is_null($this->head())) {
                    return null;
                }
            } else {
                if (! $this->get()) {
                    return null;
                }
            }
            return $this->$name;
        }
    }
}
