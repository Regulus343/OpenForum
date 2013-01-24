<?php

use Illuminate\Database\Migrations\Migration;

class CreateForumSectionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('forum_sections', function($table)
		{
			$table->increments('id');
			$table->string('uri_tag', 32);
			$table->string('title');
			$table->text('description');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('forum_sections');
	}

}