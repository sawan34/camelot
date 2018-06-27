<?php

if ( ! function_exists( 'dd' ) ) {
	/**
	 * Dump the passed variables and end the script.
	 */
	function dd() {
		// @codingStandardsIgnoreLine
		array_map( function( $x ) { var_dump( $x ); }, func_get_args() );
		die;
	}
}

if ( ! function_exists( 'wp_data' ) ) {
	/**
	 * Get Wordpress specific data from the DB and return in a usable array.
	 *
	 * @param  string $type Data type.
	 * @param  mixed  $args Optional, data query args or something else.
	 * @return array
	 */
	function wp_data( $type, $args = array() ) {
		return Skeleton\Support\WP_Data::get( $type, $args );
	}
}

if ( ! function_exists( 'skeleton_render_field' ) ) :
	/**
	 * Tiny helper render a field.
	 *
	 * @param  CMB2_Field $field CMB2 Field instance.
	 * @return void
	 */
	function skeleton_render_field( CMB2_Field $field ) {
		( new CMB2_Types( $field ) )->render();
	}
endif;

if ( ! function_exists( 'skeleton_display_field_errors' ) ) :
	/**
	 * Tiny helper display field errors.
	 *
	 * @param  CMB2_Field $field CMB2 Field instance.
	 * @return void
	 */
	function skeleton_display_field_errors( CMB2_Field $field ) {
		$cmb2 = $field->get_cmb();

		// Don't show if invalid CMB2 instance.
		if ( ! $cmb2 || is_wp_error( $cmb2 ) ) {
			return;
		}

		$id = $field->id( true );
		$errors = $cmb2->get_errors();

		if ( isset( $errors[ $id ] ) ) {
			$error_message = is_string( $errors[ $id ] ) ? $errors[ $id ] : $errors[ $id ][0];
			printf( '<p class="cmb2-validate-error">%s</p>', $error_message ); // WPCS: XSS OK.
		}
	}
endif;
