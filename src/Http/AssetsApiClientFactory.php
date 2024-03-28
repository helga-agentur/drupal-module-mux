<?php

namespace Drupal\mux\Http;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use GuzzleHttp\Client;
use MuxPhp\Api\AssetsApi;
use MuxPhp\Configuration;

/**
 * Service for generating Mux Assets API clients.
 */
class AssetsApiClientFactory {

  /**
   * The config settings object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected ImmutableConfig $config;

  /**
   * Constructs a new AssetsApiClientFactory instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('mux.settings');
  }

  /**
   * Creates a new OpenAI client instance.
   *
   * @return AssetsApi
   *   The client instance.
   */
  public function create(): AssetsApi {
    $config = Configuration::getDefaultConfiguration()
      ->setUsername($this->config->get('username'))
      ->setPassword($this->config->get('password'));

    // API Client Initialization
    return new AssetsApi(
      new Client(),
      $config
    );
  }

}
