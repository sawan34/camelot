<?php

namespace MPHB\Repositories;

use \MPHB\Entities;

class SeasonRepository extends AbstractPostRepository {

	protected $type = 'season';

	/**
	 *
	 * @param array $atts
	 * @return Entities\Season[]
	 */
	public function findAll( $atts = array() ){
		return parent::findAll( $atts );
	}

	/**
	 *
	 * @param int $id
	 * @return Entities\Season|null
	 */
	public function findById( $id, $force = false ){
		return parent::findById( $id, $force );
	}

	public function mapPostToEntity( $post ){

		$id = ( is_a( $post, '\WP_Post' ) ) ? $post->ID : $post;

		$startDate	 = get_post_meta( $id, 'mphb_start_date', true );
		$endDate	 = get_post_meta( $id, 'mphb_end_date', true );
		$days		 = get_post_meta( $id, 'mphb_days', true );

		$seasonArgs = array(
			'id'			 => $id,
			'title'			 => get_the_title( $id ),
			'description'	 => get_post_field( 'post_content', $id ),
			'start_date'	 => !empty( $startDate ) ? \DateTime::createFromFormat( 'Y-m-d', $startDate ) : null,
			'end_date'		 => !empty( $endDate ) ? \DateTime::createFromFormat( 'Y-m-d', $endDate ) : null,
			'days'			 => !empty( $days ) ? $days : array()
		);

		return new Entities\Season( $seasonArgs );
	}

	/**
	 *
	 * @param Entities\Season $entity
	 * @return \MPHB\Entities\WPPostData
	 */
	public function mapEntityToPostData( $entity ){
		$postAtts = array(
			'ID'			 => $entity->getId(),
			'post_metas'	 => array(),
			'post_status'	 => $entity->isActive() ? 'publish' : 'draft',
			'post_title'	 => $entity->getTitle(),
			'post_content'	 => $entity->getDescription(),
			'post_type'		 => MPHB()->postTypes()->season()->getPostType(),
		);

		$postAtts['post_metas'] = array(
			'mphb_start_date'	 => !is_null( $entity->getStartDate() ) ? $entity->getStartDate()->format( 'Y-m-d' ) : null,
			'mphb_end_date'		 => !is_null( $entity->getEndDate() ) ? $entity->getEndDate()->format( 'Y-m-d' ) : null,
			'mphb_days'			 => $entity->getDays()
		);

		return new Entities\WPPostData( $postAtts );
	}

	/**
	 * @global \WPDB $wpdb
	 *
	 * @param array $atts
	 * @param \DateTime $atts['from_date'] Optional. Default is today.
	 *
	 * @return int[] Array of season IDs.
	 */
	public function findSeasonIds( $atts = array() ){
		global $wpdb;

		$atts = array_merge( array(
			'from_date' => new \DateTime( current_time( 'mysql' ) )
		), $atts );

		$select	 = "SELECT seasons.ID AS id FROM $wpdb->posts AS seasons";
		$join	 = " INNER JOIN $wpdb->postmeta AS seasonStart"
				 . " ON ( seasons.id = seasonStart.post_id )"
				 . " INNER JOIN $wpdb->postmeta AS seasonEnd"
				 . " ON ( seasons.id = seasonEnd.post_id )";
		$where	 = " WHERE seasons.post_type = '" . MPHB()->postTypes()->season()->getPostType() . "'"
				 . " AND seasons.post_status = 'publish'"
				 . " AND seasonStart.meta_key = 'mphb_start_date'"
				 . " AND seasonEnd.meta_key = 'mphb_end_date'"
				 . " AND CAST(seasonStart.meta_value AS DATE) <= '%s'"
				 . " AND CAST(seasonEnd.meta_value AS DATE) >= '%s'";

		$fromDate = $atts['from_date']->format( 'Y-m-d' );
		$where = $wpdb->prepare( $where, $fromDate, $fromDate );

		$query = $select . $join . $where;
		$seasonIds = $wpdb->get_col( $query );

		// Check "Applied for days" parameter
		$dayNumber = (int)$atts['from_date']->format('w');
		$filteredIds = array();

		foreach ( $seasonIds as $seasonId ) {
			$seasonId = (int)$seasonId;
			$season = MPHB()->getSeasonRepository()->findById( $seasonId );

			if ( $season && in_array( $dayNumber, $season->getDays() ) ) {
				$filteredIds[] = $seasonId;
			}
		}

		return $filteredIds;
	}

	/**
	 * @param array $atts
	 * @param \DateTime $atts['from_date'] Optional. Default is today.
	 *
	 * @return int First found season ID.
	 */
	public function findSeasonId( $atts = array() ){
		$seasonIds = $this->findSeasonIds( $atts );

		if ( empty( $seasonIds ) ) {
			return 0;
		} else if ( count( $seasonIds ) == 1 ) {
			return reset( $seasonIds );
		}

		$priorities = get_option( 'mphb_booking_rules_season_priorities', array() );
		$foundId = 0;
		$currentPriority = PHP_INT_MAX;

		foreach ( $seasonIds as $id ) {
			// The smaller the value, the higher the priority
			if ( isset( $priorities[$id] ) && $priorities[$id] < $currentPriority ) {
				$foundId = $id;
				$currentPriority = $priorities[$id];
			}
		}

		return $foundId;
	}

}
