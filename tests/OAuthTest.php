<?php

use Jenssegers\OAuth\Factory;

class OAuthTest extends Orchestra\Testbench\TestCase {

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return ['Jenssegers\OAuth\OAuthServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return ['OAuth' => 'Jenssegers\OAuth\Facades\OAuth'];
    }

    public function testCreatesOAuth2Consumers()
    {
        $oauth = new Factory;
        $consumers = ['Eventbrite', 'Facebook', 'Github', 'Google', 'Instagram', 'Linkedin', 'Microsoft', 'Vkontakte'];

        foreach ($consumers as $consumer)
        {
            Config::set('services.' . strtolower($consumer) . '.clientId', uniqid());
            Config::set('services.' . strtolower($consumer) . '.clientSecret', md5(uniqid()));

            $instance = $oauth->consumer($consumer);
            $this->assertInstanceOf("League\OAuth2\Client\Provider\\$consumer", $instance);
        }
    }

    public function testCreatesOAuth1Consumers()
    {
        $oauth = new Factory;
        $consumers = ['Bitbucket', 'Trello', 'Tumblr', 'Twitter'];

        foreach ($consumers as $consumer)
        {
            Config::set('services.' . strtolower($consumer) . '.identifier', uniqid());
            Config::set('services.' . strtolower($consumer) . '.secret', md5(uniqid()));

            $instance = $oauth->consumer($consumer);
            $this->assertInstanceOf("League\OAuth1\Client\Server\\$consumer", $instance);
        }
    }

    public function testOAuth2ConfigScopes()
    {
        Config::set('services.facebook.clientId', uniqid());
        Config::set('services.facebook.clientSecret', md5(uniqid()));
        Config::set('services.facebook.scopes', ['public_profile', 'email']);

        $oauth = new Factory;
        $facebook = $oauth->consumer('facebook');

        $this->assertEquals(['public_profile', 'email'], $facebook->scopes);
    }

    public function testOAuth2OverrideScopes()
    {
        Config::set('services.facebook.clientId', uniqid());
        Config::set('services.facebook.clientSecret', md5(uniqid()));
        Config::set('services.facebook.scopes', ['public_profile', 'email']);

        $oauth = new Factory;
        $facebook = $oauth->consumer('facebook', null, ['email', 'user_friends']);

        $this->assertEquals(['email', 'user_friends'], $facebook->scopes);
    }

    public function testOAuth2DefaultRedirectUri()
    {
        Config::set('services.facebook.clientId', uniqid());
        Config::set('services.facebook.clientSecret', md5(uniqid()));

        $oauth = new Factory;
        $facebook = $oauth->consumer('facebook');

        $this->assertEquals(URL::current(), $facebook->redirectUri);
    }

    public function testOAuth2CustomRedirectUri()
    {
        Config::set('services.facebook.clientId', uniqid());
        Config::set('services.facebook.clientSecret', md5(uniqid()));

        $oauth = new Factory;
        $facebook = $oauth->consumer('facebook', 'http://example.com/callback');

        $this->assertEquals('http://example.com/callback', $facebook->redirectUri);
    }

    public function testOAuth1ConfigScopes()
    {
        Config::set('services.trello.identifier', uniqid());
        Config::set('services.trello.secret', md5(uniqid()));
        Config::set('services.trello.scope', ['read', 'write']);

        $oauth = new Factory;
        $trello = $oauth->consumer('trello');

        $this->assertEquals('read,write', $trello->getApplicationScope());
    }

    public function testOAuth1OverrideScopes()
    {
        Config::set('services.trello.identifier', uniqid());
        Config::set('services.trello.secret', md5(uniqid()));
        Config::set('services.trello.scope', ['read']);

        $oauth = new Factory;
        $trello = $oauth->consumer('trello', null, ['read', 'write']);

        $this->assertEquals('read,write', $trello->getApplicationScope());
    }

    public function testOAuth1DefaultRedirectUri()
    {
        Config::set('services.twitter.identifier', uniqid());
        Config::set('services.twitter.secret', md5(uniqid()));

        $oauth = new Factory;
        $twitter = $oauth->consumer('twitter');

        $this->assertEquals(URL::current(), $twitter->getClientCredentials()->getCallbackUri());
    }

    public function testOAuth1CustomRedirectUri()
    {
        Config::set('services.twitter.identifier', uniqid());
        Config::set('services.twitter.secret', md5(uniqid()));

        $oauth = new Factory;
        $twitter = $oauth->consumer('twitter', 'http://example.com/callback');

        $this->assertEquals('http://example.com/callback', $twitter->getClientCredentials()->getCallbackUri());
    }

}
