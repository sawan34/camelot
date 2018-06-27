<?php

/**
 * Class FMViewPricing_fm
 */
class FMViewPricing_fm extends FMAdminView {
	/**
	* FMViewpricing_fm constructor.
	*/
	public function __construct() {
		wp_enqueue_style('fm-style');
		wp_enqueue_style('fm-pricing');
		wp_enqueue_script('fm-admin');
	}

	public function display( $params = array() ) {
		$page = $params['page'];
		$page_url = $params['page_url'];
		ob_start();
		echo $this->body($params);
		// Pass the content to form.
		$form_attr = array(
			'id' => WDFM()->prefix . '_pricing',
			'name' => WDFM()->prefix . '_pricing',
			'class' => WDFM()->prefix . '_pricing wd-form',
			'action' => add_query_arg( array('page' => $page, 'task' => 'display'), $page_url),
		);
		echo $this->form(ob_get_clean(), $form_attr);
  }

  /**
	* Generate page body.
	*
	* @return string Body html.
	*/
	public function body( $params = array() ) {
	  ?>
	<div class="fm-pricestable-container">
		<div class="fm-pricestable">
      <div class="ptFree">
        <span class="price product_info"><span>$</span>30</span>
        <p><?php _e('Personal', WDFM()->prefix); ?></p>
        <span class="supp">
          <strong><?php _e('6 Months', WDFM()->prefix); ?></strong>
          <span class="desc_span"><?php _e('You’ll have access to new releases during this period and update plugin to include new features without additional charges.', WDFM()->prefix); ?></span><br>
          <?php _e('Access to Updates', WDFM()->prefix); ?>
        </span>
        <span class="supp">
          <strong><?php _e('6 Months', WDFM()->prefix); ?></strong>
          <span class="desc_span"><?php _e('Get quick answers to all product related questions from our support team.', WDFM()->prefix); ?></span><br>
          <?php _e('Premium Support', WDFM()->prefix); ?>
        </span>
        <span class="supp product_info">
          <strong><?php _e('1 Domain', WDFM()->prefix); ?></strong><br>
          <?php _e('Support', WDFM()->prefix); ?>
        </span>
        <ul class="circles">
          <li><div></div></li>
          <li><div></div></li>
          <li><div></div></li>
        </ul>
        <span class="supp product_info"><?php _e('Unlimited Forms/Fields', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('40+ Field Types', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('Multi-Page Forms', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('Paypal Integration', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('File Upload field', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('Fully Customizable Themes', WDFM()->prefix); ?></span>
        <span>
          <a href="https://web-dorado.com/index.php?option=com_wdsubscriptions&view=checkoutpage&tmpl=component&id=69&offerId=117" target="_blank"><?php _e('Buy now', WDFM()->prefix); ?></a>
        </span>
      </div>
      <div class="ptPersonal">
        <span class="price product_info"><span>$</span>45</span>
        <p><?php _e('Business', WDFM()->prefix); ?></p>
        <span class="supp">
          <strong><?php _e('1 Year', WDFM()->prefix); ?></strong>
          <span class="desc_span"><?php _e('You’ll have access to new releases during this period and update plugin to include new features without additional charges.', WDFM()->prefix); ?></span><br>
          <?php _e('Access to Updates', WDFM()->prefix); ?>
        </span>
        <span class="supp">
          <strong><?php _e('1 Year', WDFM()->prefix); ?></strong>
          <span class="desc_span"><?php _e('Get quick answers to all product related questions from our support team.', WDFM()->prefix); ?></span><br>
          <?php _e('Premium Support', WDFM()->prefix); ?>
        </span>
        <span class="supp product_info">
          <strong><?php _e('3 Domains', WDFM()->prefix); ?></strong><br>
          <?php _e('Support', WDFM()->prefix); ?>
        </span>
        <ul class="circles">
          <li><div></div></li>
          <li><div></div></li>
          <li><div></div></li>
        </ul>
        <span class="supp product_info"><?php _e('Unlimited Forms/Fields', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('40+ Field Types', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('Multi-Page Forms', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('Paypal Integration', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('File Upload field', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('Fully Customizable Themes', WDFM()->prefix); ?></span>
        <span>
          <a href="https://web-dorado.com/index.php?option=com_wdsubscriptions&view=checkoutpage&tmpl=component&id=70&offerId=117" target="_blank"><?php _e('Buy now', WDFM()->prefix); ?></a>
        </span>
      </div>
      <div class="ptBusiness">
        <span class="price product_info"><span>$</span>60</span>
        <p><?php _e('Developer', WDFM()->prefix); ?></p>
        <span class="supp">
          <strong><?php _e('1 Year', WDFM()->prefix); ?></strong>
          <span class="desc_span"><?php _e('You’ll have access to new releases during this period and update plugin to include new features without additional charges.', WDFM()->prefix); ?></span><br>
          <?php _e('Access to Updates', WDFM()->prefix); ?>
        </span>
        <span class="supp"><strong><?php _e('1 Year', WDFM()->prefix); ?></strong>
          <span class="desc_span"><?php _e('Get quick answers to all product related questions from our support team.', WDFM()->prefix); ?></span><br>
          <?php _e('Premium Support', WDFM()->prefix); ?>
        </span>
        <span class="supp product_info">
          <strong><?php _e('Unlimited Domains', WDFM()->prefix); ?></strong><br>
          <?php _e('Support', WDFM()->prefix); ?>
        </span>
        <ul class="circles">
          <li><div></div></li>
          <li><div></div></li>
          <li><div></div></li>
        </ul>
        <span class="supp product_info"><?php _e('Unlimited Forms/Fields', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('40+ Field Types', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('Multi-Page Forms', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('Paypal Integration', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('File Upload field', WDFM()->prefix); ?></span>
        <span class="supp product_info"><?php _e('Fully Customizable Themes', WDFM()->prefix); ?></span>
        <span>
          <a href="https://web-dorado.com/index.php?option=com_wdsubscriptions&view=checkoutpage&tmpl=component&id=71&offerId=117" target="_blank"><?php _e('Buy now', WDFM()->prefix); ?></a>
        </span>
      </div>
      <div class="ptDeveloper">
        <span class="special_offer"><?php _e('Special offer', WDFM()->prefix); ?></span>
        <span class="price product_info"><span>$</span>99</span>
        <p class="save_money"><span><?php _e('Save', WDFM()->prefix); ?> $735</span></p>
        <p><?php _e('Form Maker Premium', WDFM()->prefix); ?></p>
        <span class="supp">
          <strong><?php _e('+12 Add-ons', WDFM()->prefix); ?></strong>
          <span class="desc_span"><?php _e('Tune up Form Maker with powerful add-ons: PDF Integration, Mailchimp, Export/Import, Conditional Emails, Registration,etc.', WDFM()->prefix); ?></span>
        </span>
        <span class="supp product_info">
          <strong><?php _e('+ All Our 50 WordPress Premium Plugins', WDFM()->prefix); ?></strong>
        </span>
        <span class="supp product_info"><?php _e('Photo Gallery, Slider, Event Calendar &amp; etc.', WDFM()->prefix); ?></span>
        <ul class="circles">
          <li><div></div></li>
          <li><div></div></li>
          <li><div></div></li>
        </ul>
        <span class="supp">
          <?php _e('6 Months Access to Updates', WDFM()->prefix); ?>
          <span class="desc_span"><?php _e('You’ll have access to new releases during this period and update plugins to include new features without additional charges.', WDFM()->prefix); ?></span>
        </span>
        <span class="supp"><?php _e('6 Months Premium Support', WDFM()->prefix); ?>
          <span class="desc_span"><?php _e('Get quick answers to all product related questions from our support team.', WDFM()->prefix); ?></span>
        </span>
        <span class="supp product_info"><?php _e('Unlimited Domains Support', WDFM()->prefix); ?></span>
        <span>
          <a href="https://web-dorado.com/index.php?option=com_wdsubscriptions&task=buy&id=117&from_id=71&wd_button_clicks=insert_into" target="_blank"><?php _e('Buy now', WDFM()->prefix); ?></a>
        </span>
      </div>
    </div>
		<div class="fm-prices-more">
			<div>
				<?php _e('Learn more about Form Maker plugin.', WDFM()->prefix); ?> <a href="https://web-dorado.com/products/wordpress-form.html" target="_blank"><?php _e('Learn More', WDFM()->prefix); ?></a>
			</div>
		</div>
	</div>
	  <?php
	}
}
