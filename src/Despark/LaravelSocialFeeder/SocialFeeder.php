<?php namespace Despark\LaravelSocialFeeder;

use Config;

/**
* Main class for the package
*/
class SocialFeeder {

	public static function updateTwitterPosts()
	{
		// Config::get('');

		// This should be in config
		$consumerKey = 'JvXHFRFtON6PFeqzzPKuYzqWl';
		$consumerSecret = 'C4hjh8k9TTUVqXTFjb4GdnK9IeYc4YGdpa6Hl8Uvc4G4nFuEaX';
		$accessToken = '192664878-cho3bZ0piKCJaCqQTCHl0eyFOJZo3Lj363GPmzhx';
		$accessTokenSecret = 'dhvsEOMGGmo3txrLr7lXse6rxmUe5fcM3xTxCW18jRCCN';

		$connection = new \TwitterOAuth(
			$consumerKey,
			$consumerSecret,
			$accessToken,
			$accessTokenSecret
		);

		$connection->host = 'https://api.twitter.com/1.1/';

		// Screen Name in config
		$params = array(
            'screen_name' => 'Byal_Shtark',
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
				'url' => 'https://twitter.com/Byal_Shtark/status/'.$tweet->id_str,
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
