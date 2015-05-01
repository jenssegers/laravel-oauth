<?php namespace Jenssegers\OAuth;

use App, Config, URL;
use OAuth\ServiceFactory;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;

class OAuth {

    /**
     * OAuth Service Factory instance.
     *
     * @var \OAuth\ServiceFactory
     */
    protected $factory;

    /**
     * The storage instance.
     * @var \OAuth\Common\Storage\TokenStorageInterface
     */
    protected $storage;

    /**
     * Constructor.
     *
     * @param \OAuth\ServiceFactory $factory
     */
    public function __construct(ServiceFactory $factory, TokenStorageInterface $storage, $httpClient = null)
    {
        // Dependency injection
        $this->factory = $factory;
        $this->storage = $storage;

        // Set HTTP client
        if ($httpClient)
        {
            $this->setHttpClient($httpClient);
        }
    }

    /**
     * Create an OAuth consumer.
     *
     * @param  string $service
     * @param  string $url
     * @param  array  $scope
     * @param  string $baseUri
     * @param  string $version
     * @return \OAuth\Common\Service\AbstractService
     */
    public function consumer($service, $url = null, $scope = null, $baseUri = null, $version = null)
    {
        // Get service configuration
        $config = Config::get('services.' . strtolower($service));

        // Create credentials object.
        $credentials = new Credentials(
            array_get($config, 'client_id'),
            array_get($config, 'client_secret'),
            $url ?: URL::current()
        );

        // Get default scope.
        if (is_null($scope))
        {
            $scope = array_get($config, 'scope', []);
        }

        // Get default base uri.
        if (is_null($baseUri))
        {
            $baseUri = array_get($config, 'baseUri');
        }

        // Get default api version.
        if (is_null($version))
        {
            $version = array_get($config, 'version', '');
        }

        return $this->factory->createService($service, $credentials, $this->storage, $scope, $baseUri, $version);
    }

    /**
     * Consumer alias method.
     *
     * @param  string $service
     * @param  string $url
     * @param  array  $scope
     * @param  string $baseUri
     * @param  string $version
     * @return \OAuth\Common\Service\AbstractService
     */
    public function service($service, $url = null, $scope = null, $baseUri = null, $version = null)
    {
        return $this->consumer($service, $url, $scope, $baseUri, $version);
    }

    /**
     * Set the HTTP client.
     *
     * @param string $client
     */
    public function setHttpClient($client)
    {
        if ( ! in_array($client, ['StreamClient', 'CurlClient']))
        {
            throw new \InvalidArgumentException('Invalid HTTP client');
        }

        $class = '\OAuth\Common\Http\Client\\' . $client;

        $this->factory->setHttpClient(new $class);
    }

    /**
     * Handle dynamic method calls.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->factory, $method), $parameters);
    }

}
