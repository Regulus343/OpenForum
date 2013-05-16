<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumPostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('forum_posts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('section_id');
			$table->integer('thread_id');

			$table->text('content');
			$table->integer('root');

			$table->timestamps();
			$table->integer('deleted');
			$table->datetime('deleted_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('forum_posts');
	}

}