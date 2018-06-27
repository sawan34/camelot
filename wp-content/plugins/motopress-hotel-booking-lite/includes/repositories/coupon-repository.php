<?php

namespace MPHB\Repositories;

use \MPHB\Entities;

class CouponRepository extends AbstractPostRepository {

	/**
	 *
	 * @param Entities\Coupon $entity
	 * @return \MPHB\Entities\WPPostData
	 */
	public function mapEntityToPostData( $entity ){

		$postAtts = array(
			'ID'			 => $entity->getId(),
			'post_metas'	 => array(),
			'post_title'	 => $entity->getCode(),
			'post_status'	 => $entity->getStatus(),
			'post_type'		 => MPHB()->postTypes()->coupon()->getPostType(),
		);

		$postAtts['post_metas'] = array(
			'_mphb_description'				 => $entity->getDescription(),
			'_mphb_amount'					 => $entity->getAmount(),
			'_mphb_expiration_date'			 => $entity->getExpirationDate() ? $entity->getExpirationDate()->format( 'Y-m-d' ) : '',
			'_mphb_include_room_types'		 => $entity->getRoomTypes(),
			'_mphb_check_in_date_after'		 => $entity->getCheckInDateAfter() ? $entity->getCheckInDateAfter()->format( 'Y-m-d' ) : '',
			'_mphb_check_out_date_before'	 => $entity->getCheckOutDateBefore() ? $entity->getCheckOutDateBefore()->format( 'Y-m-d' ) : '',
			'_mphb_min_nights'				 => $entity->getMinNights(),
			'_mphb_max_nights'				 => $entity->getMaxNights(),
			'_mphb_usage_limit'				 => $entity->getUsageLimit(),
			'_mphb_usage_count'				 => $entity->getUsageCount()
		);

		return new Entities\WPPostData( $postAtts );
	}

	/**
	 * @param \WP_Post|int $post
	 * @return \MPHB\Entities\Coupon
	 */
	public function mapPostToEntity( $post ){

		if ( is_a( $post, '\WP_Post' ) ) {
			$id = $post->ID;
		} else {
			$id		 = absint( $post );
			$post	 = get_post( $id );
		}

		$description = get_post_meta( $id, '_mphb_description', true );

		$amount = max( (float) get_post_meta( $id, '_mphb_amount', true ), 0.0 );

		$roomTypes = get_post_meta( $id, '_mphb_include_room_types', true );
		if ( $roomTypes == '' ) {
			$roomTypes = array();
		}

		$minNights	 = (int) get_post_meta( $id, '_mphb_min_nights', true );
		$maxNights	 = (int) get_post_meta( $id, '_mphb_max_nights', true );
		$usageLimit	 = (int) get_post_meta( $id, '_mphb_usage_limit', true );
		$usageCount	 = (int) get_post_meta( $id, '_mphb_usage_count', true );

		$atts = array(
			'id'			 => $id,
			'status'		 => $post->post_status,
			'code'			 => $post->post_title,
			'description'	 => $description,
			'amount'		 => $amount,
			'room_types'	 => $roomTypes,
			'min_nights'	 => $minNights,
			'max_nights'	 => $maxNights,
			'usage_limit'	 => $usageLimit,
			'usage_count'	 => $usageCount
		);

		$expirationDate = \DateTime::createFromFormat( 'Y-m-d', get_post_meta( $id, '_mphb_expiration_date', true ) );
		if ( $expirationDate ) {
			$atts['expiration_date'] = $expirationDate;
		}

		$checkInDateAfter = \DateTime::createFromFormat( 'Y-m-d', get_post_meta( $id, '_mphb_check_in_date_after', true ) );
		if ( $checkInDateAfter ) {
			$atts['check_in_date_after'] = $checkInDateAfter;
		}

		$checkOutDateBefore = \DateTime::createFromFormat( 'Y-m-d', get_post_meta( $id, '_mphb_check_out_date_before', true ) );
		if ( $checkOutDateBefore ) {
			$atts['check_out_date_before'] = $checkOutDateBefore;
		}

		return new Entities\Coupon( $atts );
	}

	/**
	 *
	 * @param string $code
	 * @return \MPHB\Entities\Coupon|null
	 */
	public function findByCode( $code ){

		$atts = array(
			'title'			 => $code,
			'posts_per_page' => 1,
			'status'		 => 'publish'
		);

		$coupons = $this->findAll( $atts );

		return !empty( $coupons ) ? reset( $coupons ) : null;
	}

	/**
	 *
	 * @param int $id
	 * @param bool $force
	 * @return Entities\Coupon
	 */
	public function findById( $id, $force = false ){
		return parent::findById( $id, $force );
	}

}
