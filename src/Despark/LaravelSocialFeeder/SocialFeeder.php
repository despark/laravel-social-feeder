<?php namespace Despark\LaravelSocialFeeder;

use Config;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Facebook;

class SocialFeeder {

	public static function updateTwitterPosts()
	{
        $connection = new \Abraham\TwitterOAuth\TwitterOAuth(
            Config::get('laravel-social-feeder::twitterCredentials.consumerKey'),
            Config::get('laravel-social-feeder::twitterCredentials.consumerSecret'),
            Config::get('laravel-social-feeder::twitterCredentials.accessToken'),
            Config::get('laravel-social-feeder::twitterCredentials.accessTokenSecret')
        );

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
            $tweets = $connection->get('statuses/user_timeline', $params);
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

        return true;
	}

    public static function updateFacebookPosts()
    {
        Facebook::setAccessToken(Config::get('laravel-social-feeder::facebookCredentials.accessToken'));

        $pageName = Config::get('laravel-social-feeder::facebookCredentials.pageName');

        $posts = Facebook::object($pageName.'/posts')->get()->all();

        $lastPost = \SocialPost::type('facebook')->orderBy('published_at', 'DESC')->first();

        foreach ($posts as $post)
        {
            $published_at = date('Y-m-d H:i:s', $post->get('created_time')->timestamp);

            if ($lastPost and $lastPost->published_at >= $published_at)
                continue;

            if ( ! $post->get('message'))
                continue;

            $socialId = array_get(explode('_', $post->get('id')), 1);

            $newPostData = array(
                'type' => 'facebook',
                'social_id' => $socialId,
                'url' => 'https://www.facebook.com/'.$pageName.'/posts/'.$socialId,
                'text' => $post->get('message'),
                'image_url' => $post->get('picture'),
                'show_on_page' => 1,
                'published_at' => $published_at,
            );

            $newPostEntity = new \SocialPost;
            $newPostEntity->fill($newPostData)->save();
        }

        return true;
    }

    public static function updateInstagramPosts()
    {
        $lastInstagramPost = \SocialPost::type('instagram')->latest('published_at')->get()->first();
        $lastInstagramPostTimestamp = $lastInstagramPost ? strtotime($lastInstagramPost->published_at) : 0;

        $clientId = Config::get('laravel-social-feeder::instagramCredentials.clientId');
        $userId = Config::get('laravel-social-feeder::instagramCredentials.userId');

        $url = 'https://api.instagram.com/v1/users/'.$userId.'/media/recent?count=5&client_id='.$clientId;
        $json = file_get_contents($url);

        $obj = json_decode($json);

        $postsData = $obj->data;

        foreach ($postsData as $post)
        {
            if ( $post->caption->created_time <= $lastInstagramPostTimestamp)
                continue;

            $newPostData = array(
                'type' => 'instagram',
                'social_id' => $post->caption->id,
                'url' => $post->link,
                'text' => $post->caption->text,
                'image_url' => $post->images->standard_resolution->url,
                'show_on_page' => 1,
                'published_at' => date('Y-m-d H:i:s', $post->caption->created_time),
            );

            $newPostEntity = new \SocialPost;
            $newPostEntity->fill($newPostData)->save();
        }

        return true;
    }
}
