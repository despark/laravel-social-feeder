# Social Feeder for Laravel

Social Feeder is interface to social APIs. With this package you can get latest posts/tweets/photos from Facebook, Twitter, Instagram and save it to database to show it in your application.

# Installation

Open `composer.json` file of your project and add the following to the require array:
```json
"despark/laravel-social-feeder": "~1.2"
```

Now run `composer update` to install the new requirement.

Once it's installed, you need to register the service provider in `app/config/app.php` in the providers array:
```php
'providers' => array(
  ...
  'Despark\LaravelSocialFeeder\LaravelSocialFeederServiceProvider',
);
```

Publish the config file:
`php artisan config:publish despark/laravel-social-feeder`

Then execute migration with the following command
`php artisan migrate --package="despark/laravel-social-feeder"`

This will create new table `social_posts`. In this table package store the posts from feeds.
