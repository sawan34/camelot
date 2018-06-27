<?php

namespace MPHB\Shortcodes;

class SearchResultsShortcode extends AbstractShortcode {

	protected $name = 'mphb_search_results';

	const NONCE_NAME			 = 'mphb-search-available-room-nonce';
	const NONCE_ACTION		 = 'mphb-search-available-room';
	const SORTING_MODE_PRICE	 = 'price';
	const SORTING_MODE_ORDER	 = 'order';

	/**
	 *
	 * @var int
	 */
	private $adults;

	/**
	 *
	 * @var int
	 */
	private $children;

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
	 * @var array
	 */
	private $errors = array();

	/**
	 *
	 * @var bool
	 */
	private $isCorrectInputData = false;

	/**
	 *
	 * @var bool
	 */
	private $isCorrectPage = false;

	/**
	 *
	 * @var array
	 */
	private $availableRoomsCount;

	/**
	 *
	 * @var bool
	 */
	private $isShowGallery;

	/**
	 *
	 * @var bool
	 */
	private $isShowFeaturedImage;

	/**
	 *
	 * @var bool
	 */
	private $isShowTitle;

	/**
	 *
	 * @var bool
	 */
	private $isShowExcerpt;

	/**
	 *
	 * @var bool
	 */
	private $isShowDetails;

	/**
	 *
	 * @var bool
	 */
	private $isShowPrice;

	/**
	 *
	 * @var bool
	 */
	private $isShowViewButton;

	/**
	 *
	 * @var string
	 */
	private $sortingMode;

	/**
	 *
	 * @var int
	 */
	private $stickedRoomType;

	public function addActions(){
		parent::addActions();
		add_action( 'wp', array( $this, 'setup' ) );

		add_filter( 'the_posts', array( $this, 'stickRequestedRoomType' ), 10, 2 );

		add_action( 'mphb_sc_search_results_before_loop', array( $this, 'renderRecommendation' ) );
		add_action( 'mphb_sc_search_results_before_loop', array( $this, 'renderReservationCart' ) );

		add_action( 'mphb_sc_search_results_render_gallery', array( '\MPHB\Views\LoopRoomTypeView', 'renderGallery' ) );
		add_action( 'mphb_sc_search_results_render_image', array( '\MPHB\Views\LoopRoomTypeView', 'renderFeaturedImage' ) );
		add_action( 'mphb_sc_search_results_render_title', array( '\MPHB\Views\LoopRoomTypeView', 'renderTitle' ) );
		add_action( 'mphb_sc_search_results_render_excerpt', array( '\MPHB\Views\LoopRoomTypeView', 'renderExcerpt' ) );
		add_action( 'mphb_sc_search_results_render_details', array( '\MPHB\Views\LoopRoomTypeView', 'renderAttributes' ) );
		add_action( 'mphb_sc_search_results_render_price', array( '\MPHB\Views\LoopRoomTypeView', 'renderPriceForDates' ), 10, 2 );
		add_action( 'mphb_sc_search_results_render_view_button', array( '\MPHB\Views\LoopRoomTypeView', 'renderViewDetailsButton' ) );
		add_action( 'mphb_sc_search_results_render_book_button', array( $this, 'renderBookButton' ) );

		add_action( 'mphb_sc_search_results_error', array( '\MPHB\Views\GlobalView', 'prependBr' ) );
	}

	public function render( $atts, $content = '', $shortcodeName ){

		$defaultAtts = array(
			'gallery'			 => 'true',
			'featured_image'	 => 'true',
			'title'				 => 'true',
			'excerpt'			 => 'true',
			'details'			 => 'true',
			'price'				 => 'true',
			'view_button'		 => 'true',
			'default_sorting'	 => self::SORTING_MODE_ORDER,
			'class'				 => ''
		);

		$atts = shortcode_atts( $defaultAtts, $atts, $shortcodeName );

		$this->isShowGallery		 = \MPHB\Utils\ValidateUtils::validateBool( $atts['gallery'] );
		$this->isShowFeaturedImage	 = \MPHB\Utils\ValidateUtils::validateBool( $atts['featured_image'] );
		$this->isShowTitle			 = \MPHB\Utils\ValidateUtils::validateBool( $atts['title'] );
		$this->isShowExcerpt		 = \MPHB\Utils\ValidateUtils::validateBool( $atts['excerpt'] );
		$this->isShowDetails		 = \MPHB\Utils\ValidateUtils::validateBool( $atts['details'] );
		$this->isShowPrice			 = \MPHB\Utils\ValidateUtils::validateBool( $atts['price'] );
		$this->isShowViewButton		 = \MPHB\Utils\ValidateUtils::validateBool( $atts['view_button'] );
		$this->sortingMode			 = $atts['default_sorting'];

		ob_start();

		if ( $this->isCorrectPage && $this->isCorrectInputData ) {

			$this->setupMatchedRoomTypes();

			MPHB()->getPublicScriptManager()->enqueue();

			if ( !empty( $this->availableRoomsCount ) ) {
				$this->mainLoop();
			} else {
				$this->renderResultsInfo( 0 );
			}
		} else {
			$this->showErrorsMessage();
		}

		$content = ob_get_clean();

		$wrapperClass = apply_filters( 'mphb_sc_search_results_wrapper_class', 'mphb_sc_search_results-wrapper' );
		$wrapperClass .= empty( $wrapperClass ) ? $atts['class'] : ' ' . $atts['class'];
		return '<div class="' . esc_attr( $wrapperClass ) . '">' . $content . '</div>';
	}

	private function mainLoop(){

		$roomTypesQuery = $this->getRoomTypesQuery();

		$this->renderResultsInfo( $roomTypesQuery->post_count );

		if ( $roomTypesQuery->have_posts() ) {

			do_action( 'mphb_sc_search_results_before_loop', $roomTypesQuery );

			while ( $roomTypesQuery->have_posts() ) : $roomTypesQuery->the_post();

				do_action( 'mphb_sc_search_results_before_room' );

				$this->renderRoomType();

				do_action( 'mphb_sc_search_results_after_room' );

			endwhile;

			do_action( 'mphb_sc_search_results_after_loop', $roomTypesQuery );

			wp_reset_postdata();
		}
	}

	/**
	 *
	 * @return \WP_Query
	 */
	private function getRoomTypesQuery(){
		$queryAtts = array(
			'post_type'				 => MPHB()->postTypes()->roomType()->getPostType(),
			'post_status'			 => 'publish',
			'post__in'				 => array_keys( $this->availableRoomsCount ),
			'posts_per_page'		 => -1,
			'ignore_sticky_posts'	 => true
		);

		if ( !empty( $this->stickedRoomType ) ) {
			$queryAtts['mphb_stick_post'] = $this->stickedRoomType;
		}

		switch ( $this->sortingMode ) {
			case self::SORTING_MODE_PRICE :
				$queryAtts['orderby']	 = 'post__in';
				$queryAtts['order']		 = 'ASC';
				break;
			case self::SORTING_MODE_ORDER:
				$queryAtts['orderby']	 = 'menu_order';
				$queryAtts['order']		 = 'ASC';
				break;
		}

		return new \WP_Query( $queryAtts );
	}

	/**
	 *
	 * @global \WPDB $wpdb
	 * @return array of arrays [string $id, string $count]
	 *
	 */
	private function getAvailableRoomTypes(){
		global $wpdb;

		$roomsAtts = array(
			'availability'	 => 'locked',
			'from_date'		 => $this->checkInDate,
			'to_date'		 => $this->checkOutDate
		);

		$lockedRooms	 = MPHB()->getRoomPersistence()->searchRooms( $roomsAtts );
		$lockedRoomsStr	 = join( ',', $lockedRooms );

		$query = "SELECT DISTINCT room_meta_room_type_id.meta_value AS id, COUNT(rooms.ID) AS count "
			. "FROM $wpdb->posts AS rooms "
			. "INNER JOIN $wpdb->postmeta AS room_meta_room_type_id "
			. "		ON ( rooms.ID = room_meta_room_type_id.post_id  ) "
			. "INNER JOIN $wpdb->posts AS room_types "
			. "		ON ( room_meta_room_type_id.meta_value = room_types.ID ) "
			. "WHERE 1=1 "
			. "AND rooms.post_type = '" . MPHB()->postTypes()->room()->getPostType() . "' "
			. (!empty( $lockedRoomsStr ) ? "AND rooms.ID NOT IN ( $lockedRoomsStr ) " : "" )
			. "AND rooms.post_status = 'publish' "
			. "AND room_meta_room_type_id.meta_key = 'mphb_room_type_id' "
			. "AND room_meta_room_type_id.meta_value IS NOT NULL "
			. "AND room_meta_room_type_id.meta_value <> '' "
			. "AND room_types.post_status = 'publish' "
			. "AND room_types.post_type = '" . MPHB()->postTypes()->roomType()->getPostType() . "' "
			. "GROUP BY room_meta_room_type_id.meta_value "
			. "DESC";

		$roomTypeDetails = $wpdb->get_results( $query, ARRAY_A );

		return $roomTypeDetails;
	}

	private function renderRoomType(){
		$templateAtts = array(
			'checkInDate'		 => $this->checkInDate,
			'checkOutDate'		 => $this->checkOutDate,
			'adults'			 => $this->adults,
			'children'			 => $this->children,
			'isShowGallery'		 => $this->isShowGallery,
			'isShowImage'		 => $this->isShowFeaturedImage,
			'isShowTitle'		 => $this->isShowTitle,
			'isShowExcerpt'		 => $this->isShowExcerpt,
			'isShowDetails'		 => $this->isShowDetails,
			'isShowPrice'		 => $this->isShowPrice,
			'isShowViewButton'	 => $this->isShowViewButton,
			// disabling book button by shortcode attribute is deprecated
			'isShowBookButton'	 => true
		);
		mphb_get_template_part( 'shortcodes/search-results/room-content', $templateAtts );
	}

	/**
	 *
	 * @return false|\WP_Query
	 */
	private function setupMatchedRoomTypes(){

		$checkInDate	 = $this->checkInDate;
		$checkOutDate	 = $this->checkOutDate;

		/**
		 * @since 2.4.0
		 */
		do_action( 'mphb_sc_search_results_before_search' );

		$roomTypeDetailsList = $this->getAvailableRoomTypes();

		$roomTypeDetailsList = array_filter( $roomTypeDetailsList, function( $roomTypeDetails ) use ( $checkInDate, $checkOutDate ) {

			$roomTypeId = $roomTypeDetails['id'];

			$roomType = MPHB()->getRoomTypeRepository()->findById( $roomTypeId );

			if ( !$roomType ) {
				return false;
			}

			$rateAtts = array(
				'check_in_date'	 => $checkInDate,
				'check_out_date' => $checkOutDate
			);

			if ( !MPHB()->getRateRepository()->isExistsForRoomType( $roomTypeId, $rateAtts ) ) {
				return false;
			}

			if ( !MPHB()->getRulesChecker()->verify( $checkInDate, $checkOutDate, $roomTypeId ) ) {
				return false;
			}

			return true;
		} );

		if ( $this->sortingMode === self::SORTING_MODE_PRICE ) {
			$roomTypesPriceList = array_map( function( $roomTypeDetails ) use ($checkInDate, $checkOutDate) {
				$roomType = MPHB()->getRoomTypeRepository()->findById( $roomTypeDetails['id'] );
				return $roomType->getDefaultPriceForDates( $checkInDate, $checkOutDate );
			}, $roomTypeDetailsList );

			// Replace numeric indexes with room type IDs
			$roomTypesPriceList = array_combine( wp_list_pluck( $roomTypeDetailsList, 'id' ), $roomTypesPriceList );

			usort( $roomTypeDetailsList, function( $roomType1, $roomType2 ) use ($roomTypesPriceList) {
				return $roomTypesPriceList[$roomType1['id']] > $roomTypesPriceList[$roomType2['id']] ? 1 : -1;
			} );
		}

		// Verify available rooms count
		array_walk( $roomTypeDetailsList, function( &$roomTypeDetails ) use ($checkInDate, $checkOutDate) {
			$roomTypeId	 = $roomTypeDetails['id'];
			$unavailableRoomsCount = MPHB()->getRulesChecker()->customRules()->getUnavailableRoomsCount( $checkInDate, $checkOutDate, $roomTypeId );
			$roomTypeDetails['count'] -= $unavailableRoomsCount;
		} );

		$roomTypeDetailsList = array_filter( $roomTypeDetailsList, function( $roomTypeDetails ) {
			return $roomTypeDetails['count'] > 0;
		} );

		// array_combine() issues E_WARNING on PHP 5.4- if one of the array is empty
		$ids	 = wp_list_pluck( $roomTypeDetailsList, 'id' );
		$counts	 = wp_list_pluck( $roomTypeDetailsList, 'count' );
		if ( !empty( $ids ) && !empty( $counts ) ) {
			$this->availableRoomsCount = array_combine( $ids, $counts );
		}
	}

	private function setupSearchData(){
		$this->adults				 = null;
		$this->children				 = null;
		$this->checkInDate			 = null;
		$this->checkOutDate			 = null;
		$this->isCorrectInputData	 = false;

		$input = $_GET;

		if ( isset( $input['mphb_adults'], $input['mphb_children'], $input['mphb_check_in_date'], $input['mphb_check_out_date'] ) ) {

			$this->parseInputData( $input );

			if ( $this->isCorrectInputData ) {
				MPHB()->searchParametersStorage()->save(
					array(
						'mphb_check_in_date'	 => $this->checkInDate->format( MPHB()->settings()->dateTime()->getDateTransferFormat() ),
						'mphb_check_out_date'	 => $this->checkOutDate->format( MPHB()->settings()->dateTime()->getDateTransferFormat() ),
						'mphb_adults'			 => $this->adults,
						'mphb_children'			 => $this->children
					)
				);
			}
		} else if ( MPHB()->searchParametersStorage()->hasStored() ) {
			$this->parseInputData( MPHB()->searchParametersStorage()->get() );
		}

		if ( !empty( $input['mphb_room_type_id'] ) ) {
			$roomTypeId				 = \MPHB\Utils\ValidateUtils::validateInt( $input['mphb_room_type_id'], 1 );
			$this->stickedRoomType	 = $roomTypeId ? $roomTypeId : null;
		}
	}

	/**
	 *
	 * @return bool
	 */
	private function parseInputData( $input ){
		$isCorrectAdults		 = $this->parseAdults( $input['mphb_adults'] );
		$isCorrectChildren		 = $this->parseChildren( $input['mphb_children'] );
		$isCorrectCheckInDate	 = $this->parseCheckInDate( $input['mphb_check_in_date'] );
		$isCorrectCheckOutDate	 = $this->parseCheckOutDate( $input['mphb_check_out_date'] );

		$this->isCorrectInputData = ( $isCorrectAdults && $isCorrectChildren && $isCorrectCheckInDate && $isCorrectCheckOutDate );

		return $this->isCorrectInputData;
	}

	public function setup(){
		if ( mphb_is_search_results_page() ) {
			$this->isCorrectPage = true;
			$this->setupSearchData();
			/**
			 * @since 2.4.0
			 */
			do_action( 'mphb_load_search_results_page', array(
				'check_in_date'	 => $this->checkInDate,
				'check_out_date' => $this->checkOutDate,
				'adults'		 => $this->adults,
				'children'		 => $this->children,
				'is_correct'	 => $this->isCorrectInputData
			) );
		}
	}

	/**
	 *
	 * @param string|int $adults
	 * @return bool
	 */
	private function parseAdults( $adults ){
		$adults = intval( $adults );
		if ( $adults >= MPHB()->settings()->main()->getMinAdults() && $adults <= MPHB()->settings()->main()->getSearchMaxAdults() ) {
			$this->adults = $adults;
			return true;
		} else {
			$this->errors[] = __( 'Adults number is not valid.', 'motopress-hotel-booking' );
			return false;
		}
	}

	/**
	 *
	 * @param int|string $children
	 * @return boolean
	 */
	private function parseChildren( $children ){
		$children = intval( $children );
		if ( $children >= MPHB()->settings()->main()->getMinChildren() && $children <= MPHB()->settings()->main()->getSearchMaxChildren() ) {
			$this->children = $children;
			return true;
		} else {
			$this->errors[] = __( 'Children number is not valid.', 'motopress-hotel-booking' );
			return false;
		}
	}

	/**
	 *
	 * @param string $date Date in front date format
	 * @return boolean
	 */
	private function parseCheckInDate( $date ){
		$checkInDateObj	 = \DateTime::createFromFormat( MPHB()->settings()->dateTime()->getDateTransferFormat(), $date );
		$todayDate		 = \DateTime::createFromFormat( 'Y-m-d', mphb_current_time( 'Y-m-d' ) );

		if ( !$checkInDateObj ) {
			$this->errors[] = __( 'Check-in date is not valid.', 'motopress-hotel-booking' );
			return false;
		} else if ( \MPHB\Utils\DateUtils::calcNights( $todayDate, $checkInDateObj ) < 0 ) {
			$this->errors[] = __( 'Check-in date cannot be earlier than today.', 'motopress-hotel-booking' );
			return false;
		}

		$this->checkInDate = $checkInDateObj;
		return true;
	}

	/**
	 *
	 * @param string $date Date in front date format
	 * @return boolean
	 */
	private function parseCheckOutDate( $date ){

		$checkOutDateObj = \MPHB\Utils\DateUtils::createCheckOutDate( MPHB()->settings()->dateTime()->getDateTransferFormat(), $date );

		if ( !$checkOutDateObj ) {
			$this->errors[] = __( 'Check-out date is not valid.', 'motopress-hotel-booking' );
			return false;
		}

		if ( isset( $this->checkInDate ) &&
			!MPHB()->getRulesChecker()->verify( $this->checkInDate, $checkOutDateObj )
		) {
			$this->errors[] = __( 'Nothing found. Please try again with different search parameters.', 'motopress-hotel-booking' );
			return false;
		}

		$this->checkOutDate = $checkOutDateObj;
		return true;
	}

	public function showErrorsMessage(){
		$templateAtts = array(
			'errors' => $this->errors
		);
		mphb_get_template_part( 'shortcodes/search-results/errors', $templateAtts );
	}

	/**
	 *
	 * @param int $roomTypeCount
	 */
	private function renderResultsInfo( $roomTypeCount ){
		$templateAtts = array(
			'roomTypesCount' => $roomTypeCount,
			'adults'		 => $this->adults,
			'children'		 => $this->children,
			'checkInDate'	 => \MPHB\Utils\DateUtils::formatDateWPFront( $this->checkInDate ),
			'checkOutDate'	 => \MPHB\Utils\DateUtils::formatDateWPFront( $this->checkOutDate )
		);
		mphb_get_template_part( 'shortcodes/search-results/results-info', $templateAtts );
	}

	public function renderReservationCart(){

		do_action( 'mphb_sc_search_results_reservation_cart_before' );

		$title = apply_filters( 'mphb_sc_search_results_reservation_cart_title', '' );

		if ( $title ) {
			?>
			<h2 class="mphb-reservation-cart-title"><?php echo $title; ?></h2>
		<?php } ?>
		<form action="<?php echo esc_url( MPHB()->settings()->pages()->getCheckoutPageUrl() ); ?>"
			  method="POST"
			  id="mphb-reservation-cart"
			  class="mphb-reservation-cart mphb-empty-cart">
			<input type="hidden" name="mphb_check_in_date"
				   value="<?php echo esc_attr( $this->checkInDate->format( MPHB()->settings()->dateTime()->getDateTransferFormat() ) ); ?>"/>
			<input type="hidden" name="mphb_check_out_date"
				   value="<?php echo esc_attr( $this->checkOutDate->format( MPHB()->settings()->dateTime()->getDateTransferFormat() ) ); ?>"/>
				   <?php wp_nonce_field( \MPHB\Shortcodes\CheckoutShortcode::NONCE_ACTION_CHECKOUT, \MPHB\Shortcodes\CheckoutShortcode::NONCE_NAME, true ); ?>
			<div class="mphb-reservation-details">
				<p class="mphb-empty-cart-message"><?php _e( 'Select from available accommodations.', 'motopress-hotel-booking' ); ?></p>
				<p class="mphb-cart-message"></p>
				<p class="mphb-cart-total-price">
					<span class="mphb-cart-total-price-title">
						<?php _e( 'Total:', 'motopress-hotel-booking' ); ?>
					</span>
					<span class="mphb-cart-total-price-value"></span>
				</p>
			</div>
			<button class="button mphb-button mphb-confirm-reservation"><?php _e( 'Confirm Reservation', 'motopress-hotel-booking' ); ?></button>
			<div class="mphb-clear"></div>
		</form>
		<?php
		do_action( 'mphb_sc_search_results_reservation_cart_after' );
	}

	/**
	 *
	 * @param \WP_Query $roomTypesQuery
	 */
	public function renderRecommendation( $roomTypesQuery ){

		if ( !MPHB()->settings()->main()->isEnabledRecommendation() ) {
			return;
		}

		$adults			 = $this->adults;
		$children		 = $this->children;
		$availableRooms	 = array_map( 'intval', $this->availableRoomsCount );
		$recommendation	 = $this->generateRecommmendation( $adults, $children, $availableRooms );

		if ( empty( $recommendation ) ) {
			return;
		}

		do_action( 'mphb_sc_search_results_recommendation_before' );

		$title = sprintf( _n( 'Recommended for %d adult', 'Recommended for %d adults', $this->adults, 'motopress-hotel-booking' ), $this->adults );
		if ( !empty( $this->children ) ) {
			$title .= sprintf( _n( ' and %d child', ' and %d children', $this->children, 'motopress-hotel-booking' ), $this->children );
		}
		$title	 = apply_filters( 'mphb_sc_search_results_recommendation_title', $title );
		?>
		<h2 class="mphb-recommendation-title"><?php echo $title; ?></h2>
		<form action="<?php echo MPHB()->settings()->pages()->getCheckoutPageUrl(); ?>"
			  method="POST"
			  id="mphb-recommendation"
			  class="mphb-recommendation">
				  <?php wp_nonce_field( \MPHB\Shortcodes\CheckoutShortcode::NONCE_ACTION_CHECKOUT, \MPHB\Shortcodes\CheckoutShortcode::RECOMMENDATION_NONCE_NAME, true ); ?>
			<input type="hidden" name="mphb_check_in_date"
				   value="<?php echo esc_attr( $this->checkInDate->format( MPHB()->settings()->dateTime()->getDateTransferFormat() ) ); ?>"/>
			<input type="hidden" name="mphb_check_out_date"
				   value="<?php echo esc_attr( $this->checkOutDate->format( MPHB()->settings()->dateTime()->getDateTransferFormat() ) ); ?>"/>
			<ul class="mphb-recommendation-details-list">
				<?php
				$total	 = 0.0;
				foreach ( $recommendation as $roomTypeId => $roomsCount ) {
					$roomType		 = MPHB()->getRoomTypeRepository()->findById( $roomTypeId );
					$roomType		 = apply_filters( '_mphb_translate_room_type', $roomType, null );
					$roomPrice		 = $roomType->getDefaultPriceForDates( $this->checkInDate, $this->checkOutDate );
					$price			 = $roomPrice * $roomsCount;
					$total += $price;
					?>
					<li>
						<input name="mphb_rooms_details[<?php echo esc_attr( $roomType->getOriginalId() ); ?>]"
							   type="hidden" value="<?php echo esc_attr( $roomsCount ); ?>">
						<div class="mphb-recommendation-item">
							<span class="mphb-recommedation-item-subtotal">
								<?php echo mphb_format_price( $price ); ?>
							</span>
							<span class="mphb-recommendation-item-count"><?php echo $roomsCount . ' &times; ' ?></span>
							<a href="<?php echo esc_url( $roomType->getLink() ); ?>" class="mphb-recommendation-item-link" target="_blank">
								<?php echo $roomType->getTitle(); ?>
							</a>
							<small class="mphb-recommendation-item-guests">
								<?php
								$roomAdults		 = $roomType->getAdultsCapacity();
								$roomChildren	 = $roomType->getChildrenCapacity();
								?>
								<span class="mphb-recommendation-item-guests-label">
									<?php _e( 'Max occupancy:', 'motopress-hotel-booking' ); ?>
								</span>
								<span class="mphb-recommendation-item-adults mphb-adults-<?php echo $roomAdults; ?>"><?php
									printf( _n( '%d adult', '%d adults', $roomAdults, 'motopress-hotel-booking' ), $roomAdults );
									?></span>
								<?php
								if ( $roomChildren > 0 ) {
									?>
									<span class="mphb-recommendation-item-adults-children-separator"><?php echo ', '; ?></span>
									<span class="mphb-recommendation-item-children mphb-children-<?php echo $roomChildren; ?>"><?php
										printf( _n( '%d child', '%d children', $roomChildren, 'motopress-hotel-booking' ), $roomChildren );
										?></span>
									<?php
								}
								?>
							</small>
						</div>
					</li>
				<?php } ?>
			</ul>
			<p	 class="mphb-recommendation-total">
				<span class="mphb-recommendation-total-title"><?php _e( 'Total:', 'motopress-hotel-booking' ); ?></span>
				<span class="mphb-recommendation-total-value"><?php echo mphb_format_price( $total ); ?></span>
			</p>
			<button class="button mphb-button mphb-recommendation-reserve-button">
				<?php _e( 'Reserve', 'motopress-hotel-booking' ); ?>
			</button>
			<div class="mphb-clear"></div>
		</form>
		<?php
		do_action( 'mphb_sc_search_results_recommendation_after' );
	}

	/**
	 * Generate basic naive recommendation
	 *
	 * @param int $adults
	 * @param int $children
	 * @param array $availableRooms Room type ids as keys and rooms count as values.
	 * @param bool $strict Optional. Forbid incomplete allocation. Default FALSE.
	 * @return array
	 */
	private function generateRecommmendation( $adults, $children, $availableRooms, $strict = false ){
		$adults		 = max( 0, $adults );
		$children	 = max( 0, $children );

		if ( $adults == 0 && $children == 0 ) {
			return array();
		}

		$recommendation = new \MPHB\Recommendation( $availableRooms );
		return $recommendation->generate( $adults, $children, $strict );
	}

	public function renderBookButton(){
		$roomType		 = MPHB()->getCurrentRoomType();
		$maxRoomsCount	 = isset( $this->availableRoomsCount[$roomType->getOriginalId()] ) ? $this->availableRoomsCount[$roomType->getOriginalId()] : 0;
		$roomPrice		 = $roomType->getDefaultPriceForDates( $this->checkInDate, $this->checkOutDate );
		?>
		<div class="mphb-reserve-room-section"
			 data-room-type-id="<?php echo esc_attr( $roomType->getOriginalId() ); ?>"
			 data-room-type-title="<?php echo esc_attr( $roomType->getTitle() ); ?>"
			 data-room-price="<?php echo esc_attr( $roomPrice ); ?>">
			<p class="mphb-rooms-quantity-wrapper">
				<select class="mphb-rooms-quantity" id="mphb-rooms-quantity-<?php echo $roomType->getOriginalId(); ?>">
					<?php for ( $count = 1; $count <= $maxRoomsCount; $count++ ) { ?>
						<option value="<?php echo esc_attr( $count ); ?>"><?php echo $count; ?></option>
					<?php } ?>
				</select>
				<span class="mphb-available-rooms-count"><?php
					echo sprintf( _n( 'of %d accommodation available.', 'of %d accommodations available.', $maxRoomsCount, 'motopress-hotel-booking' ), $maxRoomsCount );
					?></span>
			</p>
			<div class="mphb-rooms-reservation-message-wrapper">
				<a href="#" class="mphb-remove-from-reservation"><?php _e( 'Remove', 'motopress-hotel-booking' ); ?></a>
				<p class="mphb-rooms-reservation-message"></p>
			</div>
			<button class="button mphb-button mphb-book-button"><?php _e( 'Book', 'motopress-hotel-booking' ); ?></button>
			<button class="button mphb-button mphb-confirm-reservation"><?php _e( 'Confirm Reservation', 'motopress-hotel-booking' ); ?></button>
		</div>
		<?php
	}

	/**
	 *
	 * @param \WP_Post[] $posts
	 * @param \WP_Query $wp_query
	 */
	public function stickRequestedRoomType( $posts, $wp_query ){
		if ( !$wp_query->get( 'mphb_stick_post' ) ) {
			return $posts;
		}
		$position = array_search( $this->stickedRoomType, wp_list_pluck( $posts, 'ID' ) );
		if ( false !== $position ) {
			$stickedPost = $posts[$position];
			unset( $posts[$position] );
			array_unshift( $posts, $stickedPost );
		}

		return $posts;
	}

}
