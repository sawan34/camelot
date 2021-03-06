<?php

namespace MPHB\Settings;

class TaxesAndFeesSettings {

	private $taxes = array();
	private $fees = null;

	private function getTaxes( $name, $typeId ){
		if ( !isset( $this->taxes[$name] ) ) {
			$this->taxes[$name] = get_option( $name, array() );
		}

		if ( $typeId != 0 ) {
			$taxes = $this->filterByTypeId( $this->taxes[$name], $typeId );
		} else {
			$taxes = $this->taxes[$name];
		}

		return $this->structurize( $taxes );
	}

	public function getFees( $typeId = 0 ){
		if ( is_null( $this->fees ) ) {
			$this->fees = get_option( 'mphb_fees', array() );
		}

		if ( $typeId != 0 ) {
			$taxes = $this->filterByTypeId( $this->fees, $typeId );
		} else {
			$taxes = $this->fees;
		}

		return $this->structurize( $taxes );
	}

	public function getAccommodationTaxes( $typeId = 0 ){
		return $this->getTaxes( 'mphb_accommodation_taxes', $typeId );
	}

	public function getServiceTaxes( $typeId = 0 ){
		return $this->getTaxes( 'mphb_service_taxes', $typeId );
	}

	public function getFeeTaxes( $typeId = 0 ){
		return $this->getTaxes( 'mphb_fee_taxes', $typeId );
	}

	private function filterByTypeId( $rules, $filterId ){
		$filteredRules = array();

		foreach ( $rules as $rule ) {
			// Filter by room type ID
			$roomTypes = $rule['rooms'];

			foreach ( $roomTypes as $roomType ) {
				$roomType = (int)$roomType;

				// 0 = "All types"
				if ( $roomType == 0 || $roomType == $filterId ) {
					$filteredRules[] = $rule;
					break;
				}

			} // foreach ( $roomTypes ... )
		} // foreach ( $rules ... )

		return $filteredRules;
	}

	private function structurize( $rules ){
		$convertedRules = array();

		foreach ( $rules as $rule ) {
			if ( !is_array( $rule['amount'] ) ) {
				$amount = (float)$rule['amount'];
			} else {
				$amount = array(
					'adults'	 => (float)$rule['amount'][0],
					'children'	 => (float)$rule['amount'][1]
				);
			}

			$convertedRules[] = array(
				'label'  => $rule['label'],
				'type'   => $rule['type'],
				'amount' => $amount,
				'limit'  => (int)$rule['limit']
			);
		}

		return $convertedRules;
	}

}
