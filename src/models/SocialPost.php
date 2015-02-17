<?php

class SocialPost extends Eloquent {

	protected $table = 'social_posts';

	protected $fillable = ['type', 'title', 'text', 'social_id', 'url', 'image_url', 'show_on_page', 'published_at',];

	public function scopeType($query, $type)
    {
        return $query->whereType($type);
    }

    public function scopeVisible($query)
    {
    	return $query->where('show_on_page', '=', 1);
    }

    public function getDates()
    {
        return array('published_at', 'created_at', 'updated_at');
    }
}
