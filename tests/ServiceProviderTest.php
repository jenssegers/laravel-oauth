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
        $this->assertInstanceOf('Jenssegers\OAuth\Factory', $this->app['oauth']);
    }

    public function testFacade()
    {
        $this->assertInstanceOf('Jenssegers\OAuth\Factory', OAuth::getFacadeRoot());
    }

}
