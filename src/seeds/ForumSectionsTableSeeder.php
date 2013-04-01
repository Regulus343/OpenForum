<?php

class ForumSectionsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('forum_sections')->truncate();

		$forumSections = array(
			array(
				'uri_tag'     => 'general',
				'title'       => 'General Discussion',
				'description' => 'Anything goes here. Discuss anything you want.',
			),
			array(
				'uri_tag'     => 'questions',
				'title'       => 'Questions',
				'description' => 'Ask the community a question...',
			),
			array(
				'uri_tag'     => 'announcements',
				'title'       => 'Official Announcements',
				'description' => 'Substantial updates to the website are announced here.',
			),
		);

		foreach ($forumSections as $forumSection) {
			Regulus\OpenForum\ForumSection::create($forumSection);
		}
	}

}