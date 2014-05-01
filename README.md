Laravel OAuth
=============

[![Build Status](https://travis-ci.org/jenssegers/Laravel-OAuth.svg)](https://travis-ci.org/jenssegers/Laravel-OAuth) [![Coverage Status](https://coveralls.io/repos/jenssegers/Laravel-OAuth/badge.png)](https://coveralls.io/r/jenssegers/Laravel-OAuth)

A Laravel 4 OAuth library, using [PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib). This library uses your Laravel configured session to store tokens.

*Original code by [hannesvdvreken](https://github.com/hannesvdvreken/laravel-oauth) (not maintained).*

Installation
------------

Add the package to your `composer.json` and run `composer update`.

    {
        "require": {
            "jenssegers/oauth": "*"
        }
    }

Add the service provider in `app/config/app.php`:

    'Jenssegers\OAuth\OAuthServiceProvider',

Add the OAuth alias to `app/config/app.php`:

    'OAuth'            => 'Jenssegers\OAuth\Facades\OAuth',

Configuration
-------------

Publish the included configuration file:

    php artisan config:publish jenssegers/oauth

Add your consumer credentials to the configuration file:

    'consumers' => array(

        'Facebook' => array(
            'client_id'     => '',
            'client_secret' => '',
            'scope'         => array(),
        )

    )

You can also create a `config/oauth.php` file that contains your configuration. When the library is loaded for the first time, it will check if that file is present or not.

Usage
-----

Once you have added your credentials, you can create PHPoAuthLib service objects like this:

    $oauth = OAuth::consumer('Facebook');

To override the default redirect url, or scope use:

    $oauth = OAuth::consumer('Facebook', URL::to('url'), array('email', 'publish_actions'));

For more information check out [PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib).

Example
-------

Example usage for the Facebook API.

    $oauth = OAuth::consumer('Facebook');

    // Response from Facebook
    if ($code = Input::get('code'))
    {
        $token = $oauth->requestAccessToken($code);

        $result = json_decode($oauth->request('/me'), true);

        echo 'Your unique facebook user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
    }

    // Redirect to login
    else
    {
        return Redirect::to($oauth->getAuthorizationUri());
    }

For more examples check out [PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib).
