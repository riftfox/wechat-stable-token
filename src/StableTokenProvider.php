<?php

namespace Riftfox\Wechat\StableToken;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Riftfox\Wechat\Token\TokenFactoryInterface;
use Riftfox\Wechat\Application\ApplicationInterface;
use Riftfox\Wechat\Exception\ExceptionFactoryInterface;
use Riftfox\Wechat\Token\TokenProvider;
use Riftfox\Wechat\Token\TokenProviderInterface;

class StableTokenProvider extends TokenProvider implements TokenProviderInterface
{
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private StreamFactoryInterface $streamFactory;
    public function __construct(ClientInterface         $client,
                                TokenFactoryInterface $tokenFactory,
                                ExceptionFactoryInterface $exceptionFactory,
                                RequestFactoryInterface $requestFactory,
                                UriFactoryInterface     $uriFactory,
                                StreamFactoryInterface  $streamFactory)
    {
        parent::__construct($client, $tokenFactory, $exceptionFactory);
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
        $this->streamFactory = $streamFactory;
    }

    public function getRequest(ApplicationInterface $application, bool $forceRefresh = false): RequestInterface
    {
        $uri = $this->uriFactory->createUri(self::STABLE_TOKEN_URL);
        $request = $this->requestFactory->createRequest('POST', $uri);
        $body = json_encode([
            'grant_type' => 'client_credential',
            'appid' => $application->getAppId(),
            'secret' => $application->getAppSecret(),
            'force_refresh' => $forceRefresh
        ]);
        return $request->withBody($this->streamFactory->createStream($body));
    }
}