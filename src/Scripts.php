<?php
/**
 * The Scripts class file.
 *
 * @package Mazepress\Skeleton
 */

declare(strict_types=1);

namespace Mazepress\Skeleton;

use Mazepress\Core\Struct\PackageInterface;

/**
 * The Scripts class.
 */
class Scripts {

	/**
	 * The package.
	 *
	 * @var PackageInterface $package
	 */
	private $package;

	/**
	 * Initiate class.
	 *
	 * @param PackageInterface $package The package.
	 */
	public function __construct( PackageInterface $package ) {
		$this->set_package( $package );
	}

	/**
	 * Enque scripts and style.
	 *
	 * @return void
	 */
	public function load(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {

		wp_enqueue_style(
			'skeleton',
			$this->get_package()->get_url() . 'assets/css/style.css',
			array(),
			$this->get_package()->get_version(),
		);
	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts(): void {

		wp_enqueue_style(
			'skeleton-admin',
			$this->get_package()->get_url() . 'assets/css/admin.css',
			array(),
			$this->get_package()->get_version(),
		);
	}

	/**
	 * Get the package.
	 *
	 * @return PackageInterface|null
	 */
	public function get_package(): ?PackageInterface {
		return $this->package;
	}

	/**
	 * Set the package.
	 *
	 * @param PackageInterface $package The package.
	 *
	 * @return self
	 */
	public function set_package( PackageInterface $package ): self {
		$this->package = $package;
		return $this;
	}
}
