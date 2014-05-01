<?php

class ServiceProviderTest extends Orchestra\Testbench\TestCase {

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

    public function testBind()
    {
        $this->assertInstanceOf('Jenssegers\OAuth\OAuth', $this->app['oauth']);
    }

    public function testFacade()
    {
        $this->assertInstanceOf('Jenssegers\OAuth\OAuth', OAuth::getFacadeRoot());
    }

}
