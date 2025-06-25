<?php
/**
 * The BasePostTest class file.
 *
 * @package    Mazepress\Post
 * @subpackage Tests
 */

declare(strict_types=1);

namespace Mazepress\Post\Tests;

use Mazepress\Post\BasePost;
use Mazepress\Post\PostMeta;
use Mazepress\Post\Tests\HelloWorld;
use WP_Mock\Tools\TestCase;
use WP_Mock;


/**
 * The BasePostTest class.
 */
class BasePostTest extends TestCase {

	/**
	 * Test class properites.
	 *
	 * @return void
	 */
	public function test_properties(): void {

		$object = new HelloWorld();

		$this->assertInstanceOf( BasePost::class, $object );
		$this->assertEquals( 'hello-world', $object->get_post_type() );
		$this->assertInstanceOf( HelloWorld::class, $object->set_id( 123 ) );
		$this->assertEquals( 123, $object->get_id() );

		$this->assertInstanceOf( HelloWorld::class, $object->set_title( 'title' ) );
		$this->assertEquals( 'title', $object->get_title() );

		$this->assertInstanceOf( HelloWorld::class, $object->set_slug( 'slug' ) );
		$this->assertEquals( 'slug', $object->get_slug() );

		$this->assertInstanceOf( HelloWorld::class, $object->set_content( 'content' ) );
		$this->assertEquals( 'content', $object->get_content() );

		$this->assertInstanceOf( HelloWorld::class, $object->set_status( 'status' ) );
		$this->assertEquals( 'status', $object->get_status() );

		$this->assertInstanceOf( HelloWorld::class, $object->set_author( 456 ) );
		$this->assertEquals( 456, $object->get_author() );

		$this->assertEmpty( $object->get_post_meta() );
		$this->assertInstanceOf( HelloWorld::class, $object->set_post_meta( array( new PostMeta( 'key1' ) ) ) );
		$this->assertNotEmpty( $object->get_post_meta() );
		$this->assertEquals( 1, count( $object->get_post_meta() ) );
		$this->assertInstanceOf( HelloWorld::class, $object->add_post_meta( new PostMeta( 'key2', 'value2' ) ) );
		$this->assertEquals( 2, count( $object->get_post_meta() ) );
	}

	/**
	 * Test the get_post function
	 *
	 * @return void
	 */
	public function test_get_post(): void {

		$object = new HelloWorld();
		$post   = array(
			'ID'         => 101,
			'name'       => 'test-title',
			'post_title' => 'Test Title',
			'post_type'  => $object->get_post_type(),
		);
		$metas  = array(
			'test_meta' => array( 'Test Meta Value' ),
		);

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( 101, ARRAY_A )
			->andReturn( $post );

		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 101 )
			->andReturn( $metas );

		$post = $object->get_post( 101 );

		$this->assertNotEmpty( $post );
		$this->assertEquals( $object->get_post_type(), $post['post_type'] );
		$this->assertEquals( 'Test Meta Value', $post['test_meta'] );
	}

	/**
	 * Test the get_posts function
	 *
	 * @return void
	 */
	public function test_get_posts(): void {

		$object = new HelloWorld();
		$post   = array(
			'ID'         => 101,
			'name'       => 'test-title',
			'post_title' => 'Test Title',
			'post_type'  => $object->get_post_type(),
		);
		$metas  = array(
			'test_meta' => array( 'Test Meta Value' ),
		);
		$params = array(
			'post_status' => array( 'publish' ),
			'numberposts' => -1,
			'nopaging'    => true,
			'post_type'   => $object->get_post_type(),
			'fields'      => 'ids',
		);

		WP_Mock::userFunction( 'get_posts' )
			->once()
			->with( $params )
			->andReturn( array( 101 ) );

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( 101, ARRAY_A )
			->andReturn( $post );

		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 101 )
			->andReturn( $metas );

		$posts = $object->get_posts();
		$this->assertNotEmpty( $posts );

		$post = array_shift( $posts );
		$this->assertNotEmpty( $post );
		$this->assertEquals( $object->get_post_type(), $post['post_type'] );
		$this->assertEquals( 'Test Meta Value', $post['test_meta'] );
	}

	/**
	 * Test the get_post_by function
	 *
	 * @phpcs:disable WordPress.DB.SlowDBQuery
	 *
	 * @return void
	 */
	public function test_get_post_by(): void {

		$object = new HelloWorld();
		$post   = array(
			'ID'         => 101,
			'name'       => 'test-title',
			'post_title' => 'Test Title',
			'post_type'  => $object->get_post_type(),
		);
		$metas  = array(
			'test_meta' => array( 'Test Meta Value' ),
		);
		$params = array(
			'post_status' => array( 'publish' ),
			'numberposts' => 1,
			'nopaging'    => true,
			'post_type'   => $object->get_post_type(),
			'fields'      => 'ids',
			'name'        => 'test-title',
		);

		WP_Mock::userFunction( 'get_posts' )
			->once()
			->with( $params )
			->andReturn( array( 101 ) );

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( 101, ARRAY_A )
			->andReturn( $post );

		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 101 )
			->andReturn( $metas );

		$post = $object->get_post_by( 'test-title' );

		$this->assertNotEmpty( $post );
		$this->assertEquals( $object->get_post_type(), $post['post_type'] );
		$this->assertEquals( 'Test Meta Value', $post['test_meta'] );

		$params = array(
			'post_status' => array( 'publish' ),
			'numberposts' => 1,
			'nopaging'    => true,
			'post_type'   => $object->get_post_type(),
			'fields'      => 'ids',
			'meta_key'    => '_test_meta',
			'meta_value'  => 'Test Meta Value',
		);

		WP_Mock::userFunction( 'get_posts' )
			->once()
			->with( $params )
			->andReturn( array( 101 ) );

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( 101, ARRAY_A )
			->andReturn( $post );

		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 101 )
			->andReturn( $metas );

		$post = $object->get_post_by( 'Test Meta Value', 'test_meta' );

		$this->assertNotEmpty( $post );
		$this->assertEquals( $object->get_post_type(), $post['post_type'] );
	}

	/**
	 * Test the save_post function
	 *
	 * @return void
	 */
	public function test_save_post(): void {

		$object  = new HelloWorld();
		$title   = 'Post Title';
		$content = 'Test content!';
		$author  = 1;
		$metas   = array(
			'first_name' => 'First',
			'last_name'  => 'Last',
		);
		$params  = array(
			'post_type'    => 'hello-world',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_content' => $content,
			'post_author'  => $author,
			'meta_input'   => array(
				'_first_name' => 'First',
				'_last_name'  => 'Last',
			),
		);

		WP_Mock::userFunction( 'wp_insert_post' )
			->once()
			->with( $params )
			->andReturn( 101 );

		$post_id = $object->save_post( $title, $content, $metas, $author );

		$this->assertNotEmpty( $post_id );
		$this->assertEquals( 101, $post_id );
	}

	/**
	 * Test the update_post_metas function
	 *
	 * @return void
	 */
	public function test_update_post_metas(): void {

		$object = new HelloWorld();
		$metas  = array(
			'first_name' => 'First',
			'last_name'  => 'Last',
		);

		WP_Mock::userFunction( 'update_post_meta' )
			->once()
			->with( 101, '_first_name', 'First' );

		WP_Mock::userFunction( 'update_post_meta' )
			->once()
			->with( 101, '_last_name', 'Last' );

		$object->update_post_metas( 101, $metas );

		$this->assertConditionsMet();
	}

	/**
	 * Test the parse_meta_key function
	 *
	 * @return void
	 */
	public function test_parse_meta_key(): void {

		$object = new HelloWorld();

		$this->assertEquals( '_test_key', $object->parse_meta_key( 'test_key' ) );
		$this->assertEquals( '_test_key', $object->parse_meta_key( '_test_key' ) );
	}
}
