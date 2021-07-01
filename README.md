# HTTPClient

A simple lightweight http client for PHP.

## Installation

    composer require shisa/httpclient


## Quickstart

    use Shisa\HTTPClient\Clients\HTTPClient;
    use Shisa\HTTPClient\Formatters\JsonFormatter;

    $client = new HTTPClient();
    $client->setBaseUrl('https://baidu.com');
    $formatter = new JsonFormatter();
    $client->setFormatter($formatter);
    $response = $client->send('/debug', 'POST', ['data' => 1]);
    $data = $response->json();


## Usages
### Extend


## TODOS



## Changelog
### 0.2.1
* (BREAKING CHANGE) createRequest方法改为public方法
* 增加FormData Formatter
* 允许给`Request`对象增加属性

### 0.2.0
* Auth的isInvalidAuthError改为接受Exception异常
* 增加authRequestPostPrepare方法
* PreparedRequest的headers改为Key: value