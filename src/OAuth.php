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
     * The prefix used to get the configuration.
     *
     * @var string
     */
    protected $prefix = 'services';

    /**
     * Constructor.
     *
     * @param \OAuth\ServiceFactory $factory
     */
    public function __construct(ServiceFactory $factory, TokenStorageInterface $storage)
    {
        // Dependency injection
        $this->factory = $factory;
        $this->storage = $storage;

        // Here we check what consumer configuration file is used before we start.
        // By default, we will use the Laravel services file, but we will check
        // other files such as the included package configuration file.
        if (Config::get('oauth::consumers'))
        {
            $this->prefix = 'oauth::consumers';
        }
        else if (Config::get('oauth.consumers'))
        {
            $this->prefix = 'oauth.consumers';
        }

        // Set HTTP client
        if ($client = Config::get('oauth.client') ?: Config::get('oauth::client'))
        {
            $class = '\OAuth\Common\Http\Client\\' . $client;
            $this->factory->setHttpClient(new $class);
        }
    }

    /**
     * Create an OAuth consumer.
     *
     * @param  string $service
     * @param  string $url
     * @param  array  $scope
     * @return \OAuth\Common\Service\AbstractService
     */
    public function consumer($service, $url = null, $scope = null)
    {
        // Create credentials object.
        $credentials = new Credentials(
            Config::get($this->prefix . ".$service.client_id"),
            Config::get($this->prefix . ".$service.client_secret"),
            $url ?: URL::current()
        );

        // Get default scope.
        if (is_null($scope))
        {
            $scope = Config::get($this->prefix . ".$service.scope", array());
        }

        return $this->factory->createService($service, $credentials, $this->storage, $scope);
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
