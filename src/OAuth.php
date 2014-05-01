<?php namespace Jenssegers\OAuth;

use \App;
use \Config;
use \URL;
use \OAuth\ServiceFactory;
use \OAuth\Common\Consumer\Credentials;
use \OAuth\Common\Storage\SymfonySession;

class OAuth
{
    /**
     * OAuth Service Factory
     *
     * @var \OAuth\ServiceFactory
     */
    protected $factory;

    /**
     * The prefix used to get the configuration.
     *
     * @var string
     */
    protected $prefix = 'oauth::';

    /**
     * Constructor.
     *
     * @param \OAuth\ServiceFactory $factory
     */
    public function __construct(ServiceFactory $factory)
    {
        // Dependency injection
        $this->factory = $factory;

        // Here we check what configuration file is used before we start. By default,
        // we check the included package configuration file, but if a "custom"
        // config/oauth.php file is detected, we will use that one.
        if (Config::has('oauth.consumers'))
        {
            $this->prefix = 'oauth.';
        }

        // Set custom HTTP client
        if ($client = Config::get($this->prefix . 'client'))
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
        // Create credentials object
        $credentials = new Credentials(
            Config::get($this->prefix . "consumers.$service.client_id"),
            Config::get($this->prefix . "consumers.$service.client_secret"),
            $url ?: URL::current()
        );

        // Create Laravel based session
        $session = App::make('session')->driver();
        $storage = new SymfonySession($session);

        // Get default scope
        if (is_null($scope))
        {
            $scope = Config::get($this->prefix . "consumers.$service.scope", array());
        }

        return $this->factory->createService($service, $credentials, $storage, $scope);
    }

}
