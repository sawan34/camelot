<?php

namespace MPHB\Utils;

class ValidateUtils {

	/**
	 *
	 * @param mixed $value
	 * @param int $min Optional.
	 * @param int $max Optional.
	 * @return int|false Validated number or FALSE if the filter fails.
	 */
	public static function validateInt( $value, $min = null, $max = null ){
		$options = array();

		if ( isset( $min ) ) {
			$options['min_range'] = $min;
		}

		if ( isset( $max ) ) {
			$options['max_range'] = $min;
		}

		if ( !empty( $options ) ) {
			$options = array(
				'options' => $options
			);
		}

		return !empty( $options ) ? filter_var( $value, FILTER_VALIDATE_INT, $options ) : filter_var( $value, FILTER_VALIDATE_INT );
	}

	/**
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public static function validateBool( $value ){
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	public static function validateCommaSeparatedIds( $value ){
		$values = explode( ',', $value );
		$ids = array();

		foreach ( $values as $id ) {
			$ids[] = self::validateInt( $id, 0 );
		}

		$ids = array_filter( $ids );

		return $ids;
	}

	public static function validateRelation( $value ){
		$value = strtoupper( $value );
		return ( $value == 'OR' || $value == 'AND' ? $value : 'OR' );
	}

	/**
	 *
	 * @param bool $value
	 * @return bool
	 */
	public static function isNotEqualFalse( $value ){
		return $value !== false;
	}

}
