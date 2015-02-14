Laravel OAuth
=============

[![Build Status](http://img.shields.io/travis/jenssegers/laravel-oauth.svg)](https://travis-ci.org/jenssegers/laravel-oauth) [![Coverage Status](http://img.shields.io/coveralls/jenssegers/laravel-oauth.svg)](https://coveralls.io/r/jenssegers/laravel-oauth)

A Laravel OAuth1 and OAuth2 library, using [thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client) and [thephpleague/oauth1-client](https://github.com/thephpleague/oauth1-client). This package allows you to easily construct different OAuth consumers using Laravel's service configuration file.

Supported services
------------------

- OAuth1
    - BitBucket
    - Trello
    - Tumblr
    - Twitter
- OAuth2
    - Eventbrite
    - Facebook
    - Github
    - Google
    - Instagram
    - LinkedIn
    - Microsoft
- Third party OAuth2
    - [Battle.net](https://packagist.org/packages/depotwarehouse/oauth2-bnet)
    - [Dropbox](https://github.com/pixelfear/oauth2-dropbox)
    - [FreeAgent](https://github.com/CloudManaged/oauth2-freeagent)
    - [Google Nest](https://github.com/JC5/nest-oauth2-provider)
    - [Mail.ru](https://packagist.org/packages/aego/oauth2-mailru)
    - [Meetup](https://github.com/howlowck/meetup-oauth2-provider)
    - [Naver](https://packagist.org/packages/deminoth/oauth2-naver)
    - [Odnoklassniki](https://packagist.org/packages/aego/oauth2-odnoklassniki)
    - [Twitch.tv](https://github.com/tpavlek/oauth2-twitch)
    - [Vkontakte](https://packagist.org/packages/j4k/oauth2-vkontakte)
    - [Yandex](https://packagist.org/packages/aego/oauth2-yandex)

Installation
------------

Install using composer:

    composer require jenssegers/oauth

Add the service provider in `app/config/app.php`:

    'Jenssegers\OAuth\OAuthServiceProvider',

Add the OAuth alias to `app/config/app.php`:

    'OAuth'            => 'Jenssegers\OAuth\Facades\OAuth',

Configuration
-------------

This package supports configuration through the services configuration file located in `config/services.php`:

    'facebook' => [
        'clientId'     => 'your-client-id',
        'clientSecret' => 'your-client-secret',
        'redirectUri'  => 'https://example.com/callback',
        'scopes'       => ['public_profile', 'email'],
    ],

    'twitter' => [
        'identifier'   => 'your-identifier',
        'secret'       => 'your-secret',
        'callback_uri' => 'https://example.com/callback',
        'scope'        => ['public_profile', 'email'],
    ]

For more information about the possible configuration paramters, check out [thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client) and [thephpleague/oauth1-client](https://github.com/thephpleague/oauth1-client).

Usage
-----

Once you have added your credentials, you can create OAuth service objects like this:

    $oauth = OAuth::consumer('facebook');

To override the default redirect url, or scope use:

    $oauth = OAuth::consumer('facebook', URL::route('oauth2-callback'), ['email', 'publish_actions']);

Once you have the service object, you can use it to interact with the service's API. For more information check out [thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client) and [thephpleague/oauth1-client](https://github.com/thephpleague/oauth1-client).

Example
-------

Example usage for the Facebook API.

    $facebook = OAuth::consumer('facebook');

    // Response from Facebook
    if ($code = Input::get('code'))
    {
        $token = $facebook->getAccessToken('authorization_code', ['code' => $code]);

        $user = $facebook->getUserDetails($token);

        echo 'Your unique facebook user id is: ' . $user->uid . ' and your name is ' . $user->name;
    }

    // Redirect to login
    else
    {
        return Redirect::away((string) $facebook->getAuthorizationUrl());
    }

For more examples check out [thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client) and [thephpleague/oauth1-client](https://github.com/thephpleague/oauth1-client).
