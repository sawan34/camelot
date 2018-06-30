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

class OpalHotel_Post_Type_Offers extends OpalHotel_Abstract_Post_Type {

    /* post type */
    public $post_type = null;

    /* post type args */
    public $post_type_args = null;

    public function __construct() {

        /* post type name*/
        $this->post_type = OPALHOTEL_CPT_OFF;

        /* post type args register */
        $this->post_type_args = array(
            'labels'             => array(
                'name'               => esc_html__('Offers', "mazison"),
                'singular_name'      => esc_html__('Offer', "mazison"),
                'add_new'            => esc_html__('Add New Offer', "mazison"),
                'add_new_item'       => esc_html__('Add New Offer', "mazison"),
                'edit_item'          => esc_html__('Edit Offer', "mazison"),
                'new_item'           => esc_html__('New Offer', "mazison"),
                'view_item'          => esc_html__('View Offer', "mazison"),
                'search_items'       => esc_html__('Search Offers', "mazison"),
                'not_found'          => esc_html__('No Offers found', "mazison"),
                'not_found_in_trash' => esc_html__('No Offers found in Trash', "mazison"),
                'menu_name'          => esc_html__('Offers', "mazison"),
            ),
            'hierarchical'        => true,
            'description'         => 'List Offers',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'), //page-attributes, post-formats
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'show_in_nav_menus'   => false,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'has_archive'         => true,
            'query_var'           => true,
            'can_export'          => true,
            'rewrite'             => array( 'slug' => _x( 'offers', 'URL slug', 'opal-hotel-room-booking' ), 'with_front' => false, 'feeds' => true ),
            'capability_type'     => 'post'
        );

        parent::__construct();

    }

}

new OpalHotel_Post_Type_Offers();