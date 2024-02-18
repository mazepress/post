<?php
/**
 * The PostTypeInterface class file.
 *
 * @package Mazepress\Post
 */

declare(strict_types=1);

namespace Mazepress\Post;

/**
 * The PostTypeInterface class.
 */
interface PostTypeInterface {

	/**
	 * Get the post type.
	 *
	 * @return string
	 */
	public function get_post_type(): string;
}
