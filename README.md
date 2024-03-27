# drupal-module-mux

This module provides a service for generating Mux Assets API clients in a Drupal project. It provides custom functions for uploading videos to Mux and retrieving playback ids, as well as polling the Mux API for an assets status.

## Installation


## Usage

To use this module, simply call the `mux.assets_api` service:

```php
$assetsApi = \Drupal::service('mux.assets_api');
```

This will return an instance of `MuxPhp\Api\AssetsApi`, authenticated with the Mux API username and password from the site's configuration.

You can then use this client to interact with the Mux Assets API, for example:

```php
$asset = $assetsApi->getAsset($assetId);
```

Please refer to the [Mux Assets API documentation](https://docs.mux.com/api-reference/video#operation/get-asset) for more information on the available methods and their usage.

## Custom Functions

- `uploadAndRetrieveAsset($videoUrl): ?Asset`: This function takes a video URL as an argument and uploads the video to Mux as a new asset. If the upload is successful, it returns the asset.

- `getPlaybackIdFromAsset(?Asset $asset)`: This function takes an asset as an argument and retrieves the playback id from it.

- `pollAssetStatus(string $assetId)`: This function takes an asset id as an argument and polls the Mux API for the asset's status every second until the status is 'ready'. It cancels the polling if the status is 'errored'.

These functions provide a simple and effective way to interact with the Mux Assets API, allowing for video upload and retrieval of playback ids. They also handle error scenarios, ensuring that the application can gracefully handle any issues that may occur during the interaction with the Mux API.

