<?php namespace Jenssegers\OAuth;

use App, Config, URL;

class Factory {

    /**
     * Create an OAuth consumer.
     *
     * @param  string $service
     * @param  string $url
     * @param  array  $scope
     * @return mixed
     */
    public function consumer($service, $url = null, array $scope = [])
    {
        // Get service configuration
        $config = Config::get('services.' . strtolower($service));

        // Possible classes
        $oauth1 = "\\League\\OAuth1\\Client\\Server\\$service";
        $oauth2 = "\\League\\OAuth2\\Client\\Provider\\$service";

        if (class_exists($oauth2))
        {
            $config['redirectUri'] = $url ?: array_get($config, 'redirectUri', URL::current());
            $config['scopes'] = $scope ?: array_get($config, 'scopes', []);

            return new $oauth2($config);
        }
        else if (class_exists($oauth1))
        {
            $config['callback_uri'] = $url ?: array_get($config, 'callback_uri', URL::current());
            $config['scope'] = $scope ?: array_get($config, 'scope', '');

            // Convert scopes array to string
            if (is_array($config['scope']))
            {
                $config['scope'] = implode(',', $config['scope']);
            }

            return new $oauth1($config);
        }
    }

    /**
     * Consumer alias method.
     *
     * @param  string $service
     * @param  string $url
     * @param  array  $scope
     * @return \OAuth\Common\Service\AbstractService
     */
    public function service($service, $url = null, $scope = null)
    {
        return $this->consumer($service, $url, $scope);
    }

    /**
     * Consumer alias method.
     *
     * @param  string $service
     * @param  string $url
     * @param  array  $scope
     * @return \OAuth\Common\Service\AbstractService
     */
    public function provider($service, $url = null, $scope = null)
    {
        return $this->consumer($service, $url, $scope);
    }

}
