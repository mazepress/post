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
}
