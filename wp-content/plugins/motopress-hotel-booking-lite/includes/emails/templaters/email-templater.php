<?php

namespace MPHB\Emails\Templaters;

use \MPHB\Views;

class EmailTemplater extends AbstractTemplater {

	private $tagGroups = array();

	/**
	 *
	 * @var \MPHB\Entities\Booking
	 */
	private $booking;

	/**
	 *
	 * @var \MPHB\Entities\Payment
	 */
	private $payment;

	/**
	 *
	 * @param array $tagGroups
	 * @param bool $tagGroups['global'] Global site tags. Default TRUE.
	 * @param bool $tagGroups['booking'] Booking tags. Default FALSE.
	 * @param bool $tagGroups['user_confirmation'] User confirmation tags. Default FALSE.
	 * @param bool $tagGroups['user_cancellation'] User cancellation tags. Default FALSE.
	 * @param bool $tagGroups['payment'] Payment details tags. Default FALSE.
	 */
	public static function create( $tagGroups = array() ){

		$templater = new self();

		$templater->setTagGroups( $tagGroups );

		return $templater;
	}

	public function setTagGroups( $tagGroups ){
		$defaultTagGroups = array(
			'global'			 => true,
			'booking'			 => false,
			'user_confirmation'	 => false,
			'user_cancellation'	 => false,
			'payment'			 => false
		);

		$this->tagGroups = array_merge( $defaultTagGroups, $tagGroups );
	}

	/**
	 *
	 * @param array $tagGroups
	 */
	public function setupTags(){

		$tags = array();

		if ( $this->tagGroups['global'] ) {
			$this->_fillGlobalTags( $tags );
		}

		if ( $this->tagGroups['booking'] ) {
			$this->_fillBookingTags( $tags );
		}

		if ( $this->tagGroups['user_confirmation'] ) {
			$this->_fillUserConfirmationTags( $tags );
		}

		if ( $this->tagGroups['user_cancellation'] ) {
			$this->_fillUserCancellationTags( $tags );
		}

		if ( $this->tagGroups['payment'] ) {
			$this->_fillPaymentTags( $tags );
		}

		$tags = apply_filters( 'mphb_email_tags', $tags );

		foreach ( $tags as $tag ) {
			$this->addTag( $tag['name'], $tag['description'], $tag );
		}
	}

	private function _fillGlobalTags( &$tags ){
		$globalTags = array(
			array(
				'name'			 => 'site_title',
				'description'	 => __( 'Site title (set in Settings > General)', 'motopress-hotel-booking' ),
			)
		);

		$tags = array_merge( $tags, $globalTags );
	}

	private function _fillBookingTags( &$tags ){
		$deprecatedTitle = sprintf( __( 'Use %s tag for reserved accommodations details.', 'motopress-hotel-booking' ), '%reserved_rooms_details%' );
		$bookingTags	 = array(
			// Booking
			array(
				'name'			 => 'booking_id',
				'description'	 => __( 'Booking ID', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'booking_edit_link',
				'description'	 => __( 'Booking Edit Link', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'booking_total_price',
				'description'	 => __( 'Booking Total Price', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'check_in_date',
				'description'	 => __( 'Check-in Date', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'check_out_date',
				'description'	 => __( 'Check-out Date', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'check_in_time',
				'description'	 => __( 'Check-in Time', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'check_out_time',
				'description'	 => __( 'Check-out Time', 'motopress-hotel-booking' ),
			),
			array(
				'name'				 => 'adults',
				'description'		 => __( 'Adults', 'motopress-hotel-booking' ),
				'deprecated'		 => true,
				'deprecated_title'	 => $deprecatedTitle
			),
			array(
				'name'				 => 'children',
				'description'		 => __( 'Children', 'motopress-hotel-booking' ),
				'deprecated'		 => true,
				'deprecated_title'	 => $deprecatedTitle
			),
			array(
				'name'				 => 'services',
				'description'		 => __( 'Services', 'motopress-hotel-booking' ),
				'deprecated'		 => true,
				'deprecated_title'	 => $deprecatedTitle
			),
			// Customer
			array(
				'name'			 => 'customer_first_name',
				'description'	 => __( 'Customer First Name', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'customer_last_name',
				'description'	 => __( 'Customer Last Name', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'customer_email',
				'description'	 => __( 'Customer Email', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'customer_phone',
				'description'	 => __( 'Customer Phone', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'customer_note',
				'description'	 => __( 'Customer Note', 'motopress-hotel-booking' ),
			),
			// Room Type
			array(
				'name'				 => 'room_type_id',
				'description'		 => __( 'Accommodation Type ID', 'motopress-hotel-booking' ),
				'deprecated'		 => true,
				'deprecated_title'	 => $deprecatedTitle
			),
			array(
				'name'				 => 'room_type_link',
				'description'		 => __( 'Accommodation Type Link', 'motopress-hotel-booking' ),
				'deprecated'		 => true,
				'deprecated_title'	 => $deprecatedTitle
			),
			array(
				'name'				 => 'room_type_title',
				'description'		 => __( 'Accommodation Type Title', 'motopress-hotel-booking' ),
				'deprecated'		 => true,
				'deprecated_title'	 => $deprecatedTitle
			),
			array(
				'name'				 => 'room_type_categories',
				'description'		 => __( 'Accommodation Type Categories', 'motopress-hotel-booking' ),
				'deprecated'		 => true,
				'deprecated_title'	 => $deprecatedTitle
			),
			array(
				'name'				 => 'room_type_bed_type',
				'description'		 => __( 'Accommodation Type Bed', 'motopress-hotel-booking' ),
				'deprecated'		 => true,
				'deprecated_title'	 => $deprecatedTitle
			),
			array(
				'name'				 => 'room_rate_title',
				'description'		 => __( 'Accommodation Rate Title', 'motopress-hotel-booking' ),
				'deprecated'		 => true,
				'deprecated_title'	 => $deprecatedTitle
			),
			array(
				'name'				 => 'room_rate_description',
				'description'		 => __( 'Accommodation Rate Description', 'motopress-hotel-booking' ),
				'deprecated'		 => true,
				'deprecated_title'	 => $deprecatedTitle
			),
			array(
				'name'			 => 'reserved_rooms_details',
				'description'	 => __( 'Reserved Accommodations Details', 'motopress-hotel-booking' ),
			)
		);

		$tags = array_merge( $tags, $bookingTags );
	}

	private function _fillUserConfirmationTags( &$tags ){
		$userConfirmationTags = array(
			array(
				'name'			 => 'user_confirm_link',
				'description'	 => __( 'Confirmation Link', 'motopress-hotel-booking' )
			),
			array(
				'name'			 => 'user_confirm_link_expire',
				'description'	 => __( 'Confirmation Link Expiration Time ( UTC )', 'motopress-hotel-booking' )
			)
		);

		$tags = array_merge( $tags, $userConfirmationTags );
	}

	private function _fillUserCancellationTags( &$tags ){
		$deprecatedTitle		 = sprintf( __( 'Use %s tag for cancellation section.', 'motopress-hotel-booking' ), '%cancellation_details%' );
		$userCancellationTags	 = array(
			array(
				'name'				 => 'user_cancel_link',
				'description'		 => __( 'User Cancellation Link', 'motopress-hotel-booking' ),
				'deprecated'		 => true,
				'deprecated_title'	 => $deprecatedTitle
			),
			array(
				'name'			 => 'cancellation_details',
				'description'	 => __( 'Cancellation Details (if enabled)', 'motopress-hotel-booking' ),
			),
		);

		$tags = array_merge( $tags, $userCancellationTags );
	}

	private function _fillPaymentTags( &$tags ){
		$paymentTags = array(
			array(
				'name'			 => 'payment_amount',
				'description'	 => __( 'The total price of payment', 'motopress-hotel-booking' )
			),
			array(
				'name'			 => 'payment_id',
				'description'	 => __( 'The unique ID of payment', 'motopress-hotel-booking' )
			),
			array(
				'name'			 => 'payment_method',
				'description'	 => __( 'The method of payment', 'motopress-hotel-booking' )
			),
		);

		$tags = array_merge( $tags, $paymentTags );
	}

	/**
	 *
	 * @param \MPHB\Entities\Booking $booking
	 */
	public function setupBooking( $booking ){
		$this->booking = $booking;
	}

	/**
	 *
	 * @param \MPHB\Entities\Payment $payment
	 */
	public function setupPayment( $payment ){
		$this->payment = $payment;
	}

	/**
	 *
	 * @param array $match
	 * @param string $match[0] Tag
	 *
	 * @return string
	 */
	public function replaceTag( $match ){

		$tag = str_replace( '%', '', $match[0] );

		$replaceText = '';

		switch ( $tag ) {

			// Global
			case 'site_title':
				$replaceText = get_bloginfo( 'name' );
				break;
			case 'check_in_time':
				$replaceText = MPHB()->settings()->dateTime()->getCheckInTimeWPFormatted();
				break;
			case 'check_out_time':
				$replaceText = MPHB()->settings()->dateTime()->getCheckOutTimeWPFormatted();
				break;

			// Booking
			case 'booking_id':
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getId();
				}
				break;
			case 'booking_edit_link':
				if ( isset( $this->booking ) ) {
					$replaceText = mphb_get_edit_post_link_for_everyone( $this->booking->getId() );
				}
				break;
			case 'booking_total_price':
				if ( isset( $this->booking ) ) {
					ob_start();
					Views\BookingView::renderTotalPriceHTML( $this->booking );
					$replaceText = ob_get_clean();
				}
				break;
			case 'check_in_date':
				if ( isset( $this->booking ) ) {
					ob_start();
					Views\BookingView::renderCheckInDateWPFormatted( $this->booking );
					$replaceText = ob_get_clean();
				}
				break;
			case 'check_out_date':
				if ( isset( $this->booking ) ) {
					ob_start();
					Views\BookingView::renderCheckOutDateWPFormatted( $this->booking );
					$replaceText = ob_get_clean();
				}
				break;
			case 'reserved_rooms_details':
				if ( isset( $this->booking ) ) {
					$replaceText = MPHB()->emails()->getReservedRoomsTemplater()->process( $this->booking );
				}
				break;

			// Customer
			case 'customer_first_name':
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getCustomer()->getFirstName();
				}
				break;
			case 'customer_last_name':
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getCustomer()->getLastName();
				}
				break;
			case 'customer_email':
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getCustomer()->getEmail();
				}
				break;
			case 'customer_phone';
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getCustomer()->getPhone();
				}
				break;
			case 'customer_note':
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getNote();
				}
				break;
			case 'user_confirm_link':
				if ( isset( $this->booking ) ) {
					$replaceText = MPHB()->userActions()->getBookingConfirmationAction()->generateLink( $this->booking );
				}
				break;
			case 'user_confirm_link_expire':
				if ( isset( $this->booking ) ) {
					$expireTime	 = $this->booking->retrieveExpiration( 'user' );
					$replaceText = date_i18n( MPHB()->settings()->dateTime()->getDateTimeFormatWP(), $expireTime );
				}
				break;
			case 'cancellation_details':
				if ( isset( $this->booking ) && MPHB()->settings()->main()->canUserCancelBooking() ) {
					$replaceText = MPHB()->emails()->getCancellationTemplater()->process( $this->booking );
				}
				break;

			// Payment
			case 'payment_amount':
				if ( isset( $this->payment ) ) {
					$amountAtts	 = array(
						'currency_symbol' => MPHB()->settings()->currency()->getBundle()->getSymbol( $this->payment->getCurrency() )
					);
					$replaceText = mphb_format_price( $this->payment->getAmount(), $amountAtts );
				}
				break;
			case 'payment_id':
				if ( isset( $this->payment ) ) {
					$replaceText = $this->payment->getId();
				}
				break;
			case 'payment_method':
				if ( isset( $this->payment ) ) {
					$gateway	 = MPHB()->gatewayManager()->getGateway( $this->payment->getGatewayId() );
					$replaceText = $gateway ? $gateway->getTitle() : '';
				}
				break;
			// Deprecated
			case 'adults':
				if ( isset( $this->booking ) ) {
					$reservedRoomsList = $this->booking->getReservedRooms();
					if ( !empty( $reservedRoomsList ) ) {
						$reservedRoom	 = reset( $reservedRoomsList );
						$replaceText	 = $reservedRoom->getAdults();
					}
				}
				break;
			case 'children':
				if ( isset( $this->booking ) ) {
					$reservedRoomsList = $this->booking->getReservedRooms();
					if ( !empty( $reservedRoomsList ) ) {
						$reservedRoom	 = reset( $reservedRoomsList );
						$replaceText	 = $reservedRoom->getChildren();
					}
				}
				break;
			case 'services':
				if ( isset( $this->booking ) ) {
					$reservedRoomsList = $this->booking->getReservedRooms();
					if ( !empty( $reservedRoomsList ) ) {
						$reservedRoom	 = reset( $reservedRoomsList );
						ob_start();
						Views\ReservedRoomView::renderServicesList( $reservedRoom );
						$replaceText	 = ob_get_clean();
					}
				}
				break;
			case 'room_type_id':
				if ( isset( $this->booking ) ) {
					$reservedRoomsList = $this->booking->getReservedRooms();
					if ( !empty( $reservedRoomsList ) ) {
						$reservedRoom	 = reset( $reservedRoomsList );
						$roomType		 = MPHB()->getRoomTypeRepository()->findById( $reservedRoom->getRoomTypeId() );
						$replaceText	 = ( $roomType ) ? $roomType->getId() : '';
					}
				}
				break;
			case 'room_type_link':
				if ( isset( $this->booking ) ) {
					$reservedRoomsList = $this->booking->getReservedRooms();
					if ( !empty( $reservedRoomsList ) ) {
						$reservedRoom	 = reset( $reservedRoomsList );
						$roomType		 = MPHB()->getRoomTypeRepository()->findById( $reservedRoom->getRoomTypeId() );
						$roomType		 = apply_filters( '_mphb_translate_room_type', $roomType, $this->booking->getLanguage() );
						$replaceText	 = ( $roomType ) ? $roomType->getLink() : '';
					}
				}
				break;
			case 'room_type_title':
				if ( isset( $this->booking ) ) {
					$reservedRoomsList = $this->booking->getReservedRooms();
					if ( !empty( $reservedRoomsList ) ) {
						$reservedRoom	 = reset( $reservedRoomsList );
						$roomType		 = MPHB()->getRoomTypeRepository()->findById( $reservedRoom->getRoomTypeId() );
						$roomType		 = apply_filters( '_mphb_translate_room_type', $roomType, $this->booking->getLanguage() );
						$replaceText	 = ( $roomType ) ? $roomType->getTitle() : '';
					}
				}
				break;
			case 'room_type_categories':
				if ( isset( $this->booking ) ) {
					$reservedRoomsList = $this->booking->getReservedRooms();
					if ( !empty( $reservedRoomsList ) ) {
						$reservedRoom	 = reset( $reservedRoomsList );
						$roomType		 = MPHB()->getRoomTypeRepository()->findById( $reservedRoom->getRoomTypeId() );
						$roomType		 = apply_filters( '_mphb_translate_room_type', $roomType, $this->booking->getLanguage() );
						$replaceText	 = ( $roomType ) ? implode( ', ', wp_list_pluck( $roomType->getCategories(), 'name' ) ) : '';
					}
				}
				break;
			case 'room_type_bed_type':
				if ( isset( $this->booking ) ) {
					$reservedRoomsList = $this->booking->getReservedRooms();
					if ( !empty( $reservedRoomsList ) ) {
						$reservedRoom	 = reset( $reservedRoomsList );
						$roomType		 = MPHB()->getRoomTypeRepository()->findById( $reservedRoom->getRoomTypeId() );
						$roomType		 = apply_filters( '_mphb_translate_room_type', $roomType, $this->booking->getLanguage() );
						$replaceText	 = ( $roomType ) ? $roomType->getBedType() : '';
					}
				}
				break;
			case 'room_rate_title':
				if ( isset( $this->booking ) ) {
					$reservedRoomsList = $this->booking->getReservedRooms();
					if ( !empty( $reservedRoomsList ) ) {
						$reservedRoom	 = reset( $reservedRoomsList );
						$roomRate		 = MPHB()->getRateRepository()->findById( $reservedRoom->getRateId() );
						$roomRate		 = $roomRate ? apply_filters( '_mphb_translate_rate', $roomRate, $this->booking->getLanguage() ) : $roomRate;
						$replaceText	 = $roomRate ? $roomRate->getTitle() : '';
					}
				}
				break;
			case 'room_rate_description':
				if ( isset( $this->booking ) ) {
					$reservedRoomsList = $this->booking->getReservedRooms();
					if ( !empty( $reservedRoomsList ) ) {
						$reservedRoom	 = reset( $reservedRoomsList );
						$roomRate		 = MPHB()->getRateRepository()->findById( $reservedRoom->getRateId() );
						$roomRate		 = $roomRate ? apply_filters( '_mphb_translate_rate', $roomRate, $this->booking->getLanguage() ) : $roomRate;
						$replaceText	 = $roomRate ? $roomRate->getDescription() : '';
					}
				}
				break;
			case 'user_cancel_link':
				if ( isset( $this->booking ) && MPHB()->settings()->main()->canUserCancelBooking() ) {
					$replaceText = MPHB()->userActions()->getBookingCancellationAction()->generateLink( $this->booking );
				}
				break;
		}

		$replaceText = apply_filters( 'mphb_email_replace_tag', $replaceText, $tag );

		return $replaceText;
	}

}
