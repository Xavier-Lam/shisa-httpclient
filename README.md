# HTTPClient

A simple lightweight http client for PHP.

## Installation

    composer require shisa\httpclient


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