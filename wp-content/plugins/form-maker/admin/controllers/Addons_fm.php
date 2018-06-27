<?php
/**
 * Class FMControllerAddons_fm
 */
class FMControllerAddons_fm {
  /**
	* @var $model
	*/
	private $model;
	/**
	* @var $view
	*/
	private $view;
	/**
	* @var string $page
	*/
	private $page;

	public function __construct() {
		$this->page 	= WDW_FM_Library::get('page');
		$this->page_url = add_query_arg( array (
										'page' => $this->page,
										WDFM()->nonce => wp_create_nonce(WDFM()->nonce),
									  ), admin_url('admin.php')
								  );
		require_once WDFM()->plugin_dir . "/admin/views/Addons_fm.php";
		$this->view = new FMViewAddons_fm();
	}

	/**
	* Execute.
	*/
	public function execute() {
		$task = WDW_FM_Library::get('task');
		$id = (int) WDW_FM_Library::get('current_id', 0);
		if (method_exists($this, $task)) {
		  $this->$task($id);
		}
		else {
		  $this->display();
		}
	}

	/**
	* Display.
	*/
	public function display() {
	// Set params for view.
	$params = array();
	$params['page'] 	= $this->page;
	$params['page_url']	= $this->page_url;
	$params['addons']	= array(
			'Form Maker Add-ons' => array(
				'imp_exp'	=> array(
					'pro'	=> false,
					'dir'	=> 'form-maker-export-import/fm_exp_imp.php',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/export-import.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/import_export.svg',
					'name'	=> __('Import/Export', WDFM()->prefix),
					'description'	=> __('Form Maker Export/Import WordPress plugin allows exporting and importing forms with/without submissions.', WDFM()->prefix)
				),
				'mailchimp' => array(
					'pro'	=> false,
					'dir'	=> 'form-maker-mailchimp/fm_mailchimp.php',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/mailchimp.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/mailchimp.svg',
					'name'	=> __('MailChimp', WDFM()->prefix),
					'description'	=> __('This add-on is an integration of the Form Maker with MailChimp which allows to add contacts to your subscription lists just from submitted forms.', WDFM()->prefix)
				),
				'registration' => array(
					'pro'	=> false,
					'dir'	=> 'form-maker-reg/fm_reg.php',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/registration.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/registration.svg',
					'name'	=> __('Registration', WDFM()->prefix),
					'description'	=> __('User Registration add-on integrates with Form maker forms allowing users to create accounts at your website.', WDFM()->prefix)
				),
				'post_generation' => array(
					'pro'	=> false,
					'dir'	=> 'form-maker-post-generation/fm_post_generation.php',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/post-generation.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/post_generation.svg',
					'name'	=> __('Post Generation', WDFM()->prefix),
					'description'	=>	'Post Generation add-on allows creating a post, page or custom post based on the submitted data.',
				),
				'conditional_emails' => array(
					'pro'	=> false,
					'dir'	=> 'form-maker-conditional-emails/fm_conditional_emails.php',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/conditional-emails.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/conditional_emails.svg',
					'name'	=> __('Conditional Emails', WDFM()->prefix),
					'description'	=> __('Conditional Emails add-on allows to send emails to different recipients depending on the submitted data .', WDFM()->prefix)
				),
				'dropbox_integration' => array(
					'pro'	=> true,
					'dir'	=> 'form-maker-dropbox-integration/fm_dropbox_integration.php',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/dropbox.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/dropbox_integration.svg',
					'name'	=> __('Dropbox Integration', WDFM()->prefix),
					'description'	=> __('The Form Maker Dropbox Integration addon is extending the Form Maker capabilities allowing to store the form attachments straight to your Dropbox account.', WDFM()->prefix)
				),
				'gdrive_integration' => array(
					'pro'	=> true,
					'dir'	=> 'form-maker-gdrive-integration/fm_gdrive_integration.php',
					'name'	=> 'Google Drive Integration',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/google-drive.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/gdrive_integration.svg',
					'name'	=> __('Google Drive Integration', WDFM()->prefix),
					'description'	=> __('The Google Drive Integration add-on integrates Form Maker with Google Drive and allows you to send the file uploads to the Google Drive', WDFM()->prefix)
				),
				'pdf_integration' => array(
					'pro'	=> false,
					'dir'	=> 'form-maker-pdf-integration/fm_pdf_integration.php',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/pdf.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/pdf_integration.svg',
					'name'	=> __('PDF Integration', WDFM()->prefix),
					'description' => __('The Form Maker PDF Integration add-on allows sending submitted forms in PDF format.', WDFM()->prefix)
				),
				'pushover' => array(
					'pro'	=> false,
					'dir'	=> 'form-maker-pushover/fm_pushover.php',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/pushover.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/pushover.svg',
					'name'	=> __('Pushover', WDFM()->prefix),
					'description' => __('Form Maker Pushover integration allows to receive real-time notifications when a user submits a new form. This means messages can be pushed to Android and Apple devices, as well as desktop notification board.', WDFM()->prefix)
				),
				'form-maker-save-progress' => array(
					'pro'	=> false,
					'dir'	=> 'form-maker-save-progress/fm_save.php',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/save-progress.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/save_progress.svg',
					'name'	=> __('Save Progress', WDFM()->prefix),
					'description'	=> __('The add-on allows to save filled in forms as draft and continue editing them subsequently.', WDFM()->prefix)
				),
				'stripe' => array(
					'pro'	=> true,
					'dir'	=> 'form-maker-stripe/fm_stripe.php',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/stripe.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/stripe.svg',
					'name'	=> __('Stripe', WDFM()->prefix),
					'description'	=> __('Form Maker Stripe Integration Add-on allows to accept direct payments made by Credit Cards. Users will remain on your website during the entire process.', WDFM()->prefix)
				),
				'calculator' => array(
					'pro'	=> false,
					'dir'	=> 'form-maker-calculator/fm_calculator.php',
					'url'	=> 'https://web-dorado.com/products/wordpress-form/add-ons/calculator.html',
					'icon'	=> WDFM()->plugin_url . '/images/addons/calculator.svg',
					'name'	=> __('Calculator', WDFM()->prefix),
					'description'	=> __('The Form Maker Calculator add-on allows creating forms with dynamically calculated fields.', WDFM()->prefix)
				)
			)
		);
	$this->view->display( $params );
  }
}