<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialPostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_posts', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('type');
			$table->string('title')->nullable();
			$table->text('text');
			$table->string('social_id');
			$table->string('url');
			$table->string('image_url')->nullable();
			$table->boolean('show_on_page');
			$table->timestamp('published_at');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('social_posts');
	}

}
