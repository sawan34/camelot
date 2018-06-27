<?php

namespace MPHB\BookingRules\Reservation;

use \MPHB\BookingRules\RuleVerifiable;

class ReservationRules implements RuleVerifiable {

	/**
	 *
	 * @var ReservationRule[] [%Type ID% => ReservationRule]
	 */
	private $globals = array();

	/**
	 * <b>Does not</b> contain global rules.
	 * @var array [%Type ID% => [%Season ID% => ReservationRule]]
	 */
	private $rules = array();

	public function __construct( array $reservationRules ){
		$defaults = MPHB()->settings()->bookingRules()->getDefaultReservationRule();

		foreach ( $reservationRules as $typeId => $seasonRules ) {
			$this->rules[$typeId] = array();

			foreach ( $seasonRules as $seasonId => $rule ) {
				$rule = array_merge( $defaults, $rule );

				if ( $seasonId == 0 ) {
					$this->globals[$typeId] = new ReservationRule( $rule );
				} else {
					$this->rules[$typeId][$seasonId] = new ReservationRule( $rule );
				}
			} // For each season

		} // For each type
	}

	/**
	 *
	 * @param \DateTime $checkInDate
	 * @param \DateTime $checkOutDate
	 * @param int $roomTypeId
	 * @return bool
	 */
	public function verify( \DateTime $checkInDate, \DateTime $checkOutDate, $roomTypeId = 0 ){
		$verified = true;

		$seasonId = MPHB()->getSeasonRepository()->findSeasonId( array( 'from_date' => $checkInDate ) );

		// Verify All/All
		if ( isset( $this->globals[0] ) ) {
			$verified = $verified && $this->globals[0]->verify( $checkInDate, $checkOutDate );
		}

		// Verify Type/All
		if ( $roomTypeId != 0 && isset( $this->globals[$roomTypeId] ) ) {
			$verified = $verified && $this->globals[$roomTypeId]->verify( $checkInDate, $checkOutDate );
		}

		// Verify Type/X
		if ( $roomTypeId != 0 && $seasonId != 0 && isset( $this->rules[$roomTypeId][$seasonId] ) ) {
			$verified = $verified && $this->rules[$roomTypeId][$seasonId]->verify( $checkInDate, $checkOutDate );
		}

		return $verified;
	}

	public function getGlobalRule(){
		$data = isset( $this->globals[0] ) ? $this->globals[0]->getData() : MPHB()->settings()->bookingRules()->getDefaultReservationRule();

		if ( $data['max_stay_length'] == 0 ) {
			$data['max_stay_length'] = 3652; // 10 years
		}

		return $data;
	}

}
