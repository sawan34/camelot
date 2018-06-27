<?php

namespace MPHB\Entities;

class SeasonPrice {

	/**
	 *
	 * @var int
	 */
	private $id;

	/**
	 *
	 * @var int
	 */
	private $seasonId;

	/**
	 *
	 * @var float
	 */
	private $price;

	/**
	 *
	 * @param array $atts
	 * @param int $atts['id']
	 * @param int $atts['season_id']
	 * @param float $atts['price']
	 */
	protected function __construct( $atts = array() ){
		$this->id		 = $atts['id'];
		$this->seasonId	 = $atts['season_id'];
		$this->price	 = $atts['price'];
	}

	/**
	 *
	 * @return int
	 */
	function getId(){
		return $this->id;
	}

	/**
	 *
	 * @return int
	 */
	function getSeasonId(){
		return $this->seasonId;
	}

	/**
	 *
	 * @return \MPHB\Entities\Season|null
	 */
	function getSeason(){
		return MPHB()->getSeasonRepository()->findById( $this->seasonId );
	}

	/**
	 *
	 * @return float
	 */
	function getPrice(){
		return $this->price;
	}

	/**
	 *
	 * @return array
	 */
	function getDatePrices(){
		$datePrices = array();

		$season = $this->getSeason();
		if ( !$season ) {
			return $datePrices;
		}

		$dates	 = $season->getDates();
		$dates	 = array_map( array( '\MPHB\Utils\DateUtils', 'formatDateDB' ), $dates );

		$datePrices = array_fill_keys( $dates, $this->price );
		return $datePrices;
	}

	/**
	 *
	 * @param array $atts
	 * @param int $atts['id']
	 * @param int $atts['season_id']
	 * @param float $atts['price']
	 * @return SeasonPrice|null
	 */
	public static function create( $atts ){

		if ( !isset( $atts['id'], $atts['price'], $atts['season_id'] ) ) {
			return null;
		}

		$atts['id']			 = (int) $atts['id'];
		$atts['season_id']	 = (int) $atts['season_id'];
		$atts['price']		 = (float) $atts['price'];

		if ( $atts['id'] < 0 ) {
			return null;
		}

		if ( $atts['price'] < 0 ) {
			return null;
		}

		if ( !MPHB()->getSeasonRepository()->findById( $atts['season_id'] ) ) {
			return null;
		}

		return new self( $atts );
	}

}
