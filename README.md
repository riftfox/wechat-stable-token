# wechat-stable-token

`wechat-stable-token` 是对 [微信公众平台获取稳定版接口调用凭据（stable_token）接口](https://api.weixin.qq.com/cgi-bin/stable_token) 的 PHP SDK 实现，依赖于 [`wechat-token`](../wechat-token) 统一的 Token 抽象体系。

## 功能简介

- 封装微信 stable_token 获取流程，自动处理接口调用凭据。
- 依赖 `wechat-token`，可与其它 Token 类型（如 access_token）统一管理。
- 遵循 PSR-7/PSR-17/PSR-18 标准，支持主流 HTTP 客户端。
- 适用于需要与微信公众平台交互、自动管理 stable_token 的 PHP 应用。

## 依赖

- PHP 7.4 及以上
- psr/http-client
- psr/http-factory
- riftfox/wechat-token
- riftfox/wechat-application
- riftfox/wechat-exception

## 安装

建议通过 Composer 安装（如已发布到 Packagist）：

```bash
composer require riftfox/wechat-stable-token
```

或在本地开发环境中，确保 `wechat-token` 及相关依赖已正确引入。

## 使用方法

### 1. 实现 ApplicationInterface

你可以直接使用 `wechat-application` 包中的实现，或自定义实现：

```php
use Riftfox\Wechat\Application\ApplicationInterface;

class Application implements ApplicationInterface {
    // 实现 getAppId/getAppSecret 等方法
}
```

或参考 `wechat-application/tests/Application.php`。

### 2. 初始化 StableTokenProvider

```php
use Riftfox\Wechat\StableToken\StableTokenProvider;
use Riftfox\Wechat\Token\TokenFactoryInterface;
use Riftfox\Wechat\Exception\ExceptionFactoryInterface;
use GuzzleHttp\Client; // 仅为示例，需实现 PSR-18 ClientInterface
use Nyholm\Psr7\Factory\Psr17Factory; // 需实现 PSR-17 Request/Uri/StreamFactory

$client = new Client(); // 实现了 PSR-18
$requestFactory = new Psr17Factory();
$uriFactory = new Psr17Factory();
$streamFactory = new Psr17Factory();
$tokenFactory = new class implements TokenFactoryInterface {
    public function createTokenFormArray(array $data): \Riftfox\Wechat\Token\TokenInterface {
        // 实现 createTokenFormArray
    }
};
$exceptionFactory = new class implements ExceptionFactoryInterface {
    public function createException(string $message, int $code, $previous = null): \Exception {
        return new \Exception($message, $code, $previous);
    }
};

$provider = new StableTokenProvider(
    $client,
    $tokenFactory,
    $exceptionFactory,
    $requestFactory,
    $uriFactory,
    $streamFactory
);
```

### 3. 获取 stable_token

```php
$application = new Application('your-appid', 'your-secret', ApplicationInterface::TYPE_OFFICE);
$token = $provider->token($application);

if ($token->getAccessToken()) {
    echo $token->getAccessToken();
} else {
    // 处理异常或错误
}
```

## 相关链接

- [微信 stable_token 官方文档](https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/api/component_access_token.html)
- [微信 stable_token 接口](https://api.weixin.qq.com/cgi-bin/stable_token)

## License

MIT
