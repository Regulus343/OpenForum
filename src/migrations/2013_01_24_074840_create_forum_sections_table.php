<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumSectionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('forum_sections', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('slug', 64);
			$table->string('title');
			$table->text('description');
			$table->boolean('admin_create_thread');
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