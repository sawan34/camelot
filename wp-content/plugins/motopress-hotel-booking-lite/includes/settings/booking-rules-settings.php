<?php

namespace MPHB\Settings;

class BookingRulesSettings {

	private $defaultCheckInDays   = array( 0, 1, 2, 3, 4, 5, 6 );
	private $defaultCheckOutDays  = array( 0, 1, 2, 3, 4, 5, 6 );
	private $defaultMinStayLength = 1;
	private $defaultMaxStayLength = 0;

	public function getDefaultCheckInDays(){
		return $this->defaultCheckInDays;
	}

	public function getDefaultCheckOutDays(){
		return $this->defaultCheckOutDays;
	}

	public function getDefaultMinStayLength(){
		return $this->defaultMinStayLength;
	}

	public function getDefaultMaxStayLength(){
		return $this->defaultMaxStayLength;
	}

	/**
	 *
	 * @return int
	 */
	public function getGlobalMinDays(){
		return $this->getReservationRule( 'min_stay_length', 0, $this->getDefaultMinStayLength() );
	}

	/**
	 *
	 * @return int
	 */
	public function getGlobalMaxDays(){
		return $this->getReservationRule( 'max_stay_length', 0, $this->getDefaultMaxStayLength() );
	}

	/**
	 *
	 * @return array
	 */
	public function getReservationRules(){
		return get_option( 'mphb_booking_rules_reservation', array() );
	}

	/**
	 *
	 * @return array
	 */
	public function getDefaultReservationRule(){
		return array(
			'check_in_days'   => $this->getDefaultCheckInDays(),
			'check_out_days'  => $this->getDefaultCheckOutDays(),
			'min_stay_length' => $this->getDefaultMinStayLength(),
			'max_stay_length' => $this->getDefaultMaxStayLength()
		);
	}

	private function getReservationRule( $rule, $roomTypeId, $default = false ){
		$reservationRules = $this->getReservationRules();
		return isset( $reservationRules[$roomTypeId][0][$rule] ) ? $reservationRules[$roomTypeId][0][$rule] : $default;
	}

	/**
	 *
	 * @return array
	 */
	public function getCustomRules(){
		return get_option( 'mphb_booking_rules_custom', array() );
	}

	/**
	 *
	 * @param int $roomTypeId
	 */
	public function getMinDaysStay( $roomTypeId ){
		return $this->getReservationRule( 'min_stay_length', $roomTypeId, $this->getGlobalMinDays() );
	}

	/**
	 *
	 * @param int $roomTypeId 
	 */
	public function getMaxDaysStay( $roomTypeId ){
		return $this->getReservationRule( 'max_stay_length', $roomTypeId, $this->getGlobalMaxDays() );
	}

}
