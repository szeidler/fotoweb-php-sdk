# Fotoweb by fotoware.com PHP SDK

![Build Status](https://github.com/szeidler/fotoweb-php-sdk/actions/workflows/ci.yml/badge.svg)

Fotoweb PHP SDK utilizes [guzzle-services](https://github.com/guzzle/guzzle-services) for an easy integration with
[Fotoware's](https://www.fotoware.com/) FotoWeb RESTful API.

## Requirements

* PHP 7.4 or greater
* Composer
* Guzzle

## Installation

Add Fotoweb PHP SDK as a composer dependency.

`composer require szeidler/fotoweb-php-sdk:^2.0`

## Usage

Returns the asset representation based on the resource url of the asset.

```php
use Fotoweb\FotowebClient;

// Using the legacy API code method.

$client = new FotowebClient([
    'baseUrl'  => 'https://demo.fotoware.com',
    'authType' => 'token',
    'apiToken' => 'yourapi token',
]);

// Using oAuth2 with client credentials (Web API)

$client = new FotowebClient([
    'baseUrl'  => 'https://demo.fotoware.com',
    'authType' => 'oauth2',
    'grantType' => 'client_credentials',
    'clientId' => 'your client id',
    'clientSecret' => 'your client secret',
    'persistenceProvider' => new \kamermans\OAuth2\Persistence\NullTokenPersistence(),
]);

// Using oAuth2 with authorization_code (Web API)
$client = new FotowebClient([
    'baseUrl'  => 'https://demo.fotoware.com',
    'authType' => 'oauth2',
    'grantType' => 'authorization_code',
    'codeVerifier' => 'PKCE code verifier',
    'redirectUri' => 'your oauth2 redirect callback',
    'clientId' => 'your client id',
    'clientSecret' => 'your client secret',
    'persistenceProvider' => new \kamermans\OAuth2\Persistence\NullTokenPersistence(),
]);

$href = '/fotoweb/archives/5013-Demo%20assets/Artwork/Coffee%20from%20DAM/240x400.jpg.info';
$asset = $client->getAsset(['href' => $href]);
print $asset->offsetGet('filesize');
```

## Testing

This SDK includes PHPUnit as a composer `require-dev`. Copy the `phpunit.xml.dist` to `phpunit.xml` and fill in with
your API testing credentials.

Note: Currently the SDK does not use mocked responses, but does actual API calls.

`./vendor/bin/phpunit -c phpunit.xml`

## Credits

Stephan Zeidler for [Ramsalt Lab AS](https://ramsalt.com)

## License

The MIT License (MIT)
