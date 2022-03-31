<?php

namespace Fotoweb\OAuth2\GrantType;

use GuzzleHttp\ClientInterface;
use kamermans\OAuth2\GrantType\AuthorizationCode;
use kamermans\OAuth2\Utils\Collection;

/**
 * Authorization code grant type with PKCE support.
 *
 * @link http://tools.ietf.org/html/rfc6749#section-4.1
 */
class AuthorizationCodeWithPkce extends AuthorizationCode {

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

    return \GuzzleHttp\Psr7\stream_for(http_build_query($data, '', '&'));
  }

}
