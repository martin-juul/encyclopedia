<?php
declare(strict_types=1);

namespace App\Profiling\Context;

use Illuminate\Http\Request;

class RequestContext extends ProfileContext
{
    public string $category = 'request';

    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function toArray(): array
    {
        return [
            'client_ips'       => $this->request->getClientIps(),
            'locale'           => $this->request->getLocale(),
            'method'           => $this->request->method(),
            'protocol_version' => $this->request->getProtocolVersion(),
            'scheme'           => $this->request->getScheme(),
            'host'             => $this->request->getHost(),
            'uri'              => $this->request->getRequestUri(),
            'url'              => $this->request->fullUrl(),
            'query'            => $this->request->query,
        ];
    }
}
