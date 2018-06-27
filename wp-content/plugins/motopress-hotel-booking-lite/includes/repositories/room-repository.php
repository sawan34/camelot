<?php

namespace MPHB\Repositories;

use \MPHB\Entities;

class RoomRepository extends AbstractPostRepository {

	protected $type = 'room';

	/**
	 *
	 * @param int $id
	 * @param bool $force
	 * @return Entities\Room
	 */
	public function findById( $id, $force = false ){
		return parent::findById( $id, $force );
	}

	function mapPostToEntity( $post ){
		$id = ( is_a( $post, '\WP_Post' ) ) ? $post->ID : $post;
		return new Entities\Room( $id );
	}

	/**
	 *
	 * @param Entities\Room $entity
	 * @return \MPHB\Entities\WPPostData
	 */
	public function mapEntityToPostData( $entity ){
		$postAtts = array(
			'ID'			 => $entity->getId(),
			'post_metas'	 => array(),
			'post_status'	 => $entity->isActive() ? 'publish' : 'draft',
			'post_title'	 => $entity->getTitle(),
			'post_content'	 => $entity->getDescription(),
			'post_type'		 => MPHB()->postTypes()->room()->getPostType(),
		);

		$postAtts['post_metas'] = array(
			'mphb_room_type_id'	 => $entity->getRoomTypeId(),
			'mphb_season_prices' => array_reverse( $entity->getSeasonPrices() )
		);

		return new Entities\WPPostData( $postAtts );
	}

	/**
	 *
	 * @param Entities\RoomType $roomType
	 * @param int $count Optional. Number of rooms to generate. Default 1.
	 * @param string $customPrefix Optional. Default ''
	 * @return bool
	 */
	public function generateRooms( $roomType, $count = 1, $customPrefix = '' ){
		$titlePrefix = '';

		if ( !$roomType ) {
			return false;
		}

		if ( $count < 1 ) {
			return false;
		}

		if ( empty( $customPrefix ) ) {
			$titlePrefix = $roomType->getTitle() . ' ';
		} else {
			$titlePrefix = $customPrefix . ' ';
		}

		for ( $i = 1; $i <= $count; $i++ ) {
			$postMetaAtts	 = array(
				'mphb_room_type_id' => $roomType->getId(),
			);
			$postDataAtts	 = array(
				'post_metas'	 => $postMetaAtts,
				'post_title'	 => $titlePrefix . $i,
				'post_type'		 => MPHB()->postTypes()->room()->getPostType(),
				'post_status'	 => 'publish'
			);

			$postData = new Entities\WPPostData( $postDataAtts );

			$created = $this->persistence->create( $postData );
		}

		return true;
	}

	public function findAllByRoomType( $roomTypeId, $atts = array() ){
		$atts['room_type_id'] = $roomTypeId;
		return $this->findAll( $atts );
	}

	/**
	 *
	 * @param int $roomTypeId
	 * @param bool $fromToday Optional. True by default.
	 *
	 * @return array Keys are dates in format Y-m-d, values are room counts
	 */
	public function getLockedRoomsCountByDayList( $roomTypeId, $fromToday = true ){

		$roomsIds = MPHB()->getRoomPersistence()->getPosts(
			array(
				'fields'		 => 'ids',
				'room_type_id'	 => $roomTypeId,
				'post_status'	 => 'publish'
			)
		);

		$bookingAtts = array(
			'room_locked'	 => true,
			'rooms'			 => $roomsIds,
			'fields'		 => 'all'
		);

		if ( $fromToday ) {
			$bookingAtts['meta_query'] = array(
				array(
					'key'		 => 'mphb_check_out_date',
					'value'		 => mphb_current_time( 'Y-m-d' ),
					'compare'	 => '>=',
					'type'		 => 'DATE'
				)
			);
		}

		$bookings = MPHB()->getBookingRepository()->findAll( $bookingAtts );

		$dates = array();
		foreach ( $bookings as $key => $booking ) {

			$rooms = array_map( function( Entities\ReservedRoom $reservedRoom ) use ( $roomTypeId ) {
				return $reservedRoom->getRoomTypeId() == $roomTypeId ? $reservedRoom->getRoomId() : null;
			}, $booking->getReservedRooms() );
			$rooms = array_filter( $rooms );

			foreach ( $booking->getDates( $fromToday ) as $dateYmd => $date ) {
				if ( !isset( $dates[$dateYmd] ) ) {
					$dates[$dateYmd] = array();
				}
				$dates[$dateYmd] = array_merge( $dates[$dateYmd], $rooms );
			}
		}

		$dates	 = array_filter( $dates );
		$dates	 = array_map( 'array_unique', $dates );
		$dates	 = array_map( 'count', $dates );
		ksort( $dates );

		return $dates;
	}

}
