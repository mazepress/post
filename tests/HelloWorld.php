<?php
/**
 * The HelloWorld stub class file.
 *
 * @package    Mazepress\Post
 * @subpackage Tests
 */

namespace Mazepress\Post\Tests;

use Mazepress\Post\BasePost;

/**
 * The HelloWorld class.
 */
class HelloWorld extends BasePost {

	/**
	 * The post Type.
	 *
	 * @var string
	 */
	const POST_TYPE = 'hello-world';

	/**
	 * Get the post type.
	 *
	 * @return string
	 */
	public function get_post_type(): string {
		return self::POST_TYPE;
	}
}
