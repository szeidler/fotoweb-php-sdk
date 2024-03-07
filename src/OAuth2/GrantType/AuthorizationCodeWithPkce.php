<?php

namespace Fotoweb\OAuth2\GrantType;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Utils;
use kamermans\OAuth2\GrantType\GrantTypeInterface;
use kamermans\OAuth2\Signer\ClientCredentials\SignerInterface;
use kamermans\OAuth2\Utils\Collection;
use kamermans\OAuth2\Utils\Helper;

/**
 * Authorization code grant type with PKCE support.
 *
 * Mostly taken over from kamermans\OAuth2\GrantType\AuthorizationCode, but
 * due to private properties we could not use it.
 *
 * @link http://tools.ietf.org/html/rfc6749#section-4.1
 */
class AuthorizationCodeWithPkce implements GrantTypeInterface {

  /**
   * The token endpoint client.
   *
   * @var ClientInterface
   */
  protected $client;

  /**
   * Configuration settings.
   *
   * @var Collection
   */
  protected $config;

  /**
   * {@inheritdoc}
   */
  public function __construct(ClientInterface $client, array $config) {
    $this->client = $client;
    $this->config = Collection::fromConfig(
      $config,
      // Defaults
      [
        'client_secret' => '',
        'scope' => '',
        'redirect_uri' => '',
      ],
      // Required
      [
        'client_id',
        'code',
        'code_verifier',
      ]
    );
  }

  public function getRawData(SignerInterface $clientCredentialsSigner, $refreshToken = NULL) {
    $request = (new \GuzzleHttp\Psr7\Request('POST', $this->client->getConfig()['base_uri']))
      ->withBody($this->getPostBody())
      ->withHeader('Content-Type', 'application/x-www-form-urlencoded');

    $request = $clientCredentialsSigner->sign(
      $request,
      $this->config['client_id'],
      $this->config['client_secret']
    );

    $response = $this->client->send($request);

    return json_decode($response->getBody(), TRUE);
  }

  /**
   * {@inheritdoc}
   */
  protected function getPostBody() {
    // We only support Guzzle >= 6 in contradiction to the external library.
    $data = [
      'grant_type' => 'authorization_code',
      'code' => $this->config['code'],
      'code_verifier' => $this->config['code_verifier'],
    ];

    if ($this->config['scope']) {
      $data['scope'] = $this->config['scope'];
    }

    if ($this->config['redirect_uri']) {
      $data['redirect_uri'] = $this->config['redirect_uri'];
    }

    return Utils::streamFor(http_build_query($data, '', '&'));
  }

}
