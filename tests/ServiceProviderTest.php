<?php

class ServiceProviderTest extends Orchestra\Testbench\TestCase {

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

    public function testBind()
    {
        $this->assertInstanceOf('Jenssegers\OAuth\OAuth', $this->app['oauth']);
    }

    public function testFacade()
    {
        $this->assertInstanceOf('Jenssegers\OAuth\OAuth', OAuth::getFacadeRoot());
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

}
