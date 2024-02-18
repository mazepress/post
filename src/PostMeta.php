<?php
/**
 * The PostMeta class file.
 *
 * @package Mazepress\Post
 */

declare(strict_types=1);

namespace Mazepress\Post;

/**
 * The PostMeta class.
 */
class PostMeta {

	/**
	 * The key.
	 *
	 * @var string $key
	 */
	private $key;

	/**
	 * The value.
	 *
	 * @var mixed $value
	 */
	private $value;

	/**
	 * Initialise class.
	 *
	 * @param string $key   The meta key.
	 * @param mixed  $value The meta value.
	 */
	public function __construct( string $key = '', $value = '' ) {
		if ( ! empty( $key ) ) {
			$this->set_key( $key );
			$this->set_value( $value );
		}
	}

	/**
	 * Get the key.
	 *
	 * @return string|null
	 */
	public function get_key(): ?string {
		return $this->key;
	}

	/**
	 * Set the key.
	 *
	 * @param string $key The key.
	 *
	 * @return self
	 */
	public function set_key( string $key ): self {
		$this->key = $key;
		return $this;
	}

	/**
	 * Get the value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Set the value.
	 *
	 * @param mixed $value The value.
	 *
	 * @return self
	 */
	public function set_value( $value ): self {
		$this->value = $value;
		return $this;
	}
}
