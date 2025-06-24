<?php
/**
 * The PhpUnit bootstrap file.
 *
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals
 *
 * @package    Mazepress\Core
 * @subpackage Tests
 */

/**
 * Merges user defined arguments into defaults array.
 *
 * @param mixed        $args     The args.
 * @param array<mixed> $defaults The defaults.
 *
 * @return array<mixed>
 */
function wp_parse_args( $args, $defaults = array() ): array {

	if ( is_object( $args ) ) {
		$parsed_args = get_object_vars( $args );
	} elseif ( is_array( $args ) ) {
		$parsed_args =& $args;
	} else {
		parse_str( (string) $args, $parsed_args );
	}

	if ( is_array( $defaults ) && $defaults ) {
		return array_merge( $defaults, $parsed_args );
	}

	return $parsed_args;
}

/**
 * Serializes data, if needed.
 *
 * @phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
 *
 * @since 2.0.0
 *
 * @param string|array<mixed>|object $data Data that might be serialized.
 *
 * @return mixed A scalar data.
 */
function maybe_serialize( $data ) {
	if ( is_array( $data ) || is_object( $data ) ) {
		return serialize( $data );
	}

	/*
	 * Double serialization is required for backward compatibility.
	 * See https://core.trac.wordpress.org/ticket/12930
	 * Also the world will end. See WP 3.6.1.
	 */
	if ( is_serialized( $data, false ) ) {
		return serialize( $data );
	}

	return $data;
}

/**
 * Unserializes data only if it was serialized.
 *
 * @phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
 *
 * @since 2.0.0
 *
 * @param string $data Data that might be unserialized.
 *
 * @return mixed Unserialized data can be any type.
 */
function maybe_unserialize( string $data ) {
	if ( is_serialized( $data ) ) { // Don't attempt to unserialize data that wasn't serialized going in.
		return @unserialize( trim( $data ) ); //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	return $data;
}

/**
 * Checks value to find if it was serialized.
 *
 * If $data is not a string, then returned value will always be false.
 * Serialized data is always a string.
 *
 * @since 2.0.5
 * @since 6.1.0 Added Enum support.
 *
 * @param string $data   Value to check to see if was serialized.
 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
 *
 * @return bool False if not serialized and true if it was.
 */
function is_serialized( string $data, bool $strict = true ): bool {
	// If it isn't a string, it isn't serialized.
	if ( ! is_string( $data ) ) {
		return false;
	}
	$data = trim( $data );
	if ( 'N;' === $data ) {
		return true;
	}
	if ( strlen( $data ) < 4 ) {
		return false;
	}
	if ( ':' !== $data[1] ) {
		return false;
	}
	if ( $strict ) {
		$lastc = substr( $data, -1 );
		if ( ';' !== $lastc && '}' !== $lastc ) {
			return false;
		}
	} else {
		$semicolon = strpos( $data, ';' );
		$brace     = strpos( $data, '}' );
		// Either ; or } must exist.
		if ( false === $semicolon && false === $brace ) {
			return false;
		}
		// But neither must be in the first X characters.
		if ( false !== $semicolon && $semicolon < 3 ) {
			return false;
		}
		if ( false !== $brace && $brace < 4 ) {
			return false;
		}
	}
	$token = $data[0];
	switch ( $token ) {
		case 's':
			if ( $strict ) {
				if ( '"' !== substr( $data, -2, 1 ) ) {
					return false;
				}
			} elseif ( ! str_contains( $data, '"' ) ) {
				return false;
			}
			// Or else fall through.
		case 'a':
		case 'O':
		case 'E':
			return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
		case 'b':
		case 'i':
		case 'd':
			$end = $strict ? '$' : '';
			return (bool) preg_match( "/^{$token}:[0-9.E+-]+;$end/", $data );
	}
	return false;
}

/**
 * Checks whether serialized data is of string type.
 *
 * @since 2.0.5
 *
 * @param string $data Serialized data.
 *
 * @return bool False if not a serialized string, true if it is.
 */
function is_serialized_string( string $data ): bool {
	// if it isn't a string, it isn't a serialized string.
	if ( ! is_string( $data ) ) {
		return false;
	}
	$data = trim( $data );
	if ( strlen( $data ) < 4 ) {
		return false;
	} elseif ( ':' !== $data[1] ) {
		return false;
	} elseif ( ! str_ends_with( $data, ';' ) ) {
		return false;
	} elseif ( 's' !== $data[0] ) {
		return false;
	} elseif ( '"' !== substr( $data, -2, 1 ) ) {
		return false;
	} else {
		return true;
	}
}
