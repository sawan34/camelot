<?php

namespace MPHB\BookingRules;

use \MPHB\BookingRules\Custom\CustomRules;
use \MPHB\BookingRules\Reservation\ReservationRules;

class RulesChecker implements RuleVerifiable {

	/**
	 *
	 * @var ReservationRules
	 */
	protected $reservationRules;

	/**
	 *
	 * @var CustomRules
	 */
	protected $customRules;

	public function __construct( ReservationRules $reservationRules, CustomRules $customRules ){
		$this->reservationRules	 = $reservationRules;
		$this->customRules		 = $customRules;
	}

	/**
	 *
	 * @param \DateTime $checkInDate
	 * @param \DateTime $checkOutDate
	 * @param int $roomTypeId
	 * @return bool
	 */
	public function verify( \DateTime $checkInDate, \DateTime $checkOutDate, $roomTypeId = 0 ){
		return $this->reservationRules->verify( $checkInDate, $checkOutDate, $roomTypeId )
			&& $this->customRules->verify( $checkInDate, $checkOutDate, $roomTypeId );
	}

	/**
	 *
	 * @return \MPHB\BookingRules\Reservation\ReservationRules
	 */
	public function reservationRules(){
		return $this->reservationRules;
	}

	/**
	 *
	 * @return \MPHB\BookingRules\Custom\CustomRules
	 */
	public function customRules(){
		return $this->customRules;
	}

	public function getData(){
		return array(
			'global'		 => $this->reservationRules->getGlobalRule(),
			'dates'			 => $this->customRules->getGlobalRestrictions(),
			'blockedTypes'	 => $this->customRules->getGlobalTypeRestrictions()
		);
	}

}
