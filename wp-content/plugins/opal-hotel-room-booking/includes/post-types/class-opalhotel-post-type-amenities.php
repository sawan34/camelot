<?php 
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalhotel
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2016 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class OpalHotel_Post_Type_Amenities extends OpalHotel_Abstract_Post_Type {

	/* post type */
	public $post_type = null;

	/* post type args */
	public $post_type_args = null;

	public function __construct() {

		/* post type name*/
		$this->post_type = OPALHOTEL_CPT_ANT;

		/* post type args register */
		$this->post_type_args = array(
            'labels'             => array(
                'name'               => _x( 'Amenities', 'post type general name', 'opal-hotel-room-booking' ),
                'singular_name'      => _x( 'Amenity', 'post type singular name', 'opal-hotel-room-booking' ),
                'menu_name'          => __( 'Amenities', 'opal-hotel-room-booking' ),
                'parent_item_colon'  => __( 'Parent Item:', 'opal-hotel-room-booking' ),
                'all_items'          => __( 'Amenities', 'opal-hotel-room-booking' ),
                'view_item'          => __( 'View Amenity', 'opal-hotel-room-booking' ),
                'add_new_item'       => __( 'Add Amenity', 'opal-hotel-room-booking' ),
                'add_new'            => __( 'Add Amenity', 'opal-hotel-room-booking' ),
                'edit_item'          => __( 'Edit Amenity', 'opal-hotel-room-booking' ),
                'update_item'        => __( 'Update Amenity', 'opal-hotel-room-booking' ),
                'search_items'       => __( 'Search Amenity', 'opal-hotel-room-booking' ),
                'not_found'          => __( 'No amenity found', 'opal-hotel-room-booking' ),
                'not_found_in_trash' => __( 'No amenity found in Trash', 'opal-hotel-room-booking' ),
            ),
            'public'             => false,
            'query_var'          => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'has_archive'        => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'show_in_menu'       => 'edit.php?post_type=opalhotel_hotel',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'exclude_from_search'=> true,
            'supports'           => array( 'title' ),
            'hierarchical'       => false,
            'rewrite'            => array( 'slug' => _x( 'amenities', 'URL slug', 'opal-hotel-room-booking' ), 'with_front' => false, 'feeds' => true )
        );

		parent::__construct();

        /* custom message update amenity */
        add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
	}

    /* custom messages */
    public function updated_messages( $messages ) {
        $post             = get_post();
        $post_type        = get_post_type( $post );
        $post_type_object = get_post_type_object( $post_type );
        if ( ! in_array( $post_type, array( OPALHOTEL_CPT_COUPON ) ) ) {
            return $messages;
        }

        $messages[ OPALHOTEL_CPT_COUPON ] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __( 'Amenity updated.', 'opal-hotel-room-booking' ),
            2  => __( 'Custom field updated.', 'opal-hotel-room-booking' ),
            3  => __( 'Custom field deleted.', 'opal-hotel-room-booking' ),
            4  => __( 'Amenity updated.', 'opal-hotel-room-booking' ),
            /* translators: %s: date and time of the revision */
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Amenity restored to revision from %s', 'opal-hotel-room-booking' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => __( 'Amenity published.', 'opal-hotel-room-booking' ),
            7  => __( 'Amenity saved.', 'opal-hotel-room-booking' ),
            8  => __( 'Amenity submitted.', 'opal-hotel-room-booking' ),
            9  => sprintf(
                __( 'Amenity scheduled for: <strong>%1$s</strong>.', 'opal-hotel-room-booking' ),
                // translators: Publish box date format, see http://php.net/date
                date_i18n( __( 'M j, Y @ G:i', 'opal-hotel-room-booking' ), strtotime( $post->post_date ) )
            ),
            10 => __( 'Amenity draft updated.', 'opal-hotel-room-booking' )
        );

        if ( $post_type_object->publicly_queryable ) {
            $permalink = get_permalink( $post->ID );

            $view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View Amenity', 'opal-hotel-room-booking' ) );
            $messages[ $post_type ][1] .= $view_link;
            $messages[ $post_type ][6] .= $view_link;
            $messages[ $post_type ][9] .= $view_link;

            $preview_permalink = add_query_arg( 'preview', 'true', $permalink );
            $preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview amenity', 'opal-hotel-room-booking' ) );
            $messages[ $post_type ][8]  .= $preview_link;
            $messages[ $post_type ][10] .= $preview_link;
        }
        return $messages;
    }

}

new OpalHotel_Post_Type_Amenities();