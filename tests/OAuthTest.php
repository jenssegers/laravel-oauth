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
        Config::shouldReceive('has')->with('oauth.consumers')->once()->andReturn(false);
        Config::shouldReceive('get')->with('oauth::client')->once()->andReturn('CurlClient');

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $serviceFactory->shouldReceive('setHttpClient')->times(1);
        $oauth = new OAuth($serviceFactory, new Memory);
    }

    public function testDefaultHttpClient()
    {
        Config::shouldReceive('has')->with('oauth.consumers')->once()->andReturn(false);
        Config::shouldReceive('get')->with('oauth::client')->once()->andReturn(null);

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $oauth = new OAuth($serviceFactory, new Memory);
    }

    public function testCreatesConsumer()
    {
        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $serviceFactory->shouldReceive('createService');

        $oauth = new OAuth($serviceFactory, new Memory);
        $consumer = $oauth->consumer('Facebook');
    }

    public function testReturnsConsumer()
    {
        Config::set('oauth::consumers.Facebook.client_id', '123');
        Config::set('oauth::consumers.Facebook.client_secret', 'ABC');

        $oauth = App::make('oauth');
        $consumer = $oauth->consumer('Facebook', 'foo.bar.com', array('email', 'publish_actions'));
        $this->assertInstanceOf('OAuth\OAuth2\Service\Facebook', $consumer);

        $uri = (string) $consumer->getAuthorizationUri();
        $this->assertContains('client_id=123', $uri);
        $this->assertContains('redirect_uri=foo.bar.com', $uri);
        $this->assertContains('scope=email+publish_actions', $uri);
    }

    public function testReturnsDefaultConsumer()
    {
        Config::set('oauth::consumers.Facebook.client_id', '123');
        Config::set('oauth::consumers.Facebook.client_secret', 'ABC');

        $oauth = App::make('oauth');
        $consumer = $oauth->consumer('Facebook');
        $this->assertInstanceOf('OAuth\OAuth2\Service\Facebook', $consumer);

        $uri = (string) $consumer->getAuthorizationUri();
        $this->assertContains('client_id=123', $uri);
        $this->assertContains('redirect_uri=' . urlencode(URL::current()), $uri);
    }

    public function testSharesLaravelSession()
    {
        $oauth = App::make('oauth');
        $consumer = $oauth->consumer('Facebook');
        $storage = $consumer->getStorage();
        $session = $storage->getSession();

        $session->set('foo', 'bar');
        $this->assertEquals('bar', Session::get('foo'));
    }

    public function testCustomConfigurationFile()
    {
        Config::shouldReceive('has')->with('oauth.consumers')->once()->andReturn(true);
        Config::shouldReceive('get')->with('oauth.client')->once()->andReturn('CurlClient');

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $serviceFactory->shouldReceive('setHttpClient')->times(1);
        $oauth = new OAuth($serviceFactory, new Memory);
    }

}
