<?php

namespace MPHB\ScriptManagers;

class PublicScriptManager extends ScriptManager {

	/**
	 *
	 * @var int[]
	 */
	private $roomTypeIds = array();

	/**
	 *
	 * @var array
	 */
	private $gatewaysData = array();
	private $checkoutData;

	public function __construct(){
		add_action( 'wp_enqueue_scripts', array( $this, 'register' ), 9 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 11 );
	}

	public function register(){
		parent::register();

		wp_register_script( 'mphb-magnific-popup', MPHB()->getPluginUrl( 'vendors/magnific-popup/dist/jquery.magnific-popup.min.js' ), array( 'jquery' ), MPHB()->getVersion(), true );

		wp_register_script( 'mphb-flexslider', MPHB()->getPluginUrl( 'vendors/woothemes-FlexSlider/jquery.flexslider-min.js' ), array( 'jquery' ), MPHB()->getVersion(), true );
		wp_register_script( 'mphb-jquery-serialize-json', MPHB()->getPluginUrl( 'vendors/jquery.serializeJSON/jquery.serializejson.min.js' ), array( 'jquery' ), MPHB()->getVersion() );

		wp_register_script( 'mphb-vendor-stripe-checkout', 'https://checkout.stripe.com/v2/checkout.js', null, '2.0', true );

		wp_register_script( 'mphb-vendor-braintree-client-sdk', 'https://js.braintreegateway.com/js/braintree-2.31.0.min.js', null, '2.31.0', true );

		wp_register_script( 'mphb', MPHB()->getPluginUrl( 'assets/js/public/mphb.min.js' ), $this->scriptDependencies, MPHB()->getVersion(), true );
	}

	protected function registerDatepickTheme(){

		$theme = MPHB()->settings()->main()->getDatepickerTheme();

		if (
			$theme === ''  // Default theme selected
			|| !array_key_exists( $theme, MPHB()->settings()->main()->getDatepickerThemesList() )
		) {
			return;
		}

		$datepickerThemeFile = MPHB()->getPluginUrl( "assets/css/datepick-themes/mphb-datepicker-{$theme}.css" );

		wp_register_style( 'mphb-kbwood-datepick-theme', $datepickerThemeFile, array( 'mphb-kbwood-datepick-css' ), MPHB()->getVersion() );

		$this->addStyleDependency( 'mphb-kbwood-datepick-theme' );
	}

	protected function registerStyles(){
		parent::registerStyles();
		$this->registerDatepickTheme();
		wp_register_style( 'mphb-magnific-popup-css', MPHB()->getPluginUrl( 'vendors/magnific-popup/dist/magnific-popup.css' ), null, MPHB()->getVersion() );

		$useFixedFlexslider = apply_filters( 'mphb_use_fixed_flexslider_css', true );
		if ( $useFixedFlexslider ) {
			wp_register_style( 'mphb-flexslider-css', MPHB()->getPluginUrl( 'assets/css/flexslider-fixed.css' ), null, MPHB()->getVersion() );
		} else {
			wp_register_style( 'mphb-flexslider-css', MPHB()->getPluginUrl( 'vendors/woothemes-FlexSlider/flexslider.css' ), null, MPHB()->getVersion() );
		}

		wp_register_style( 'mphb', MPHB()->getPluginUrl( 'assets/css/mphb.min.css' ), $this->styleDependencies, MPHB()->getVersion() );
	}

	public function enqueue(){

		if ( !wp_script_is( 'mphb' ) ) {
			// NextGEN Gallery buffers output and prints scripts with priority 1:
			//	 add_action('wp_print_footer_scripts', ..., 1);
			// We need to add localization earlier. Otherwise we'll add localization
			// for handle "mphb" when it will be marked as "done" and all scripts will
			// be printed
			add_action( 'wp_print_footer_scripts', array( $this, 'localize' ), 0 );
		}

		wp_enqueue_script( 'mphb' );
		$this->enqueueStyles();
	}

	private function enqueueStyles(){
		wp_enqueue_style( 'mphb' );
	}

	public function addRoomTypeData( $roomTypeId ){
		if ( !in_array( $roomTypeId, $this->roomTypeIds ) ) {
			$this->roomTypeIds[] = $roomTypeId;
		}
	}

	/**
	 *
	 * @param string $gatewayId
	 * @param array $data
	 */
	public function addGatewayData( $gatewayId, $data ){
		if ( !isset( $this->gatewaysData[$gatewayId] ) ) {
			$this->gatewaysData[$gatewayId] = array();
		}
		$this->gatewaysData[$gatewayId] = array_merge( $this->gatewaysData[$gatewayId], $data );
	}

	/**
	 *
	 * @param array $data
	 */
	public function addCheckoutData( $data ){
		if ( !isset( $this->checkoutData ) ) {
			$this->checkoutData = array();
		}
		$this->checkoutData = $data;
	}

	public function localize(){
		wp_localize_script( 'mphb', 'MPHB', $this->getLocalizeData() );
	}

	public function getLocalizeData(){
		$jsDateFormat = MPHB()->settings()->dateTime()->getDateTransferFormat();

		$data = array(
			'_data' => array(
				'settings'			 => array(
					'currency'					 => MPHB()->settings()->currency()->getCurrencyCode(),
					'siteName'					 => get_bloginfo( 'name' ),
					'firstDay'					 => MPHB()->settings()->dateTime()->getFirstDay(),
					'numberOfMonthCalendar'		 => 2,
					'numberOfMonthDatepicker'	 => 2,
					'dateFormat'				 => MPHB()->settings()->dateTime()->getDateFormatJS(),
					'dateTransferFormat'		 => MPHB()->settings()->dateTime()->getDateTransferFormatJS(),
					'useBilling'				 => MPHB()->settings()->main()->getConfirmationMode() === 'payment',
					'useCoupons'				 => MPHB()->settings()->main()->isCouponsEnabled(),
					'datepickerClass'			 => MPHB()->settings()->main()->getDatepickerThemeClass(),
					'isDirectBooking'			 => MPHB()->settings()->main()->isDirectBooking()
				),
				'today'				 => mphb_current_time( $jsDateFormat ),
				'ajaxUrl'			 => MPHB()->getAjaxUrl(),
				'nonces'			 => MPHB()->getAjax()->getFrontNonces(),
				'room_types_data'	 => array(),
				'translations'		 => array(
					'errorHasOccured'					 => __( 'An error has occurred, please try again later.', 'motopress-hotel-booking' ),
					'booked'							 => __( 'Booked', 'motopress-hotel-booking' ),
					'pending'							 => __( 'Pending', 'motopress-hotel-booking' ),
					'available'							 => __( 'Available', 'motopress-hotel-booking' ),
					'notAvailable'						 => __( 'Not available', 'motopress-hotel-booking' ),
					'notStayIn'							 => __( 'Not stay-in', 'motopress-hotel-booking' ),
					'notCheckIn'						 => __( 'Not check-in', 'motopress-hotel-booking' ),
					'notCheckOut'						 => __( 'Not check-out', 'motopress-hotel-booking' ),
					'past'								 => __( 'Day in the past', 'motopress-hotel-booking' ),
					'checkInDate'						 => __( 'Check-in date', 'motopress-hotel-booking' ),
					'lessThanMinDaysStay'				 => sprintf( __( 'Less than min days (%s) stay', 'motopress-hotel-booking' ), MPHB()->settings()->bookingRules()->getGlobalMinDays() ),
					'moreThanMaxDaysStay'				 => sprintf( __( 'More than max days (%s) stay', 'motopress-hotel-booking' ), MPHB()->settings()->bookingRules()->getGlobalMaxDays() ),
					// for dates between "not stay-in" (rules, existsing bookings) date and "max days stay" date
					'laterThanMaxDate'					 => __( 'Later than max date for current check-in date', 'motopress-hotel-booking' ),
					'rules'								 => __( 'Rules:', 'motopress-hotel-booking' ),
					'tokenizationFailure'				 => __( 'Tokenisation failed: %s', 'motopress-hotel-booking' ),
					'roomsAddedToReservation_singular'	 => _n( '%1$d &times; &ldquo;%2$s&rdquo; has been added to your reservation.', '%1$d &times; &ldquo;%2$s&rdquo; have been added to your reservation.', 1, 'motopress-hotel-booking' ),
					'roomsAddedToReservation_plural'	 => _n( '%1$d &times; &ldquo;%2$s&rdquo; has been added to your reservation.', '%1$d &times; &ldquo;%2$s&rdquo; have been added to your reservation.', 2, 'motopress-hotel-booking' ),
					'countRoomsSelected_singular'		 => _n( '%s accommodation selected.', '%s accommodations selected.', 1, 'motopress-hotel-booking' ),
					'countRoomsSelected_plural'			 => _n( '%s accommodation selected.', '%s accommodations selected.', 2, 'motopress-hotel-booking' ),
					'countRoomsAvailable_singular'		 => _n( 'of %d accommodation available.', 'of %d accommodations available.', 1, 'motopress-hotel-booking' ),
					'countRoomsAvailable_plural'		 => _n( 'of %d accommodation available.', 'of %d accommodations available.', 2, 'motopress-hotel-booking' ),
					'priceFormat'						 => MPHB()->settings()->currency()->getPriceFormat(),
					'emptyCouponCode'					 => __( 'Coupon code is empty.', 'motopress-hotel-booking' ),
					'checkInNotValid'					 => __( 'Check-in date is not valid.', 'motopress-hotel-booking' ),
					'checkOutNotValid'					 => __( 'Check-out date is not valid.', 'motopress-hotel-booking' )
				),
				'page'				 => array(
					'isCheckoutPage'		 => mphb_is_checkout_page(),
					'isSingleRoomTypePage'	 => mphb_is_single_room_type_page(),
					'isSearchResultsPage'	 => mphb_is_search_results_page()
				),
				'rules'				 => MPHB()->getRulesChecker()->getData(),
				'gateways'			 => $this->gatewaysData,
				'seasons'			 => array(
					'list'					 => null, // See code below
					'rules'					 => get_option( 'mphb_booking_rules_seasons', MPHB()->getRulesChecker()->reservationRules()->getGlobalRule() ),
					'priorities'			 => get_option( 'mphb_booking_rules_season_priorities', array() ),
				)
			)
		);

		if ( isset( $this->checkoutData ) ) {
			$data['_data']['checkout'] = $this->checkoutData;
		}

		if ( mphb_is_single_room_type_page() ) {
			$this->addRoomTypeData( get_the_ID() );
		}

		$roomTypesAtts = array(
			'post__in' => $this->roomTypeIds
		);

		$roomTypes = MPHB()->getRoomTypeRepository()->findAll( $roomTypesAtts );

		foreach ( $roomTypes as $roomType ) {

			$activeRoomsCount = MPHB()->getRoomPersistence()->getCount(
				array(
					'room_type_id'	 => $roomType->getOriginalId(),
					'post_status'	 => 'publish'
				)
			);

			$data['_data']['room_types_data'][$roomType->getId()] = array(
				'dates'				 => array(
					'booked'	 => MPHB()->getRoomRepository()->getLockedRoomsCountByDayList( $roomType->getOriginalId() ),
					'havePrice'	 => $roomType->getDatesHavePrice(),
					'blocked'	 => MPHB()->getRulesChecker()->customRules()->getBlockedRoomsCounts( $roomType->getId() )
				),
				'activeRoomsCount'	 => $activeRoomsCount
			);
		}

		$seasonIds = MPHB()->getRateRepository()->getSeasonIds();
		$seasons = MPHB()->getSeasonRepository()->findAll( array( 'post__in' => $seasonIds ) );
		$seasonsList = array();

		foreach ( $seasons as $season ) {
			$seasonPeriod = array(
				'start_date'	 => $season->getStartDate()->format( $jsDateFormat ),
				'end_date'		 => $season->getEndDate()->format( $jsDateFormat ),
				'allowed_days'	 => $season->getDays()
			);
			array_walk( $seasonPeriod['allowed_days'], function ( &$value, $_ ) {
				$value = (int)$value;
			} );
			$seasonsList[$season->getId()] = $seasonPeriod;
		}

		$data['_data']['seasons']['list'] = $seasonsList;

		return $data;
	}

}
