<?php namespace Despark\LaravelSocialFeeder;

use Config;

class SocialFeeder {

	public static function updateTwitterPosts()
	{
		$connection = new \TwitterOAuth(
			Config::get('laravel-social-feeder::twitterCredentials.consumerKey'),
			Config::get('laravel-social-feeder::twitterCredentials.consumerSecret'),
			Config::get('laravel-social-feeder::twitterCredentials.accessToken'),
			Config::get('laravel-social-feeder::twitterCredentials.accessTokenSecret')
		);

		$connection->host = Config::get('laravel-social-feeder::twitterCredentials.host');

		$params = array(
            'screen_name' => Config::get('laravel-social-feeder::twitterCredentials.screen_name'),
            'count' => 10,
        );

		$lastTwitterPost = \SocialPost::type('twitter')
    		->latest('published_at')
    		->limit('1')
    		->get()
    		->first();

    	if ($lastTwitterPost)
    	{
    		$params['since_id'] = $lastTwitterPost->social_id;
    	}

        try
        {
            $tweets = $connection->get('/statuses/user_timeline.json?'.http_build_query($params));
        }
        catch (Exception $e)
       	{
            $tweets = array();
        }

        foreach ($tweets as $tweet)
        {
        	if ( ! is_object($tweet))
                continue;

			$newPostData = [
	            'type' => 'twitter',
				'social_id' => $tweet->id_str,
				'url' => 'https://twitter.com/'.$params['screen_name'].'/status/'.$tweet->id_str,
				'text' => $tweet->text,
				'show_on_page' => 1,
				'published_at' => date('Y-m-d H:i:s', strtotime($tweet->created_at)),
            ];

	        $newPostEntity = new \SocialPost;
	        $newPostEntity->fill($newPostData)->save();
        }

        echo 'done';
	}
}
