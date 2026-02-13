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

		return is_array( $data ) ? $data : array();
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
	 * @param bool   $meta  This is meta field.
	 *
	 * @return array<mixed>
	 */
	public function get_post_by( string $value, string $field = 'name', bool $meta = false ): array {

		$args = array( 'numberposts' => 1 );

		if ( $meta ) {
			// Parse the field name.
			$field = $this->parse_meta_key( $field );

			// Set the arguments.
			$args['meta_key']   = $field;
			$args['meta_value'] = $value;
		} else {
			$args[ $field ] = $value;
		}

		$posts = $this->get_posts( $args );
		$post  = ! empty( $posts ) ? array_shift( $posts ) : array();

		return $post;
	}

	/**
	 * Save the post
	 *
	 * @param string       $title       The post title.
	 * @param array<mixed> $postarr     The post data.
	 * @param array<mixed> $public_keys The public meta keys.
	 *
	 * @return int
	 */
	public function save_post( string $title, array $postarr, array $public_keys = array() ): int {

		// Sanitize.
		unset( $postarr['ID'] );

		// Parse the array.
		$postarr = $this->parse_post_array( $postarr, $public_keys );

		// Set default values.
		$postarr['post_type']    = $this->get_post_type();
		$postarr['post_title']   = $title;
		$postarr['post_content'] = ! empty( $postarr['post_content'] ) ? $postarr['post_content'] : '';
		$postarr['post_status']  = ! empty( $postarr['post_status'] ) ? $postarr['post_status'] : 'publish';

		// Create the post.
		$post_id = wp_insert_post( $postarr );

		return $post_id;
	}

	/**
	 * Update the post
	 *
	 * @param int          $post_id     The post ID.
	 * @param array<mixed> $postarr     The post data.
	 * @param array<mixed> $public_keys The public meta keys.
	 *
	 * @return int
	 */
	public function update_post( int $post_id, array $postarr, array $public_keys = array() ): int {

		// Parse the array.
		$postarr = $this->parse_post_array( $postarr, $public_keys );

		// Set the post ID.
		$postarr['ID'] = $post_id;

		// Update the post.
		$post_id = wp_update_post( $postarr );

		return $post_id;
	}

	/**
	 * Update the post metas
	 *
	 * @param int          $post_id     The post ID.
	 * @param array<mixed> $meta_input  The post data.
	 * @param array<mixed> $public_keys The public meta keys.
	 *
	 * @return void
	 */
	public function update_post_metas( int $post_id, array $meta_input, array $public_keys = array() ): void {

		// Parse the input.
		$meta_input = $this->parse_meta_input( $meta_input, $public_keys );

		foreach ( $meta_input as $key => $value ) {
			// Update the meta field.
			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Parse the post array
	 *
	 * @param array<mixed> $postarr     The post data.
	 * @param array<mixed> $public_keys The public meta keys.
	 *
	 * @return array<mixed>
	 */
	public function parse_post_array( array $postarr, array $public_keys = array() ): array {

		if ( ! empty( $postarr['post_content'] ) ) {
			// Add support for back slash.
			$postarr['post_content'] = wp_slash( $postarr['post_content'] );
		}

		if ( ! empty( $postarr['post_date'] ) ) {
			// Add support for back slash.
			$postarr['post_date_gmt'] = get_gmt_from_date( $postarr['post_date'] );
		}

		if ( ! empty( $postarr['meta_input'] ) && is_array( $postarr['meta_input'] ) ) {
			// Parse meta.
			$postarr['meta_input'] = $this->parse_meta_input( $postarr['meta_input'], $public_keys );
		}

		return $postarr;
	}

	/**
	 * Parse the post array
	 *
	 * @param array<mixed> $meta_input  The meta data.
	 * @param array<mixed> $public_keys The public meta keys.
	 *
	 * @return array<mixed>
	 */
	public function parse_meta_input( array $meta_input, array $public_keys = array() ): array {

		$new_meta = array();

		foreach ( $meta_input as $key => $value ) {
			// Parse the key and add it to meta.
			$meta_key = ! in_array( $key, $public_keys, true ) ? $this->parse_meta_key( $key ) : $key;

			// Append meta.
			$new_meta[ $meta_key ] = $value;
		}

		return $new_meta;
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
