<?php

namespace Drupal\mux;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use MuxPhp\Api\AssetsApi;
use MuxPhp\ApiException;
use MuxPhp\Models\Asset;
use MuxPhp\Models\CreateAssetRequest;
use MuxPhp\Models\InputSettings;
use MuxPhp\Models\PlaybackPolicy;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MuxAssetsApi
 *
 * This class is responsible for handling the interaction with the Mux Assets API.
 * It provides methods to upload a video to Mux and retrieve the playback id.
 */
class MuxAssetsApi implements ContainerInjectionInterface {

  /**
   * MuxAssetsApi constructor.
   *
   * @param AssetsApi $assetsApi
   *   The Mux Assets API client.
   * @param LoggerChannelFactoryInterface $loggerChannelFactory
   *   The logger channel factory service.
   */
  public function __construct(AssetsApi $assetsApi, LoggerChannelFactoryInterface $loggerChannelFactory) {
    $this->assetsApi = $assetsApi;
    $this->logger = $loggerChannelFactory->get('mux');
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('mux.asset_api'),
      $container->get('logger'),
    );
  }


  /**
   * Upload a video to Mux and return the asset
   *
   * @param $videoUrl
   *   The video file to be uploaded.
   *
   * @return Asset|null
   *   The asset data if the upload is successful, null otherwise.
   */
  public function uploadAndRetrieveAsset($videoUrl): ?Asset {
    try {

      // Create Asset Request
      $input = new InputSettings(["url" => $videoUrl]);
      $createAssetRequest = new CreateAssetRequest(["input" => $input, "playback_policy" => [PlaybackPolicy::_PUBLIC]]);

      // Ingest
      $result = $this->assetsApi->createAsset($createAssetRequest);

      return $result->getData();

    } catch (ApiException $e) {
      $this->logger->error('Mux API Exception: ' . $e->getMessage());
    } catch (\Exception $e) {
      $this->logger->error('Error: ' . $e->getMessage());
    }

    return null;
  }

  /**
   * Retrieve the playback id from an asset.
   *
   * @param Asset|null $asset
   *   The asset from which to retrieve the playback id.
   *
   * @return null
   *   The playback id if it exists, null otherwise.
   */
  public function getPlaybackIdFromAsset(?Asset $asset) {
    try {
      $playbackIds = $asset->getPlaybackIds();

      if (empty($playbackIds)) {
        throw new \Exception('No playback IDs found.');
      }

      return $playbackIds[0]->getId();

    } catch (\Exception $e) {
      $this->logger->error('Error: ' . $e->getMessage());

      return null;
    }
  }


  /**
   * Poll the Mux API for the asset status until it is ready
   *
   * @param string $assetId
   */
  public function pollAssetStatus(string $assetId) {
    try {
      $status = $this->assetsApi->getAsset($assetId)->getData()->getStatus();

      // Poll the Mux API until the video is ready
      while ($status !== 'ready') {
        sleep(1);
        $asset = $this->assetsApi->getAsset($assetId)->getData();
        $status = $asset->getStatus();

        if ($status === 'errored') {
          throw new \Exception('Video processing failed. ' . implode(', ', $asset->getErrors()->getMessages()));
        }
      }

    } catch (ApiException $e) {
      $this->logger->error('Mux API Exception: ' . $e->getMessage());
    } catch (\Exception $e) {
      $this->logger->error('Error: ' . $e->getMessage());
    }
  }
}
