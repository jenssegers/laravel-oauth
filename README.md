Laravel OAuth
=============

[![Build Status](http://img.shields.io/travis/jenssegers/laravel-oauth.svg)](https://travis-ci.org/jenssegers/laravel-oauth) [![Coverage Status](http://img.shields.io/coveralls/jenssegers/laravel-oauth.svg)](https://coveralls.io/r/jenssegers/laravel-oauth)

A Laravel OAuth 1 and 2 library, using [PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib). This library shares the Laravel session to store tokens and supports the services configuration file that was introduced in Laravel.

Supported services
------------------

- OAuth1
    - BitBucket
    - Etsy
    - FitBit
    - Flickr
    - Scoop.it!
    - Tumblr
    - Twitter
    - Xing
    - Yahoo
- OAuth2
    - Amazon
    - BitLy
    - Box
    - Dailymotion
    - Dropbox
    - Facebook
    - Foursquare
    - GitHub
    - Google
    - Harvest
    - Heroku
    - Instagram
    - Jawbone UP
    - LinkedIn
    - Mailchimp
    - Microsoft
    - PayPal
    - Pocket
    - Reddit
    - RunKeeper
    - SoundCloud
    - Spotify
    - Ustream
    - Vkontakte
    - Yammer

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
        'client_id'     => 'your-client-id',
        'client_secret' => 'your-client-secret',
        'scope'         => [],
    ]

Usage
-----

Once you have added your credentials, you can create PHPoAuthLib service objects like this:

    $oauth = OAuth::consumer('facebook');

To override the default redirect url, or scope use:

    $oauth = OAuth::consumer('facebook', URL::to('url'), ['email', 'publish_actions']);

Once you have the service object, you can use it to interact with the service's API. For more information check out [PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib).

Example
-------

Example usage for the Facebook API.

    $facebook = OAuth::consumer('facebook');

    // Response from Facebook
    if ($code = Input::get('code'))
    {
        $token = $facebook->requestAccessToken($code);

        $result = json_decode($facebook->request('/me'), true);

        echo 'Your unique facebook user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
    }

    // Redirect to login
    else
    {
        return Redirect::away((string) $facebook->getAuthorizationUri());
    }

For more examples check out [PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib/tree/master/examples).
