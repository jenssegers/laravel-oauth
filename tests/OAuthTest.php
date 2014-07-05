<?php

use Jenssegers\OAuth\OAuth;
use OAuth\Common\Storage\Memory;

class OAuthProviderTest extends Orchestra\Testbench\TestCase {

    public function tearDown()
    {
        Mockery::close();
    }

    protected function getPackageProviders()
    {
        return array('Jenssegers\OAuth\OAuthServiceProvider');
    }

    protected function getPackageAliases()
    {
        return array(
            'OAuth' => 'Jenssegers\OAuth\Facades\OAuth'
        );
    }

    public function testDefaultConfiguration()
    {
        $this->assertNotNull(Config::get('oauth::client'));
        $this->assertNotNull(Config::get('oauth::consumers'));
    }

    public function testSetsHttpClient()
    {
        Config::set('oauth::client', 'CurlClient');

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $serviceFactory->shouldReceive('setHttpClient')->times(1);
        $oauth = new OAuth($serviceFactory, new Memory);
    }

    public function testMagicCalls()
    {
        Config::set('oauth::client', '');

        $client = new \OAuth\Common\Http\Client\CurlClient;
        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $serviceFactory->shouldReceive('setHttpClient')->with($client)->times(1);

        $oauth = new OAuth($serviceFactory, new Memory);
        $oauth->setHttpClient($client);
    }

    public function testDefaultHttpClient()
    {
        Config::set('oauth::client', '');

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $serviceFactory->shouldReceive('setHttpClient')->times(0);
        $oauth = new OAuth($serviceFactory, new Memory);
    }

    public function testCreatesConsumer()
    {
        Config::set('oauth::consumers.facebook.client_id', '123');
        Config::set('oauth::consumers.facebook.client_secret', 'ABC');

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory[createService]');
        $serviceFactory->shouldReceive('createService')->passthru();

        $oauth = new OAuth($serviceFactory, new Memory);
        $consumer = $oauth->consumer('facebook');
        $this->assertInstanceOf('OAuth\OAuth2\Service\Facebook', $consumer);
    }

    public function testServiceAlias()
    {
        Config::set('oauth::consumers.facebook.client_id', '123');
        Config::set('oauth::consumers.facebook.client_secret', 'ABC');

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory[createService]');
        $serviceFactory->shouldReceive('createService')->passthru();

        $oauth = new OAuth($serviceFactory, new Memory);
        $consumer = $oauth->service('facebook');
        $this->assertInstanceOf('OAuth\OAuth2\Service\Facebook', $consumer);
    }

    public function testReturnsConsumer()
    {
        Config::set('oauth::consumers.facebook.client_id', '123');
        Config::set('oauth::consumers.facebook.client_secret', 'ABC');
        Config::set('oauth::consumers.facebook.scope', array());

        $oauth = App::make('oauth');
        $consumer = $oauth->consumer('facebook', 'foo.bar.com', array('email', 'publish_actions'));
        $this->assertInstanceOf('OAuth\OAuth2\Service\Facebook', $consumer);

        $uri = (string) $consumer->getAuthorizationUri();
        $this->assertContains('client_id=123', $uri);
        $this->assertContains('redirect_uri=foo.bar.com', $uri);
        $this->assertContains('scope=email+publish_actions', $uri);
    }

    public function testReturnsDefaultConsumer()
    {
        Config::set('oauth::consumers.facebook.client_id', '123');
        Config::set('oauth::consumers.facebook.client_secret', 'ABC');
        Config::set('oauth::consumers.facebook.scope', array('email', 'publish_actions'));

        $oauth = App::make('oauth');
        $consumer = $oauth->consumer('facebook');
        $this->assertInstanceOf('OAuth\OAuth2\Service\Facebook', $consumer);

        $uri = (string) $consumer->getAuthorizationUri();
        $this->assertContains('client_id=123', $uri);
        $this->assertContains('redirect_uri=' . urlencode(URL::current()), $uri);
        $this->assertContains('scope=email+publish_actions', $uri);
    }

    public function testSharesLaravelSession()
    {
        $oauth = App::make('oauth');
        $consumer = $oauth->consumer('facebook');
        $storage = $consumer->getStorage();
        $session = $storage->getSession();

        $session->set('foo', 'bar');
        $this->assertEquals('bar', Session::get('foo'));
    }

    public function testCustomConfigurationFile()
    {
        Config::set('oauth.client', 'CurlClient');
        Config::set('oauth.consumers.facebook.client_id', '123');
        Config::set('oauth.consumers.facebook.client_secret', 'ABC');
        Config::set('oauth.consumers.facebook.scope', array('email', 'publish_actions'));

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $serviceFactory->shouldReceive('setHttpClient')->times(1);
        $oauth = new OAuth($serviceFactory, new Memory);

        $oauth = App::make('oauth');
        $consumer = $oauth->consumer('facebook');
        $this->assertInstanceOf('OAuth\OAuth2\Service\Facebook', $consumer);

        $uri = (string) $consumer->getAuthorizationUri();
        $this->assertContains('client_id=123', $uri);
        $this->assertContains('redirect_uri=' . urlencode(URL::current()), $uri);
        $this->assertContains('scope=email+publish_actions', $uri);
    }

    public function testServicesConfiguration()
    {
        Config::set('services.facebook.client_id', '789');
        Config::set('services.facebook.client_secret', 'XYZ');
        Config::set('services.facebook.scope', array('email', 'publish_actions'));

        $oauth = App::make('oauth');
        $consumer = $oauth->consumer('facebook');
        $this->assertInstanceOf('OAuth\OAuth2\Service\Facebook', $consumer);

        $uri = (string) $consumer->getAuthorizationUri();
        $this->assertContains('client_id=789', $uri);
        $this->assertContains('redirect_uri=' . urlencode(URL::current()), $uri);
        $this->assertContains('scope=email+publish_actions', $uri);
    }

}
