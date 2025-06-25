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
	 * Get the post.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return array<mixed>
	 */
	public function get_post( int $post_id ): array {

		// Get the default post data.
		$data = get_post( $post_id, ARRAY_A );

		if ( ! empty( $data ) ) {

			// Get all the metas.
			$metas = get_post_meta( $post_id );

			foreach ( $metas as $key => $value ) {
				if ( ! empty( $key ) && is_array( $value ) ) {
					$data[ ltrim( $key, '_' ) ] = maybe_unserialize( array_shift( $value ) );
				}
			}
		}

		return $data;
	}

	/**
	 * Get all the posts.
	 *
	 * @phpcs:disable WordPress.DB.SlowDBQuery
	 *
	 * @param array<mixed> $args The argument fields.
	 *
	 * @return array<mixed>
	 */
	public function get_posts( array $args = array() ): array {

		$args = wp_parse_args(
			$args,
			array(
				'post_status' => array( 'publish' ),
				'numberposts' => -1,
				'nopaging'    => true,
			)
		);

		$args['post_type'] = $this->get_post_type();
		$args['fields']    = 'ids';

		$posts = get_posts( $args );
		$data  = array();

		foreach ( $posts as $post_id ) {
			$data[ $post_id ] = $this->get_post( $post_id );
		}

		return $data;
	}

	/**
	 * Get the post by field.
	 *
	 * @phpcs:disable WordPress.DB.SlowDBQuery
	 *
	 * @param string $value The field value.
	 * @param string $field The field name.
	 * @param bool   $parse Parse the field name.
	 *
	 * @return array<mixed>
	 */
	public function get_post_by( string $value, string $field = 'slug', bool $parse = true ): array {

		$args = array( 'numberposts' => 1 );

		if ( 'slug' === $field ) {
			$args['name'] = $value;
		} else {

			if ( $parse ) {
				$field = $this->parse_meta_key( $field );
			}

			$args['meta_key']   = $field;
			$args['meta_value'] = $value;
		}

		$posts = $this->get_posts( $args );
		$post  = ! empty( $posts ) ? array_shift( $posts ) : array();

		return $post;
	}

	/**
	 * Save the post
	 *
	 * @param string       $title   The post title.
	 * @param string       $content The post content.
	 * @param array<mixed> $meta    The post meta.
	 * @param int          $author  The post created by.
	 *
	 * @return int
	 */
	public function save_post( string $title, string $content = '', array $meta = array(), int $author = 0 ): int {

		$post = array(
			'post_type'    => $this->get_post_type(),
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_content' => $content,
			'post_author'  => $author,
		);

		if ( ! empty( $meta ) ) {

			$post['meta_input'] = array();

			foreach ( $meta as $key => $value ) {
				// Parse the key name.
				$key = $this->parse_meta_key( $key );

				// Set the meta fields.
				$post['meta_input'][ $key ] = $value;
			}
		}

		// Create the post.
		$post_id = wp_insert_post( $post );

		return $post_id;
	}

	/**
	 * Update the post
	 *
	 * @param int          $post_id The post ID.
	 * @param array<mixed> $data    The post data.
	 *
	 * @return void
	 */
	public function update_post_metas( int $post_id, array $data ): void {

		foreach ( $data as $key => $value ) {
			// Parse the key name.
			$key = $this->parse_meta_key( $key );

			// Update the meta field.
			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Parse the meta field key with underscore
	 *
	 * @param string $key The meta key.
	 *
	 * @return string
	 */
	public function parse_meta_key( string $key ): string {

		// Add the underscore prefix.
		if ( ! empty( $key ) && 0 !== strpos( $key, '_' ) ) {
			$key = '_' . $key;
		}

		return $key;
	}

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
