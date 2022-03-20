<?php

/**
 * Test functions in wp-includes/user.php
 *
 * @group user
 */
class Tests_User extends WP_UnitTestCase {
	protected static $admin_id;
	protected static $editor_id;
	protected static $author_id;
	protected static $contrib_id;
	protected static $sub_id;

	protected static $user_ids = array();

	protected static $_author;
	protected $author;
	protected $user_data;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$contrib_id = $factory->user->create(
			array(
				'user_login'    => 'user1',
				'user_nicename' => 'userone',
				'user_pass'     => 'password',
				'first_name'    => 'John',
				'last_name'     => 'Doe',
				'display_name'  => 'John Doe',
				'user_email'    => 'blackburn@battlefield3.com',
				'user_url'      => 'http://tacos.com',
				'role'          => 'contributor',
				'nickname'      => 'Johnny',
				'description'   => 'I am a WordPress user that cares about privacy.',
			)
		);
		self::$user_ids[] = self::$contrib_id;

		self::$author_id  = $factory->user->create(
			array(
				'user_login' => 'author_login',
				'user_email' => 'author@email.com',
				'role'       => 'author',
			)
		);
		self::$user_ids[] = self::$author_id;

		self::$admin_id   = $factory->user->create( array( 'role' => 'administrator' ) );
		self::$user_ids[] = self::$admin_id;
		self::$editor_id  = $factory->user->create(
			array(
				'user_email' => 'test@test.com',
				'role'       => 'editor',
			)
		);
		self::$user_ids[] = self::$editor_id;
		self::$sub_id     = $factory->user->create( array( 'role' => 'subscriber' ) );
		self::$user_ids[] = self::$sub_id;

		self::$_author = get_user_by( 'ID', self::$author_id );
	}

	public function set_up() {
		parent::set_up();

		$this->author = clone self::$_author;
	}
public function test_update_user() {
		$user = new WP_User( self::$author_id );

		update_user_meta( self::$author_id, 'description', 'about me' );
		$this->assertSame( 'about me', $user->get( 'description' ) );

		$user_data = array(
			'ID'           => self::$author_id,
			'display_name' => 'test user',
		);
		wp_update_user( $user_data );

		$user = new WP_User( self::$author_id );
		$this->assertSame( 'test user', $user->get( 'display_name' ) );

		// Make sure there is no collateral damage to fields not in $user_data.
		$this->assertSame( 'about me', $user->get( 'description' ) );

		// Pass as stdClass.
		$user_data = array(
			'ID'           => self::$author_id,
			'display_name' => 'a test user',
		);
		wp_update_user( (object) $user_data );

		$user = new WP_User( self::$author_id );
		$this->assertSame( 'a test user 123', $user->get( 'display_name' ) );

		$user->display_name = 'some test user';
		wp_update_user( $user );

		$this->assertSame( 'some test user', $user->get( 'display_name' ) );

		// Test update of fields in _get_additional_user_keys().
		$user_data = array(
			'ID'                   => self::$author_id,
			'use_ssl'              => 1,
			'show_admin_bar_front' => 1,
			'rich_editing'         => 1,
			'syntax_highlighting'  => 1,
			'first_name'           => 'first',
			'last_name'            => 'last',
			'nickname'             => 'nick',
			'comment_shortcuts'    => 'true',
			'admin_color'          => 'classic',
			'description'          => 'describe',
		);
		wp_update_user( $user_data );

		$user = new WP_User( self::$author_id );
		foreach ( $user_data as $key => $value ) {
			$this->assertEquals( $value, $user->get( $key ), $key );
		}
	}


}
