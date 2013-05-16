<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumThreadViews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('forum_thread_views', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('thread_id');
			$table->integer('user_id');
			$table->string('ip_address');
			$table->string('user_agent');
			$table->timestamp('created_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('forum_thread_views');
	}

}