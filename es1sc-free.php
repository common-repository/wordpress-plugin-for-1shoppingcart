<?php
/**
* Plugin Name: ES 1ShoppingCart
* Plugin URI: http://www.equalserving.com/products-page/wordpress-plugin/free-wordpress-plugin-for-1shoppingcart/
* Description: Using shortcodes, you can easily display product details from your 1ShoppingCart.com product catalog on pages or posts within your WordPress site. All that needs to be entered on the page or post is the title and the shortcut code [es1sc_prodlist]. The shortcode [es1sc_prodlist] without any additional arguments will display your entire active product catalog. You can limit the list to specific products by adding the argument prd_ids to the shortcode such as - [es1sc_prodlist prd_ids="8644152,8644145,8580674,8569588,8569508,8361626"].
* Version: 0.9.2
* Author: EqualServing.com
* Author URI: http://www.equalserving.com/
* Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=H8KWRPTET2SK2&lc=US&item_name=Free%20Wordpress%20Plugin%20for%201ShoppingCart&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
* License: GPLv2
*
* Free ES 1ShoppingCart is a plugin developed to simplify the process of displaying 1ShoppingCart product catalogs and
* product details on your Wordpress pages and posts.
*
*/

define( 'ES1SCVERSION', '0.9.2' );

require(plugin_dir_path( __FILE__ ) .'include/OneShopAPI.php');
$merchantId = get_option('es1sc_merchant_id');
$merchantKey = get_option('es1sc_merchant_key');
$apiUri = get_option('es1sc_api_uri');

$shop = new esOneShopAPI($merchantId, $merchantKey, $apiUri);

// Set minimum execution time to 5 minutes - won't affect safe mode
$safe_mode = array('On', 'ON', 'on', 1);
if ( !in_array(ini_get('safe_mode'), $safe_mode) && ini_get('max_execution_time') < 300 ) {
	@ini_set('max_execution_time', 300);
}

if ( isset($_POST['option_page']) && $_POST['option_page'] == "es1sc-settings") {
	es1sc_admin();
}

/**
 * Settings link in the plugins page menu
 * @param array $links
 * @param string $file
 * @return array
 */
function es1sc_set_plugin_meta($links, $file) {
	$plugin = plugin_basename(__FILE__);
	// create link
	if ($file == $plugin) {
		return array_merge(
			$links,
			array( sprintf( '<a href="admin.php?page='.$file.'">%s</a>', __('Settings') ),
			'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=H8KWRPTET2SK2&lc=US&item_name=Free%20Wordpress%20Plugin%20for%201ShoppingCart&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank">Donate</a>')
		);
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'es1sc_set_plugin_meta', 10, 2 );

function es1sc_admin_menu() {
	add_options_page('1ShoppingCart Options', '1SC Settings', 'administrator', __FILE__, 'es1sc_plugin_options');
}
add_action('admin_menu', 'es1sc_admin_menu');

function es1sc_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	if ( isset($_REQUEST['saved']) && $_REQUEST['saved'] ) {
		echo '<div id="message" class="updated fade"><p><strong>ES1SC settings saved.</strong></p></div>';
	}
	if ( isset($_REQUEST['reset']) && $_REQUEST['reset'] ) {
		echo '<div id="message" class="updated fade"><p><strong>ES1SC settings reset.</strong></p></div>';
	}

	echo '<div class="wrap">';
	echo '<h2>Your 1ShoppingCart.com API Information</h2>';

	echo '<div class="postbox-container" style="width:65%;padding:0 20px 0 0;">';
	echo '   <div class="metabox-holder">';
	echo '      <div class="meta-box-sortables">';

	echo '<form name="updatesettings" id="updatesettings" method="post" action="'. $_SERVER['REQUEST_URI']. '">';
    settings_fields( 'es1sc-settings' );
	echo '	<table class="form-table">';

	$pluginoptions = array (
		array("name" => __('Merchant ID','thematic'),
			"desc" => __('Your 1ShoppingCart.com Merchant ID','thematic'),
			"id" => "es1sc_merchant_id",
			"std" => "999999",
			"type" => "text"
		),
		array("name" => __('Merchant Key','thematic'),
			"desc" => __('Your 1ShoppingCart.com Merchant Key. <a href="http://api.1shoppingcart.com/index.php?title=Getting_Started" target="_blank">Click here if you do not know where to find your <strong>Merchant Key</strong>.</a>','thematic'),
			"id" => "es1sc_merchant_key",
			"std" => "999999",
			"type" => "text"
		),
		array("name" => __('MID','thematic'),
			"desc" => __('Your 1ShoppingCart.com mid value. <a href="https://equalserving.uservoice.com/knowledgebase/articles/165520-where-is-my-mid" target="_blank">Click here if you do not know where to find your <strong>MID</strong>.</a>' ,'thematic'),
			"id" => "es1sc_mid",
			"std" => "999999",
			"type" => "text"
		),
		array("name" => __('API URI','thematic'),
			"desc" => __('Your 1ShoppingCart.com API URI','thematic'),
			"id" => "es1sc_api_uri",
			"std" => "999999",
			"type" => "text"
		),
		array("name" => __('No Image URL','thematic'),
			"desc" => __('The location of the no product image available. This plugin comes with a black and white no image available image. It is located at '.plugin_dir_url(__FILE__).'images/image-not-available.png.','thematic'),
			"id" => "es1sc_no_image",
			"std" => "999999",
			"type" => "text"
		),
		array("name" => __('Add to Cart Image URL','thematic'),
			"desc" => __('The location of the add to cart image. This plugin comes with black, white, red, blue, orange, green and purple add to cart images. They are located at '.plugin_dir_url(__FILE__).'images/add-to-cart-COLOR.png. Just replace COLOR with the actual color you would like to use in lowercase, such as: '.plugin_dir_url(__FILE__).'images/add-to-cart-red.png','thematic'),
			"id" => "es1sc_cart_image",
			"std" => "999999",
			"type" => "text"
		),
		array("name" => __('Buy Now URL','thematic'),
			"desc" => __('Enter your 1ShoppingCart.com URL above.<br />The URL usually looks something like ->><br /> http://www.1shoppingcart.com/SecureCart/SecureCart.aspx?mid='. (trim(get_option('es1sc_mid')) ==  '' ? '####-#####-#####-#####' : get_option('es1sc_mid'))  .'.<br /><strong>If you are unsure, leave this field blank but be sure to enter your MID in the above field and this value will be automatically completed</strong>.','thematic'),
			"id" => "es1sc_buynow_url",
			"std" => "999999",
			"type" => "text",
		),
		array("name" => __('Product List Item Format','thematic'),
			"desc" => __('Enter the formatting you would like to use for your product listing.','thematic'),
			"id" => "es1sc_product_list_item_format",
			"std" => "999999",
			"type" => "textarea",
			"options" => array("cols" => 60, "rows" => 4),
		),
	);

	foreach ($pluginoptions as $value) {
		// Output the appropriate form element
		switch ( $value['type'] ) {
			case 'text':
			?>
			<tr valign="top">
				<th scope="row"><?php echo $value['name']; ?>:</th>
				<td>
					<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="text"
						value="<?php echo stripslashes(get_option( $value['id'],$value['std'] )); ?>"/>
					<br /><?php echo $value['desc']; ?>
				</td>
			</tr>
			<?php
			break;
			case 'select':
			?>
			<tr valign="top">
				<th scope="row"><?php echo $value['name']; ?></th>
				<td>
					<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
						<option value="">--</option>
						<?php foreach ($value['options'] as $key=>$option) {
							if ($key == get_option($value['id'], $value['std']) ) {
								$selected = 'selected="selected"';
							} else {
								$selected = "";
							}
							?>
							<option value="<?php echo $key ?>" <?php echo $selected ?>> <?php echo $option; ?></option>
						<?php } ?>
					</select>
					<br /><?php echo $value['desc']; ?>
				</td>
			</tr>
			<?php
			break;
			case 'textarea':
				$ta_options = $value['options'];
				?>
				<tr valign="top">
					<th scope="row"><?php echo $value['name']; ?>:</th>
					<td>
						<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"
							cols="<?php echo $ta_options['cols']; ?>"
							rows="<?php echo $ta_options['rows']; ?>"><?php
							echo stripslashes(get_option($value['id'], $value['std'])); ?>
						</textarea>
						<br /><?php echo $value['desc']; ?>
					</td>
				</tr>
			<?php
			break;
			case "radio":
			?>
				<tr valign="top">
					<th scope="row"><?php echo $value['name']; ?>:</th>
					<td>
						<?php foreach ($value['options'] as $key=>$option) {
							if ($key == get_option($value['id'], $value['std']) ) {
								$checked = 'checked="checked"';
							} else {
								$checked = "";
							}
							?>
							<input type="radio"
								name="<?php echo $value['id']; ?>"
								value="<?php echo $key; ?>"
								<?php echo $checked; ?>
								/><?php echo $option; ?>
								<br />
						<?php } ?>
						<br /><?php echo $value['desc']; ?>
					</td>
				</tr>
			<?php
			break;
			case "checkbox":
			?>
				<tr valign="top">
					<th scope="row"><?php echo $value['name']; ?></th>
					<td>
						<?php
						if(get_option($value['id'])){
							$checked = 'checked="checked"';
						} else {
							$checked = "";
						}
						?>
						<input type="checkbox"
							name="<?php echo $value['id']; ?>"
							id="<?php echo $value['id']; ?>"
							value="true"
							<?php echo $checked; ?>
							/>
							<br /><?php echo $value['desc']; ?>
					</td>
				</tr>
			<?php
			break;
			default:
			break;
		}
	}
	echo '	</table>';
	echo '	<p class="submit">';
	echo '	<input type="hidden" name="es1sc_admin" value="update_settings" />';
	echo '	<input type="submit" class="button-primary" value="Save Changes" />';
	echo '	</p>';
	echo '	</form>';
	echo '</div>';
	echo '</div>';
	echo '</div>';

	echo '	<div class="postbox-container" style="width:32%;">';
	echo '		<div class="metabox-holder">';
	echo '			<div class="meta-box-sortables">';
						plugin_help();
						plugin_like();
						plugin_didyouknow();
	echo '				</div>';
	echo '				<br/><br/><br/>';
	echo '			</div>';
	echo '		</div>';

	echo '<div style="clear:both;"> </div>';
	echo '<hr />';
	echo '<h2>ES1SC Shortcodes</h2>'."\n";

	echo '<h3><a href="http://equalserving.uservoice.com/knowledgebase/articles/217339-product-list-shortcode-free" target="_blank">&raquo; For a complete list of shortcode options, please see our FAQs &laquo;</a>.</h3>'."\n";

	echo '<h3><strong>Product List Options</strong></h3>'."\n";
	echo '<dl>';
	echo '<dt><strong>Complete Product List</strong></dt>'."\n";
	echo '<dd>To display a complete list of active products on a page or post - include the shortcode without any attributes.'."\n";
	echo '<br /><code>[es1sc_prodlist]</code></dd>'."\n";
	echo '<dt><strong>Specific Product List</strong></dt>'."\n";
	echo '<dd>To display a specific list of products on a page or post - include the shortcode with the prd_ids attribute.'."\n";
	echo '<br /><code>[es1sc_prodlist prd_ids="2723132, 9223971,9209291,9084234"]</code></dd>'."\n";
	echo '</dl>';




	echo '</div>';

}

function register_es1scsettings() {
	//register our settings
	register_setting('es1sc-settings', 'es1sc_merchant_id');
	register_setting('es1sc-settings', 'es1sc_merchant_key');
	register_setting('es1sc-settings', 'es1sc_mid');
	register_setting('es1sc-settings', 'es1sc_api_uri');
	add_option('es1sc_merchant_id', '');
	add_option('es1sc_merchant_key', '');
	add_option('es1sc_mid', '');
	add_option('es1sc_api_uri', 'https://www.mcssl.com');
	add_option('es1sc_no_image', plugin_dir_url(__FILE__) .'images/image-not-available.png');
	add_option('es1sc_cart_image', plugin_dir_url(__FILE__) .'images/add-to-cart-black.png');
	add_option('es1sc_buynow_url',get_option('siteurl').'/cmd.php');
	add_option('es1sc_product_list_item_format','<div class="product"><div style="display:block;float:left;width:250px;">#ProductImage</div><div style="float:left;display:block;width:300px;"><span class="product_name">#ProductName</span> <br /><span class="product_details"><span class="description">#ShortDescription</span><span class="sku">#ProductSku</span><br /><span class="price">#ProductPrice</span><br /><span class="buy-now">#BuyNow</span></span><div style="clear:both;"> </div></div><div style="clear:both;"> </div></div>');
}
add_action('admin_init', 'register_es1scsettings');

function es1sc_admin()
{

	//print_r($_POST);
	switch ($_POST['action']) {

	case ("update"):

		update_option('es1sc_merchant_id', $_POST['es1sc_merchant_id']);
		update_option('es1sc_merchant_key', $_POST['es1sc_merchant_key']);
		update_option('es1sc_mid', $_POST['es1sc_mid']);
		update_option('es1sc_api_uri', $_POST['es1sc_api_uri']);
		update_option('es1sc_no_image',$_POST['es1sc_no_image']);
		update_option('es1sc_cart_image',$_POST['es1sc_cart_image']);
		if (empty($_POST['es1sc_buynow_url']) && !empty($_POST['es1sc_mid'])) {
			update_option('es1sc_buynow_url', 'http://www.1shoppingcart.com/SecureCart/SecureCart.aspx?mid='. trim($_POST['es1sc_mid']));
		} else {
			update_option('es1sc_buynow_url', $_POST['es1sc_buynow_url']);
		}
		update_option('es1sc_product_list_item_format',$_POST['es1sc_product_list_item_format']);

		break;

	case ("update_fields"):
		break;
	}
}

function es1sc_product_list($atts) {
	global $shop;

	// To send HTML mail, the Content-type header must be set
	$headers = array('Content-Type: text/html; charset=UTF-8');

	$display_format = stripslashes(get_option('es1sc_product_list_item_format'));

	$retVal = "";

	extract(shortcode_atts(array("prd_ids" => 0), $atts));
	if (isset($prd_ids) && $prd_ids != "") {
		$prd_ids_temp = explode(",",$prd_ids);
		$prd_ids = (object) $prd_ids_temp;
	} else {
		$limitoffset = 0;
		$limitcount = 30;
		$prd_ids = array();
		while (!is_null($limitoffset)) {
			$shop->_apiParameters = array("LimitCount" => $limitcount, "LimitOffset" => $limitoffset);
			$products_xml = $shop->GetProductsList();
			$products = @simplexml_load_string($products_xml) or die ("no file loaded");
			//print_r($products);
			if ($products["success"] == "true") {
				foreach ($products->Products->Product as $prd_id) {
					$prd_ids[] = $prd_id;
				}
				$limitoffset = $products->NextRecordSet->LimitOffset;
			} else {
				// 2040 - No data found. We are not so concerned about this error because there were no changes made since the last synch.
				if ($products->Error["code"] != "2040") {
					$body = "<p>The 1ShoppingCart Plugin for Wordpress generated an error.</p><p><b>Error Code:</b> ".$products->Error["code"].". <b>Error Message:</b> ".$products->Error.".</p>"
					        ."<p>If you are unable to take corrective action based upon the information contained in this error report, please contact 1ShoppingCart.com Support for assistance.</p>"
							.'<p>For a complete list of error codes and descriptions go to => <a href="http://www.equalserving.com/go/es1scpluginerrorcodes" target="_blank" class="button">http://www.equalserving.com/go/es1scpluginerrorcodes</a>.</p>';
					wp_mail(get_bloginfo('admin_email'), get_bloginfo('name').' 1Shoppingcart.com Plugin for Wordpress Configuration Error', $body, $headers);
					echo $body;
				}
				$limitoffset = NULL;
			}
		}
	}
	foreach ($prd_ids as $prd_id) {
		$product_details_xml = $shop->GetProductById($prd_id);
		$product_details = @simplexml_load_string($product_details_xml) or die ("no file loaded");
		if ($product_details["success"] == "true") {

			if ($product_details->ProductInfo->IsActive == "true") {
				if ($product_details->ProductInfo->ImageUrl == "") {
					$ImageUrlSrc = get_option('es1sc_no_image');
				} else {
					$ImageUrlSrc = "https://www.mcssl.com".$product_details->ProductInfo->ImageUrl;
				}
				$ImageUrl = '<img class="product_image" src="'. $ImageUrlSrc.'" alt="'.$product_details->ProductInfo->ProductName.'" />';

				$ProductPrice = "";
				if ($product_details->ProductInfo->UseSalePrice == "true") {
					$ProductPrice .= '<span class="regular">Retail Price: <strike>$'.$product_details->ProductInfo->ProductPrice.'</strike></span> <span class="save-percent">Save: ';
					$ProductPrice .= number_format(((float)$product_details->ProductInfo->ProductPrice - (float)$product_details->ProductInfo->SalePrice) / (float)$product_details->ProductInfo->ProductPrice * 100, 0, '.', ',').'%';
					$ProductPrice .= '</span>  <span class="save-dollar">Save $';
					$ProductPrice .= number_format((float)$product_details->ProductInfo->ProductPrice - (float)$product_details->ProductInfo->SalePrice, 2, '.', ',');
					$ProductPrice .= '</span> <span class="sale">Sale Price $'.number_format((float)$product_details->ProductInfo->SalePrice, 2, '.', ',').'</span>';
				} else {
					$ProductPrice .= '<span class="regular">Regular Price: $'.$product_details->ProductInfo->ProductPrice.'</span>';
				}
				$es1sc_buynow_url = trim(get_option('es1sc_buynow_url'));
				if (substr($es1sc_buynow_url,-4) == ".php") {
					$es1sc_buynow_url = $es1sc_buynow_url."?";
				} else {
					$es1sc_buynow_url = $es1sc_buynow_url."&";
				}
				$BuyNow = '<a href="'.$es1sc_buynow_url.'pid='.$product_details->ProductInfo->VisibleId.'"><img src="'.get_option('es1sc_cart_image').'" alt="Add to Cart" /></a>';
				$TitleHyphens = preg_replace("/[^a-zA-Z 0-9]+/", "", strtolower($product_details->ProductInfo->ProductName));
				$TitleHyphens = str_replace(" ", "-", $TitleHyphens);
				$aVariables = array('#ProductId','#ProductName', '#ProductImage', '#ShortDescription', '#LongDescription', '#ProductSku', '#ProductPrice','#BuyNow','#ProductHyphenName');
				$aReplacements = array($prd_id,$product_details->ProductInfo->ProductName, $ImageUrl, wpautop($product_details->ProductInfo->ShortDescription), wpautop($product_details->ProductInfo->LongDescription), $product_details->ProductInfo->ProductSku, $ProductPrice, $BuyNow, $TitleHyphens);

				$retVal .= str_replace($aVariables, $aReplacements, $display_format);
			}
		} else {
			// 2040 - No data found. We are not so concerned about this error because there were no changes made since the last synch.
			if ($product_details->Error["code"] != "2040") {
				$body = "<p>The 1ShoppingCart Plugin for Wordpress generated an error.</p><p><b>Error Code:</b> ".$product_details->Error["code"].". <b>Error Message:</b> ".$product_details->Error.".</p>"
				        ."<p>If you are unable to take corrective action based upon the information contained in this error report, please contact 1ShoppingCart.com Support for assistance.</p>"
						.'<p>For a complete list of error codes and descriptions go to => <a href="http://www.equalserving.com/go/es1scpluginerrorcodes" target="_blank" class="button">http://www.equalserving.com/go/es1scpluginerrorcodes</a>.</p>';

				wp_mail(get_bloginfo('admin_email'), get_bloginfo('name').' 1Shoppingcart.com Plugin for Wordpress Configuration Error', $body, $headers);
				echo $body;
				break;
			}
		}

	}

	return $retVal;
}

function _es1sc_product_list($atts) {
	global $shop;

	$display_format = stripslashes(get_option('es1sc_product_list_item_format'));

	$retVal = "";

	extract(shortcode_atts(array("prd_ids" => 0), $atts));
	if (isset($prd_ids) && $prd_ids != "") {
		$prd_ids_temp = explode(",",$prd_ids);
		$prd_ids = (object) $prd_ids_temp;
	} else {
		$limitoffset = 0;
		$limitcount = 30;
		$prd_ids = array();
		while (!is_null($limitoffset)) {
			$shop->_apiParameters = array("LimitCount" => $limitcount, "LimitOffset" => $limitoffset);
			$products_xml = $shop->GetProductsList();
			$products = @simplexml_load_string($products_xml) or die ("no file loaded");
			//print_r($products);
			foreach ($products->Products->Product as $prd_id) {
				$prd_ids[] = $prd_id;
			}
			$limitoffset = $products->NextRecordSet->LimitOffset;
		}
	}
	foreach ($prd_ids as $prd_id) {
		$product_details_xml = $shop->GetProductById($prd_id);
		$product_details = @simplexml_load_string($product_details_xml) or die ("no file loaded");

		if ($product_details->ProductInfo->IsActive == "true") {
			if ($product_details->ProductInfo->ImageUrl == "") {
				$ImageUrlSrc = get_option('es1sc_no_image');
			} else {
				$ImageUrlSrc = "https://www.mcssl.com".$product_details->ProductInfo->ImageUrl;
			}
			$ImageUrl = '<img class="product_image" src="'. $ImageUrlSrc.'" alt="'.$product_details->ProductInfo->ProductName.'" />';

			$ProductPrice = "";
			if ($product_details->ProductInfo->UseSalePrice == "true") {
				$ProductPrice .= '<strike>$'.$product_details->ProductInfo->ProductPrice.'</strike> Only $'.$product_details->ProductInfo->SalePrice;
			} else {
				$ProductPrice .= "$".$product_details->ProductInfo->ProductPrice;
			}
			$es1sc_buynow_url = trim(get_option('es1sc_buynow_url'));
			if (substr($es1sc_buynow_url,-4) == ".php") {
				$es1sc_buynow_url = $es1sc_buynow_url."?";
			} else {
				$es1sc_buynow_url = $es1sc_buynow_url."&";
			}
			$BuyNow = '<a href="'.$es1sc_buynow_url.'pid='.$product_details->ProductInfo->VisibleId.'"><img src="'.get_option('es1sc_cart_image').'" alt="Add to Cart" /></a>';

			$aVariables = array('#ProductName', '#ProductImage', '#ShortDescription', '#LongDescription', '#ProductSku', '#ProductPrice','#BuyNow');
			$aReplacements = array($product_details->ProductInfo->ProductName, $ImageUrl, wpautop($product_details->ProductInfo->ShortDescription), wpautop($product_details->ProductInfo->LongDescription), $product_details->ProductInfo->ProductSku, $ProductPrice, $BuyNow);

			$retVal .= str_replace($aVariables, $aReplacements, $display_format);
		}
	}

	return $retVal;
}
add_shortcode('es1sc_prodlist', 'es1sc_product_list');

/**
 * Create a potbox widget
 */
function postbox($id, $title, $content) {
?>
	<div id="<?php echo $id; ?>" class="postbox">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle"><span><?php echo $title; ?></span></h3>
		<div class="inside">
			<?php echo $content; ?>
		</div>
	</div>
<?php
}

function plugin_like() {
	$content = '<p>'.__('Why not do any or all of the following:','es1scplugin').'</p>';
	$content .= '<ul>';
	$content .= '<li>- <a href="http://wordpress.org/extend/plugins/wordpress-plugin-for-1shoppingcart//" target="_blank">'.__('Give it a good rating on WordPress.org.','es1scplugin').'</a></li>';
	$content .= '<li>- <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=H8KWRPTET2SK2&lc=US&item_name=Free%20Wordpress%20Plugin%20for%201ShoppingCart&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank">'.__('Donate a token of your appreciation.','es1scplugin').'</a></li>';
	$content .= '</ul>';
	postbox('free-wordpress-plugin-for-1shoppingcart'.'like', 'Like this plugin?', $content);
}

function plugin_didyouknow() {
	$content = '<ul>';
	$content .= '<li>- You can create a listing of products by categories to include on specific pages.  To do so, you would include the following  shortcode<br />[es1sc_prodlist prd_ids="XXXX01,XXXX02,XXXX03,XXXX04,XXXX05"]. where <em>XXXX99</em> is the 1ShoppingCart.com product ids separated by commas.</li>';
	$content .= '<li>- This plugin comes with a number of "add to cart images."  The images are black, white, red, blue, orange, green and purple. These images are y are located at '.plugin_dir_url(__FILE__).'images/add-to-cart-COLOR.png. Just replace COLOR with the actual color you would like to use in lowercase, such as: '.plugin_dir_url(__FILE__).'images/add-to-cart-red.png.</li>';
	$content .= '<li>- This plugin comes with a black and white no image available image. It is located at '.plugin_dir_url(__FILE__).'images/image-not-available.png.</li>';
	$content .= '</ul>';
	postbox('free-wordpress-plugin-for-1shoppingcart'.'-didyouknow', 'Did You Know?', $content);
}


function plugin_help() {
	$content = '<p>'.__('Do you need help to get this plugin working?','es1scplugin').'</p>';
	$content .= '<p>Please check following resources:</p>';
	$content .= '<ul>';
	$content .= '<li>- <a href="http://equalserving.uservoice.com/knowledgebase/articles/85741-how-do-i-install-the-free-1shoppingcart-plugin-for" target="_blank">Check the detailed installation instruction</a></li>';
	$content .= '<li>- <a href="http://equalserving.uservoice.com/knowledgebase/topics/13674-1shoppingcart-wordpress-plugin-general" target="_blank">Visit the Free Wordpress Plugin For 1ShoppingCart.com Knowledgebase</a></li>';
	$content .= '<li>- <a href="http://wpdemo.equalserving.com/store/" target="_blank">See the plugin in action</a></li>';
	$content .= '</ul>';
	$content .= '<hr /><p><strong>Troubleshooting</strong></p><hr />';
	$content .= '<p><strong>To verify that you have entered the correct 1ShoppingCart.com API Merchant ID and Key, please click on the link below -</strong><br />';
	$content .= '<a href="https://www.mcssl.com/API/'.get_option('es1sc_merchant_id').'/Products/LIST?key='.get_option('es1sc_merchant_key').'" target="_blank">Verify 1Shoppingcart.com Data</a>';
	$content .= '</p>';
	postbox('free-wordpress-plugin-for-1shoppingcart'.'-help', 'Help', $content);
}
?>
