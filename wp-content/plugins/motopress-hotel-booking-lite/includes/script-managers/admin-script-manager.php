<?php

namespace MPHB\ScriptManagers;

class AdminScriptManager extends ScriptManager {

	private $roomIds = array();

	public function __construct(){
		add_action( 'admin_enqueue_scripts', array( $this, 'register' ), 9 );
	}

	public function register(){
		parent::register();

		wp_register_script( 'mphb-jquery-serialize-json', MPHB()->getPluginUrl( 'vendors/jquery.serializeJSON/jquery.serializejson.min.js' ), array( 'jquery' ), MPHB()->getVersion() );
		wp_register_script( 'mphb-bgrins-spectrum', MPHB()->getPluginUrl( 'vendors/bgrins-spectrum/build/spectrum-min.js' ), array( 'jquery' ), MPHB()->getVersion(), true );
		$this->addDependency( 'mphb-bgrins-spectrum' );

		wp_register_script( 'mphb-admin', MPHB()->getPluginUrl( 'assets/js/admin/admin.min.js' ), $this->scriptDependencies, MPHB()->getVersion(), true );
	}

	protected function registerStyles(){
		parent::registerStyles();

		wp_register_style( 'mphb-bgrins-spectrum', MPHB()->getPluginUrl( 'vendors/bgrins-spectrum/build/spectrum_theme.css' ), null, MPHB()->getVersion() );
		$this->addStyleDependency( 'mphb-bgrins-spectrum' );

		wp_register_style( 'mphb-admin-css', MPHB()->getPluginUrl( 'assets/css/admin.min.css' ), $this->styleDependencies, MPHB()->getVersion() );
	}

	public function enqueue(){
		if ( !wp_script_is( 'mphb-admin' ) ) {
			add_action( 'admin_print_footer_scripts', array( $this, 'localize' ), 5 );
		}
		wp_enqueue_script( 'mphb-admin' );

		wp_enqueue_style( 'mphb-admin-css' );
	}

	public function addRoomData( $roomId ){
		if ( !in_array( $roomId, $this->roomIds ) ) {
			$this->roomIds[] = $roomId;
		}
	}

	public function localize(){
		wp_localize_script( 'mphb-admin', 'MPHB', $this->getLocalizeData() );
	}

	public function getLocalizeData(){
		$currencySymbol = MPHB()->settings()->currency()->getCurrencySymbol();
		$currencyPosition = MPHB()->settings()->currency()->getCurrencyPosition();
		$data = array(
			'_data' => array(
				'version'			 => MPHB()->getVersion(),
				'prefix'			 => MPHB()->getPrefix(),
				'ajaxUrl'			 => MPHB()->getAjaxUrl(),
				'today'				 => mphb_current_time( 'Y-m-d' ),
				'nonces'			 => MPHB()->getAjax()->getAdminNonces(),
				'translations'		 => array(
					'roomTypeGalleryTitle'	 => __( 'Accommodation Type Gallery', 'motopress-hotel-booking' ),
					'addGalleryToRoomType'	 => __( 'Add Gallery To Accommodation Type', 'motopress-hotel-booking' ),
					'errorHasOccured'		 => __( 'An error has occurred', 'motopress-hotel-booking' ),
					'all'					 => __( 'All', 'motopress-hotel-booking' ),
					'none'					 => __( 'None', 'motopress-hotel-booking' ),
					'edit'					 => __( 'Edit', 'motopress-hotel-booking' ),
					'done'					 => __( 'Done', 'motopress-hotel-booking' ),
					'adults'				 => __( 'Adults: ', 'motopress-hotel-booking' ),
					'children'				 => __( 'Children: ', 'motopress-hotel-booking' )
				),
				'settings'			 => array(
					'firstDay'					 => MPHB()->settings()->dateTime()->getFirstDay(),
					'numberOfMonthCalendar'		 => 2,
					'numberOfMonthDatepicker'	 => 3,
					'dateFormat'				 => MPHB()->settings()->dateTime()->getDateFormatJS(),
					'dateTransferFormat'		 => MPHB()->settings()->dateTime()->getDateTransferFormatJS(),
					'currency'					 => array(
						'price_format'				 => MPHB()->settings()->currency()->getPriceFormat( $currencySymbol, $currencyPosition ),
						'decimals'					 => MPHB()->settings()->currency()->getPriceDecimalsCount(),
						'decimal_separator'			 => MPHB()->settings()->currency()->getPriceDecimalsSeparator(),
						'thousand_separator'		 => MPHB()->settings()->currency()->getPriceThousandSeparator()
					)
				)
			),
		);

		return $data;
	}

}
