<?php
/**
 * The PostMetaTest class file.
 *
 * @package    Mazepress\Post
 * @subpackage Tests
 */

declare(strict_types=1);

namespace Mazepress\Post\Tests;

use WP_Mock\Tools\TestCase;
use Mazepress\Post\PostMeta;

/**
 * The PostMetaTest class.
 */
class PostMetaTest extends TestCase {

	/**
	 * Test class properites.
	 *
	 * @return void
	 */
	public function test_properties(): void {

		$object = new PostMeta( 'key', 'value' );

		$this->assertEquals( 'key', $object->get_key() );
		$this->assertInstanceOf( PostMeta::class, $object->set_key( 'key2' ) );
		$this->assertEquals( 'key2', $object->get_key() );

		$this->assertEquals( 'value', $object->get_value() );
		$this->assertInstanceOf( PostMeta::class, $object->set_value( 'value2' ) );
		$this->assertEquals( 'value2', $object->get_value() );
	}
}
