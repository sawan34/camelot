<?php

namespace MPHB\Entities;

class Booking {

	/**
	 *
	 * @var int
	 */
	private $id;

	/**
	 *
	 * @var \DateTime
	 */
	private $checkInDate;

	/**
	 *
	 * @var \DateTime
	 */
	private $checkOutDate;

	/**
	 *
	 * @var ReservedRoom[]
	 */
	private $reservedRooms = array();

	/**
	 *
	 * @var Customer
	 */
	private $customer;

	/**
	 *
	 * @var string
	 */
	private $note;

	/**
	 *
	 * @var float
	 */
	private $totalPrice = 0.0;

	/**
	 *
	 * @var string
	 */
	private $status;

	/**
	 * Language of customer
	 *
	 * @var string
	 */
	private $language;

	/**
	 *
	 * @var int
	 */
	private $couponId;

	/**
	 *
	 * @var string
	 */
	private $iCalProdid;

	/**
	 *
	 * @var string
	 */
	private $iCalSummary = '';

	/**
	 *
	 * @var string
	 */
	private $iCalDescription = '';

	/**
	 *
	 * @param array $atts
	 */
	public function __construct( $atts ){
		$this->setupParameters( $atts );
	}

	/**
	 *
	 * @param array			 $atts
	 * @param int			 $atts['id']
	 * @param \DateTime		 $atts['check_in_date']
	 * @param \DateTime		 $atts['check_out_date']
	 * @param ReservedRoom[] $atts['reserved_rooms']
	 * @param Customer		 $atts['customer']
	 * @param float			 $atts['total_price']
	 * @param string		 $atts['note']
	 * @param string		 $atts['language']
	 * @param string		 $atts['status']
	 * @param int			 $atts['coupon_id'] Optional.
	 *
	 */
	public static function create( $atts ){
		return new self( $atts );
	}

	/**
	 *
	 * @param array			 $atts
	 * @param int			 $atts['id']
	 * @param \DateTime		 $atts['check_in_date']
	 * @param \DateTime		 $atts['check_out_date']
	 * @param ReservedRoom[] $atts['reserved_rooms']
	 * @param Customer		 $atts['customer']
	 * @param float			 $atts['total_price']
	 * @param string		 $atts['note']
	 * @param string		 $atts['status']
	 * @param string		 $atts['language']
	 *
	 */
	protected function setupParameters( $atts = array() ){

		if ( isset( $atts['id'] ) ) {
			$this->id = $atts['id'];
		}

		if ( isset( $atts['check_in_date'], $atts['check_out_date'] ) &&
			is_a( $atts['check_in_date'], '\DateTime' ) &&
			is_a( $atts['check_out_date'], '\DateTime' )
		) {
			$this->checkInDate	 = $atts['check_in_date'];
			$this->checkOutDate	 = $atts['check_out_date'];
		}

		if ( isset( $atts['reserved_rooms'] ) ) {
			$this->reservedRooms = $atts['reserved_rooms'];
		}

		if ( isset( $atts['customer'] ) ) {
			$this->customer = $atts['customer'];
		}

		$this->status = isset( $atts['status'] ) ? $atts['status'] : \MPHB\PostTypes\BookingCPT\Statuses::STATUS_AUTO_DRAFT;

		if ( isset( $atts['note'] ) ) {
			$this->note = $atts['note'];
		}

		if ( isset( $atts['total_price'] ) ) {
			$this->totalPrice = $atts['total_price'];
		} else {
			$this->updateTotal();
		}

		if ( isset( $atts['coupon_id'] ) ) {
			$this->couponId = $atts['coupon_id'];
		}

		if ( !empty( $atts['ical_prodid'] ) ) {
			$this->iCalProdid = $atts['ical_prodid'];
		} else {
			$this->iCalProdid = '';
		}

		if ( isset( $atts['ical_summary'] ) ) {
			// Empty string is correct value, so empty() is not appliable
			$this->iCalSummary = $atts['ical_summary'];
		}

		if ( isset( $atts['ical_description'] ) ) {
			// Empty string is correct value, so empty() is not appliable
			$this->iCalDescription = $atts['ical_description'];
		}

		$this->language = isset( $atts['language'] ) ? $atts['language'] : MPHB()->translation()->getCurrentLanguage();
	}

	/**
	 *
	 * @param string $status
	 */
	public function setStatus( $status ){
		$this->status = $status;
	}

	public function generateKey(){
		$key = uniqid( "booking_{$this->id}_", true );
		update_post_meta( $this->id, 'mphb_key', $key );
		return $key;
	}

	public function updateTotal(){
		$this->totalPrice = $this->calcPrice();
	}

	/**
	 *
	 * @return array
	 */
	public function getPriceBreakdown(){

		$coupon = null;
		$discount = 0; // %
		if ( MPHB()->settings()->main()->isCouponsEnabled() && $this->couponId ) {
			$coupon = MPHB()->getCouponRepository()->findById( $this->couponId );
			if( $coupon && $coupon->validate( $this ) ) {
				$discount = $coupon->getAmount();
				$discount = max( 0, min( $discount, 100 ) );
			} else {
				$coupon = null;
			}
		}

		$roomsBreakdown = array();

		foreach ( $this->reservedRooms as $reservedRoom ) {
			$roomsBreakdown[] = $reservedRoom->getPriceBreakdown( $this->checkInDate, $this->checkOutDate, $this->language, $discount );
		}

		$priceBreakdown = array(
			'rooms'	 => $roomsBreakdown,
			'total'	 => $this->calcPrice()
		);

		if (
			MPHB()->settings()->main()->getConfirmationMode() === 'payment' &&
			MPHB()->settings()->payment()->getAmountType() === 'deposit'
		) {
			$priceBreakdown['deposit'] = $this->calcDepositAmount();
		}

		if ( !is_null( $coupon ) ) {
			$priceBreakdown['coupon'] = array(
				'code'		 => $coupon->getCode(),
				'discount'	 => $coupon->calcDiscountAmount( $this )
			);
		}

		return $priceBreakdown;
	}

	/**
	 *
	 * @return float
	 */
	public function calcPrice(){

		$price = 0.0;

		if ( is_null( $this->checkInDate ) || is_null( $this->checkOutDate ) ) {
			return $price;
		}

		$coupon = null;
		$discount = 0; // %
		if ( MPHB()->settings()->main()->isCouponsEnabled() && $this->couponId ) {
			$coupon = MPHB()->getCouponRepository()->findById( $this->couponId );
			if( $coupon && $coupon->validate( $this ) ) {
				$discount = $coupon->getAmount();
				$discount = max( 0, min( $discount, 100 ) );
			} else {
				$coupon = null;
			}
		}

		foreach ( $this->reservedRooms as $reservedRoom ) {
			$reservedRoomPrice = $reservedRoom->calcPrice( $this->checkInDate, $this->checkOutDate, $discount );
			$price += $reservedRoomPrice;
		}

		if ( !is_null( $coupon ) ) {
			$discount	 = $coupon->calcDiscountAmount( $this );
			$price		 = max( 0, $price - $discount );
		}

		$price = apply_filters( 'mphb_booking_calculate_total_price', $price, $this );

		return $price;
	}

	/**
	 *
	 * @return float
	 */
	public function calcDepositAmount(){

		$totalPrice = $this->calcPrice();

		if ( MPHB()->settings()->payment()->getAmountType() === 'deposit' ) {

			$depositAmount = (float) MPHB()->settings()->payment()->getDepositAmount();

			if ( MPHB()->settings()->payment()->getDepositType() === 'percent' ) {
				$deposit = round( $totalPrice * ( $depositAmount / 100 ), 2 );
			} else {
				$deposit = $depositAmount;
			}
		} else {
			$deposit = $totalPrice;
		}

		return $deposit;
	}

	/**
	 *
	 * @param string $message
	 * @param int $author
	 */
	public function addLog( $message, $author = null ){
		$author = !is_null( $author ) ? $author : ( is_admin() ? get_current_user_id() : 0);

		$commentdata = array(
			'comment_post_ID'		 => $this->getId(),
			'comment_content'		 => $message,
			'user_id'				 => $author,
			'comment_date'			 => mphb_current_time( 'mysql' ),
			'comment_date_gmt'		 => mphb_current_time( 'mysql', get_option( 'gmt_offset' ) ),
			'comment_approved'		 => 1,
			'comment_parent'		 => 0,
			'comment_author'		 => '',
			'comment_author_IP'		 => '',
			'comment_author_url'	 => '',
			'comment_author_email'	 => '',
			'comment_type'			 => 'mphb_booking_log'
		);

		wp_insert_comment( $commentdata );
	}

	public function getRoomLink(){
		return $this->room->getLink();
	}

	public function getLogs(){

		do_action( 'mphb_booking_before_get_logs' );

		$logs = get_comments( array(
			'post_id'	 => $this->getId(),
			'order'		 => 'ASC'
			) );

		do_action( 'mphb_booking_after_get_logs' );

		return $logs;
	}

	/**
	 *
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 *
	 * @return string
	 */
	public function getKey(){
		return get_post_meta( $this->id, 'mphb_key', true );
	}

	/**
	 *
	 * @return \DateTime
	 */
	public function getCheckInDate(){
		return $this->checkInDate;
	}

	/**
	 *
	 * @return \DateTime
	 */
	public function getCheckOutDate(){
		return $this->checkOutDate;
	}

	/**
	 *
	 * @return ReservedRoom[]
	 */
	public function getReservedRooms(){
		return $this->reservedRooms;
	}

	/**
	 *
	 * @return Customer
	 */
	public function getCustomer(){
		return $this->customer;
	}

	/**
	 *
	 * @return string
	 */
	public function getNote(){
		return $this->note;
	}

	/**
	 *
	 * @return float
	 */
	public function getTotalPrice(){
		return $this->totalPrice;
	}

	/**
	 *
	 * @return string
	 */
	public function getStatus(){
		return $this->status;
	}

	/**
	 *
	 * @return array of dates where key is date in 'Y-m-d' format and value is date in frontend date format
	 */
	public function getDates( $fromToday = false ){

		$fromDate	 = $this->checkInDate->format( 'Y-m-d' );
		$toDate		 = $this->checkOutDate->format( 'Y-m-d' );

		if ( $fromToday ) {
			$today		 = mphb_current_time( 'Y-m-d' );
			$fromDate	 = $fromDate >= $today ? $fromDate : $today;
		}
		return \MPHB\Utils\DateUtils::createDateRangeArray( $fromDate, $toDate );
	}

	/**
	 * Set expiration time of pending confirmation for booking
	 *
	 * @param string $type Possible types: user, payment.
	 * @param int $expirationTime
	 */
	public function updateExpiration( $type, $expirationTime ){
		update_post_meta( $this->id, "mphb_pending_{$type}_expired", $expirationTime );
	}

	/**
	 * Retrieve expiration time for booking in UTC.
	 *
	 * @param string $type Possible types: user, payment.
	 * @return int
	 */
	public function retrieveExpiration( $type ){
		return intval( get_post_meta( $this->id, "mphb_pending_{$type}_expired", true ) );
	}

	/**
	 * Delete expiration time of pending confirmation for booking.
	 *
	 * @param string $type Possible types: user, payment.
	 */
	public function deleteExpiration( $type ){
		delete_post_meta( $this->id, "mphb_pending_{$type}_expired" );
	}

	/**
	 *
	 * @return string
	 */
	public function getICalProdid(){
		return $this->iCalProdid;
	}

	/**
	 *
	 * @return string|null
	 */
	public function getICalSummary(){
		return $this->iCalSummary;
	}

	/**
	 *
	 * @return string|null
	 */
	public function getICalDescription(){
		return $this->iCalDescription;
	}

	/**
	 * Retrieve language of customer
	 *
	 * @return string
	 */
	public function getLanguage(){
		return $this->language;
	}

	/**
	 *
	 * @param int $paymentId
	 * @return bool
	 */
	public function isExpectPayment( $paymentId ){
		$expectPayment = get_post_meta( $this->id, '_mphb_wait_payment', true );
		return $paymentId == $expectPayment;
	}

	/**
	 *
	 * @param int $paymentId
	 */
	public function setExpectPayment( $paymentId ){
		update_post_meta( $this->id, '_mphb_wait_payment', $paymentId );
	}

	/**
	 *
	 * @param Coupon $coupon
	 * @return boolean|\WP_Error
	 */
	public function applyCoupon( $coupon ){

		$isValidCoupon = $coupon->validate( $this, true );
		if ( is_wp_error( $isValidCoupon ) ) {
			return $isValidCoupon;
		}

		$this->couponId = $coupon->getId();
		$this->updateTotal();

		return true;
	}

	/**
	 *
	 * @return string
	 */
	public function getCouponCode(){
		$coupon = MPHB()->getCouponRepository()->findById( $this->couponId );
		return $coupon ? $coupon->getCode() : $this->couponId;
	}

	/**
	 *
	 * @return int
	 */
	public function getCouponId(){
		return $this->couponId;
	}

	/**
	 *
	 * @return bool
	 */
	public function isImported(){
		$roomsTotal = count( $this->reservedRooms );
		$roomsImported = array_reduce( $this->reservedRooms, function( $count, \MPHB\Entities\ReservedRoom $room ){
			return $room->getRateId() == 0 ? $count + 1 : $count;
		}, 0 );

		return $roomsTotal > 0 && $roomsImported == $roomsTotal;
	}

}
