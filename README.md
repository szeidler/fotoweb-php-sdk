# Fotoweb by fotoware.com PHP SDK

[![Build Status](https://travis-ci.org/szeidler/fotoweb-php-sdk.svg?branch=master)](https://travis-ci.org/szeidler/fotoweb-php-sdk)

Fotoweb PHP SDK utilizes [guzzle-services](https://github.com/guzzle/guzzle-services) for an easy integration with
[Fotoware's](https://www.fotoware.com/) FotoWeb RESTful API.

## Requirements

* PHP 5.6.0 or greater (PHP 7 recommended)
* Composer
* Guzzle

## Installation

Add Fotoweb PHP SDK as a composer dependency.

`composer require szeidler/fotoweb-php-sdk:dev-master`

## Usage

Returns the asset representation based on the resource url of the asset.

```php
use Fotoweb\FotowebClient;

$client = new FotowebClient([
    'baseUrl'  => 'https://demo.fotoware.com',
    'apiToken' => 'UHDuXw',
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
