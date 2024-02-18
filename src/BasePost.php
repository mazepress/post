<?php
/**
 * The BasePost class file.
 *
 * @package Mazepress\Post
 */

declare(strict_types=1);

namespace Mazepress\Post;

use Mazepress\Post\PostTypeInterface;
use Mazepress\Post\PostMeta;

/**
 * The BasePost class.
 */
abstract class BasePost implements PostTypeInterface {

	/**
	 * The ID.
	 *
	 * @var int $id
	 */
	private $id;

	/**
	 * The title.
	 *
	 * @var string $title
	 */
	private $title;

	/**
	 * The slug.
	 *
	 * @var string $slug
	 */
	private $slug;

	/**
	 * The content.
	 *
	 * @var string $content
	 */
	private $content;

	/**
	 * The status.
	 *
	 * @var string $status
	 */
	private $status;

	/**
	 * The author.
	 *
	 * @var int $author
	 */
	private $author;

	/**
	 * The post meta.
	 *
	 * @var PostMeta[] $post_meta
	 */
	private $post_meta = array();

	/**
	 * Get the post type.
	 *
	 * @return string
	 */
	abstract public function get_post_type(): string;

	/**
	 * Get the ID.
	 *
	 * @return int|null
	 */
	public function get_id(): ?int {
		return $this->id;
	}

	/**
	 * Set the ID.
	 *
	 * @param int $id The ID.
	 *
	 * @return self
	 */
	public function set_id( int $id ): self {
		$this->id = $id;
		return $this;
	}

	/**
	 * Get the title.
	 *
	 * @return string|null
	 */
	public function get_title(): ?string {
		return $this->title;
	}

	/**
	 * Set the title.
	 *
	 * @param string $title The title.
	 *
	 * @return self
	 */
	public function set_title( string $title ): self {
		$this->title = $title;
		return $this;
	}

	/**
	 * Get the slug.
	 *
	 * @return string|null
	 */
	public function get_slug(): ?string {
		return $this->slug;
	}

	/**
	 * Set the slug.
	 *
	 * @param string $slug The slug.
	 *
	 * @return self
	 */
	public function set_slug( string $slug ): self {
		$this->slug = $slug;
		return $this;
	}

	/**
	 * Get the content.
	 *
	 * @return string|null
	 */
	public function get_content(): ?string {
		return $this->content;
	}

	/**
	 * Set the content.
	 *
	 * @param string $content The content.
	 *
	 * @return self
	 */
	public function set_content( string $content ): self {
		$this->content = $content;
		return $this;
	}

	/**
	 * Get the status.
	 *
	 * @return string|null
	 */
	public function get_status(): ?string {
		return $this->status;
	}

	/**
	 * Set the status.
	 *
	 * @param string $status The status.
	 *
	 * @return self
	 */
	public function set_status( string $status ): self {
		$this->status = $status;
		return $this;
	}

	/**
	 * Get the author.
	 *
	 * @return int|null
	 */
	public function get_author(): ?int {
		return $this->author;
	}

	/**
	 * Set the author.
	 *
	 * @param int $author The author.
	 *
	 * @return self
	 */
	public function set_author( int $author ): self {
		$this->author = $author;
		return $this;
	}

	/**
	 * Get the post meta.
	 *
	 * @return PostMeta[]
	 */
	public function get_post_meta(): array {
		return $this->post_meta;
	}

	/**
	 * Set the post meta.
	 *
	 * @param PostMeta[] $post_meta The post meta.
	 *
	 * @return self
	 */
	public function set_post_meta( array $post_meta ): self {
		$this->post_meta = $post_meta;
		return $this;
	}

	/**
	 * Set the post meta.
	 *
	 * @param PostMeta $post_meta The post meta.
	 *
	 * @return self
	 */
	public function add_post_meta( PostMeta $post_meta ): self {
		$this->post_meta[] = $post_meta;
		return $this;
	}
}
