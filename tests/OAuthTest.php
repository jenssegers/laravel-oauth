<?php

use Jenssegers\OAuth\OAuth;
use OAuth\Common\Storage\Memory;

class OAuthProviderTest extends Orchestra\Testbench\TestCase {

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return array('Jenssegers\OAuth\OAuthServiceProvider');
    }

    protected function getPackageAliases($app)
    {
        return array(
            'OAuth' => 'Jenssegers\OAuth\Facades\OAuth'
        );
    }

    public function testSetsHttpClient()
    {
        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $serviceFactory->shouldReceive('setHttpClient')->with(Mockery::type('OAuth\\Common\\Http\\Client\\CurlClient'))->times(1);

        $oauth = new OAuth($serviceFactory, new Memory);
        $oauth->setHttpClient('CurlClient');
    }

    public function testInvalidHttpClient()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid HTTP client');

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $oauth = new OAuth($serviceFactory, new Memory);
        $oauth->setHttpClient('FooBarClient');
    }

    public function testDefaultHttpClient()
    {
        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $serviceFactory->shouldReceive('setHttpClient')->times(0);

        $oauth = new OAuth($serviceFactory, new Memory);
    }

    public function testCreatesConsumer()
    {
        Config::set('services.facebook.client_id', '123');
        Config::set('services.facebook.client_secret', 'ABC');
        Config::set('services.facebook.version', 'v2.2');

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory[createService]');
        $serviceFactory->shouldReceive('createService')->passthru();

        $oauth = new OAuth($serviceFactory, new Memory);
        $consumer = $oauth->consumer('facebook');
        $this->assertInstanceOf('OAuth\OAuth2\Service\Facebook', $consumer);
    }

    public function testReturnsConsumer()
    {
        Config::set('services.facebook.client_id', '123');
        Config::set('services.facebook.client_secret', 'ABC');
        Config::set('services.facebook.scope', array());
        Config::set('services.facebook.version', 'v2.2');

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $serviceFactory->shouldReceive('createService')->passthru();
        $oauth = new OAuth($serviceFactory, new Memory);

        $consumer = $oauth->consumer('facebook', 'foo.bar.com', array('email', 'publish_actions'));
        $this->assertInstanceOf('OAuth\OAuth2\Service\Facebook', $consumer);

        $uri = (string) $consumer->getAuthorizationUri();
        $this->assertContains('client_id=123', $uri);
        $this->assertContains('redirect_uri=foo.bar.com', $uri);
        $this->assertContains('scope=email+publish_actions', $uri);
        $this->assertContains('facebook.com/v2.2/dialog/oauth', $uri);
    }

    public function testReturnsDefaultConsumer()
    {
        Config::set('services.facebook.client_id', '123');
        Config::set('services.facebook.client_secret', 'ABC');
        Config::set('services.facebook.scope', array('email', 'publish_actions'));
        Config::set('services.facebook.version', 'v2.2');

        $serviceFactory = Mockery::mock('OAuth\ServiceFactory');
        $serviceFactory->shouldReceive('createService')->passthru();
        $oauth = new OAuth($serviceFactory, new Memory);

        $consumer = $oauth->consumer('facebook');
        $this->assertInstanceOf('OAuth\OAuth2\Service\Facebook', $consumer);

        $uri = (string) $consumer->getAuthorizationUri();
        $this->assertContains('client_id=123', $uri);
        $this->assertContains('redirect_uri=' . urlencode(URL::current()), $uri);
        $this->assertContains('scope=email+publish_actions', $uri);
        $this->assertContains('facebook.com/v2.2/dialog/oauth', $uri);
    }

}
