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
     * Constructor.
     *
     * @param \OAuth\ServiceFactory $factory
     */
    public function __construct(ServiceFactory $factory)
    {
        // Dependency injection
        $this->factory = $factory;

        // Set custom HTTP client
        if ($client = Config::get('oauth.client'))
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
            Config::get("oauth.consumers.$service.client_id"),
            Config::get("oauth.consumers.$service.client_secret"),
            $url ?: URL::current()
        );

        // Create Laravel based session
        $session = App::make('session')->driver();
        $storage = new SymfonySession($session);

        // Get default scope
        if (is_null($scope))
        {
            $scope = Config::get("oauth.consumers.$service.scope", array());
        }

        return $this->factory->createService($service, $credentials, $storage, $scope);
    }

}
