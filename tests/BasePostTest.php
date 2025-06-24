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
			'post_title' => 'Test Title',
			'post_type'  => $object->get_post_type(),
		);
		$metas  = array(
			'_test_meta' => array( 'Test Meta Value' ),
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
			'post_title' => 'Test Title',
			'post_type'  => $object->get_post_type(),
		);
		$metas  = array(
			'_test_meta' => array( 'Test Meta Value' ),
		);

		WP_Mock::userFunction( 'get_posts' )
			->once()
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
}
