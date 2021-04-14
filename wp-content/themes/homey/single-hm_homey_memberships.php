<?php
/**
 * The Template for displaying all single posts for hm membership
 * @since Homey 1.6.3
 */
get_header();
global $post, $homey_local;
$hm_options = get_option('hm_memberships_options');

//<editor-fold desc="init all variables">
$homeyPlugin = PLUGINDIR . "/homey-membership";

$uri = explode('/', $_SERVER['REQUEST_URI']);

define('PAYPAL_CLIENT_ID', $hm_options['paypal_client_id']);

// get access token
$is_paypal_live = $hm_options['paypal_status'];
$host = 'https://api.sandbox.paypal.com';
// Check if paypal live
if ($is_paypal_live == 'live') {
    $host = 'https://api.paypal.com';
}

$url = $host . '/v1/oauth2/token';
$postArgs = 'grant_type=client_credentials';
$access_token = homey_get_paypal_access_token($url, $postArgs);
// end of getting access token

$baseUrl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    '/'.$uri[1]
);
$baseUrl .= '/';
$membership_settings = get_option('hm_memberships_options');
$currency = isset($membership_settings['currency'])?$membership_settings['currency']:'USD';
$terms_conditions = homey_option('login_terms_condition');
$enable_password = homey_option('enable_password');
$enable_forms_gdpr = homey_option('enable_forms_gdpr');
$forms_gdpr_text = homey_option('forms_gdpr_text');

$postID = isset($post->ID)?$post->ID:get_the_ID();
$post_title = $post->post_title;

$billing_period = get_post_meta($postID, 'hm_settings_bill_period', true);
$billing_frequency = get_post_meta($postID, 'hm_settings_billing_frequency', true);
$listings_included = get_post_meta( $postID, 'hm_settings_listings_included', true );
$unlimited_listings = get_post_meta( $postID, 'hm_settings_unlimited_listings', true );
$featured_listings = get_post_meta( $postID, 'hm_settings_featured_listings', true );
$stripe_package_id = get_post_meta( $postID, 'hm_settings_stripe_package_id', true );
$visibility = get_post_meta( $postID, 'hm_settings_visibility', true );
$images_per_listing = get_post_meta( $postID, 'hm_settings_images_per_listing', true );
$unlimited_images = get_post_meta( $postID, 'hm_settings_unlimited_images', true );
$tax_id_stripe = get_post_meta( $postID, 'hm_settings_tax_id_stripe', true );
$tax_id_paypal = get_post_meta( $postID, 'hm_settings_tax_id_paypal', true );
$popular_featured = get_post_meta( $postID, 'hm_settings_popular_featured', true );
$custom_link = get_post_meta( $postID, 'hm_settings_custom_link', true );
$package_total_price = $package_price = get_post_meta( $postID, 'hm_settings_package_price', true );

$enable_paypal = $hm_options['paypal_status'];
$enable_stripe = $hm_options['stripe_status'];
$stripe_processor_link = homey_get_template_link('template/template-membership-webhook.php');
$paypal_processor_link = $stripe_processor_link.'?is_homey_membership=1&payment_gateway=paypal';
//</editor-fold>

//<editor-fold desc="Stripe Related Code">
    function createStripeProductAndPlan($hm_options)
    {
        $stripe_pk = $hm_options['stripe_pk'];
        //to check if already stripe product is created?
        $hm_stripe_product_id = get_option('hmStripePid_'.$stripe_pk);

        //We have to create stripe product if already not created
        if(empty($hm_stripe_product_id)){
            $data = array(
                "name" => "Homey Memberships"
            );

            $stripe = new \Stripe\StripeClient(
                $hm_options['stripe_sk']
            );

            $stripe_product_info = $stripe->products->create($data);

            $hm_stripe_product_id = isset($stripe_product_info->id)?$stripe_product_info->id:-1;
            if($hm_stripe_product_id != -1){
                update_option('hmStripePid_'.$stripe_pk, $hm_stripe_product_id);
            }
        }

        return $hm_stripe_product_id;
    }

    $hm_stripe_product_id = createStripeProductAndPlan($hm_options);
//check for valid product's plan from stripe gateway, if not then try to create them.
    $hm_stripe_plan_id = get_option($postID.'_'.$hm_options['stripe_pk']);

    if(!empty(trim($hm_stripe_product_id)) && empty(trim($hm_stripe_plan_id))){
        //create plan on product
        $interval_unit = "month";
        if($billing_period == "weekly"){
            $interval_unit = "week";
        }if($billing_period == "daily"){
            $interval_unit = "day";
        }elseif ($billing_period == "monthly"){
            $interval_unit = "month";
        }elseif ($billing_period == "yearly") {
            $interval_unit = "year";
        }

        $stripeData = array (
            'amount' => (float) $package_price * 100,
            'currency' => $currency,
            'interval' => $interval_unit,
            'interval_count' => (int) $billing_frequency,
            'product' => $hm_stripe_product_id,
        );

        $stripe = new \Stripe\StripeClient(
            $hm_options['stripe_sk']
        );

        $productInfo = $stripe->plans->create($stripeData);

        update_option($postID.'_'.$hm_options['stripe_pk'], $productInfo->id);
        update_post_meta($postID, 'hm_settings_stripe_package_id', $productInfo->id);

        $hm_stripe_plan_id = $productInfo->id;
        $productInfo = '';
    }
//end for -> check for valid product's plan from stripe gateway, if not then try to create them.

//</editor-fold>

//<editor-fold desc="Paypal related code">

    function createProductAndPlan($access_token, $host)
    {
        //to check if already product is created?
        $hm_product_id = get_option('hm_prod_id_'.PAYPAL_CLIENT_ID);

        //We have to create paypal product if already not created
        if(empty($hm_product_id)){
            $jsonData = json_encode(array(
                "name" => "Homey Memberships",
                "description" => "Homey Memberships is for Host to buy different plans to post listings.",
                "type" => "SERVICE",
                "category" => "SOFTWARE",
                "image_url" => 'http://dev.homey.com/wp-content/uploads/2019/01/homey-logo.png',
                "home_url" => 'http://dev.homey.com/wp-content/uploads/2019/01/homey-logo.png'
            ));

            $productInfo = homey_execute_curl_request($host."/v1/catalogs/products", $jsonData, $access_token);

            if(isset($productInfo['id'])){
                update_option('hm_prod_id_'.PAYPAL_CLIENT_ID, $productInfo['id']);
                $hm_product_id = $productInfo['id'];
            }
        }
        return $hm_product_id;
    }
//to check if already product is created?
    $hm_product_id = createProductAndPlan($access_token, $host);

//check for valid product's plan from gateways, if not then try to create them.
    $hm_plan_id = get_option($postID.'_'.PAYPAL_CLIENT_ID);

    if(!empty(trim($hm_product_id)) && empty(trim($hm_plan_id))){
        //create plan on product
        $interval_unit = "MONTH";
        if($billing_period == "daily"){
            $interval_unit = "DAY";
        }if($billing_period == "weekly"){
            $interval_unit = "WEEK";
        }elseif ($billing_period == "monthly"){
            $interval_unit = "MONTH";
        }elseif ($billing_period == "yearly") {
            $interval_unit = "YEAR";
        }

        $payPalData = array (
            'product_id' => $hm_product_id,
            'name' => $post_title,
            'description' => $post_title.' package plan will allow you to create '.$listings_included.' numbers of listings.',
            'status' => 'ACTIVE',
            'billing_cycles' =>
                array (
                    0 =>
                        array (
                            'frequency' =>
                                array (
                                    'interval_unit' => $interval_unit,
                                    'interval_count' => (int) $billing_frequency,
                                ),
                            'tenure_type' => 'REGULAR',
                            'sequence' => 1,
                            'total_cycles' => 1,
                            'pricing_scheme' =>
                                array (
                                    'fixed_price' =>
                                        array (
                                            'value' => (float) $package_price,
                                            'currency_code' => strtoupper($currency),
                                        ),
                                ),
                        ),
                ),
            'payment_preferences' =>
                array (
                    'auto_bill_outstanding' => true,
                    'setup_fee' =>
                        array (
                            'value' => 0,
                            'currency_code' => strtoupper($currency),
                        ),
                    'setup_fee_failure_action' => 'CONTINUE',
                    'payment_failure_threshold' => 3,
                ),
            'taxes' =>
                array (
                    'percentage' => (float) $tax_id_paypal,
                    'inclusive' => false,
                ),
        );

        $jsonData = json_encode($payPalData);

        $productInfo = homey_execute_curl_request($host."/v1/billing/plans", $jsonData, $access_token);
        update_option($postID.'_'.PAYPAL_CLIENT_ID, $productInfo['id']);
        update_post_meta($postID, 'hm_settings_paypal_package_id', $productInfo['id']);
        $hm_plan_id = $productInfo['id'];
    }
//end for -> check for valid product's plan from gateways, if not then try to create them.
//</editor-fold>

?>
<!--<editor-fold desc="html section">-->
    <section class="main-content-area">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="page-title">
                        <div class="block-top-title">
                            <?php get_template_part('template-parts/breadcrumb'); ?>
                        </div><!-- block-top-title -->
                    </div><!-- page-title -->
                </div><!-- col-xs-12 col-sm-12 col-md-12 col-lg-12 -->
            </div><!-- .row -->
        </div><!-- .container -->
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <div class="membership-package-order-detail">
                        <div class="block">
                            <div class="block-body">
                                <h3><?php echo esc_html__("Membership Package", 'homey'); ?></h3>

                                <ul class="list-unstyled mebership-list-info">
                                    <li><i class="fa fa-check" aria-hidden="true"></i> <?php echo esc_html__("Package", 'homey'); ?> <strong><?php  the_title(); ?></strong></li>
                                    <li><i class="fa fa-check" aria-hidden="true"></i> <?php echo esc_html__("Price", 'homey'); ?> <strong><?php echo $currency; ?><?php echo $package_price; ?></strong></li>
                                    <li><i class="fa fa-check" aria-hidden="true"></i> <?php echo esc_html__("Time Period", 'homey'); ?> <strong><?php echo $billing_frequency.' '.ucfirst($billing_period); ?></strong></li>
                                    <li><i class="fa fa-check" aria-hidden="true"></i>  <?php echo __("Number of Listings"); ?><strong><?php echo $listings_included; ?></strong></li>

                                    <!--                                    <li><i class="fa fa-check" aria-hidden="true"></i> --><?php //echo __("Taxes"); ?><!-- <strong>--><?php //echo $taxes; ?><!--%</strong></li>-->
                                    <li class="total-price"><?php echo __("Total Price"); ?><strong>$<?php echo $package_total_price; ?></strong></li>
                                </ul>

                            </div>
                        </div>
                    </div><!-- membership-package-order-detail -->
                    <div class="text-center">
                        <a href="<?php echo homey_get_template_link('template/template-membership-webhook.php'); ?>"><?php echo __("Change Package"); ?></a>
                    </div>
                </div>


                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                    <div class="membership-package-wrap">
                        <?php if ( !is_user_logged_in() ): ?>
                            <div class="block">
                                <div class="block-title">
                                    <div class="block-left">
                                        <h2 class="title"><?php echo __("Account Information"); ?></h2>
                                    </div><!-- block-left -->
                                    <div class="block-right">
                                        <?php echo __("Already have an account?"); ?> <a href="#" id="hm_membership_login_btn" data-toggle="modal" data-target="#modal-login"><?php echo esc_attr($homey_local['login_text']); ?></a>
                                    </div><!-- block-right -->
                                </div>
                                <div class="block-body">
                                    <div class="row"><div class="error-message error" id="hm_register_msgs"></div></div>
                                    <div class="row">
                                        <!--<div class="col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input class="form-control is-valid" placeholder="Enter your first name" type="text">
                                            </div>
                                        </div>--><!-- col-md-6 col-sm-12 -->
                                        <!--<div class="col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input class="form-control is-invalid" placeholder="Enter your last name" type="text">
                                            </div>
                                        </div>--><!-- col-md-6 col-sm-12 -->
                                        <form id="memberships_register_form">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label><?php echo __("Username"); ?></label>
                                                    <input name="username" type="text" class="form-control email-input-1" placeholder="<?php esc_html_e('Username','homey'); ?>" />
                                                </div>
                                            </div><!-- col-md-6 col-sm-12 -->
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label><?php echo __("Email"); ?></label>
                                                    <input type="useremail" name="useremail" class="form-control email-input-1" placeholder="<?php echo esc_html__('Email', 'homey'); ?>">
                                                </div>
                                            </div><!-- col-md-6 col-sm-12 -->
                                            <?php if( $enable_password == 'yes' ) { ?>
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label><?php echo __("Password"); ?></label>
                                                        <input name="register_pass" type="password" class="form-control password-input-1" placeholder="<?php echo esc_html__('Password', 'homey'); ?>">
                                                    </div>
                                                </div><!-- col-md-6 col-sm-12 -->
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label><?php echo __("Confirm Password"); ?></label>
                                                        <input name="register_pass_retype"  type="password" class="form-control password-input-2" placeholder="<?php echo esc_html__('Repeat Password', 'homey'); ?>">
                                                    </div>
                                                </div><!-- col-md-6 col-sm-12 -->
                                            <?php } ?>

                                            <?php get_template_part('template-parts/google', 'reCaptcha'); ?>

                                            <?php if($enable_forms_gdpr != 0) { ?>
                                                <div class="checkbox pull-left">
                                                    <label>
                                                        <input name="privacy_policy" type="checkbox">
                                                        <?php echo wp_kses($forms_gdpr_text, homey_allowed_html()); ?>
                                                    </label>
                                                </div>
                                            <?php } ?>

                                            <?php wp_nonce_field( 'homey_register_nonce', 'homey_register_security' ); ?>
                                            <input type="hidden" id="action" name="action" value="homey_register">
                                            <input type="hidden" id="role" name="role" value="homey_host">

                                            <div class="col-md-6 col-sm-12 checkbox pull-left">
                                                <label>
                                                    <input name="term_condition" type="checkbox"> <?php echo sprintf( wp_kses(__( 'I agree with your <a href="%s">Terms & Conditions</a>', 'homey' ), homey_allowed_html()), get_permalink($terms_conditions) ); ?>&nbsp;
                                                </label>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <button id="plugin_register_btn" class="hm_membership_register btn btn-primary">Register</button>
                                            </div>
                                        </form>
                                    </div>
                                </div><!-- block-body -->
                            </div><!-- block -->
                        <?php else: ?>
                            <div class="block">
                                <div class="block-head table-block">
                                    <h2 class="title"><?php echo esc_attr($homey_local['payment_label']); ?></h2>
                                </div>
                                <div class="block-body" >
                                    <!--                                <form name="homey_checkout" method="post" class="homey_payment_form" action="--><?php //echo esc_url($stripe_processor_link); ?><!--">-->
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h3><?php echo esc_attr($homey_local['select_payment']); ?></h3>
                                            <div class="payment-method">
                                                <?php if( $enable_paypal != 'disabled' ) { ?>
                                                    <div class="payment-method-block paypal-method">
                                                        <!-- Buy button -->
                                                        <script  src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&vault=true&disable-funding=credit,card"></script>
                                                        <script>
                                                            paypal.Buttons({
                                                                createSubscription: function(data, actions) {
                                                                    return actions.subscription.create({
                                                                        'plan_id': '<?php echo $hm_plan_id; ?>'
                                                                    });
                                                                },
                                                                onApprove: function(data, actions) {
                                                                    //console.log(data);
                                                                    jQuery("#paypal-button-container").html("<p class='btn btn-primary btn-block'><?php esc_html_e('Redirecting to Invoice Page.','homey'); ?></p>");
                                                                    window.location.href = '<?php echo $paypal_processor_link.'&hm_planID='.$postID;?>&success=1&subscriptionID='+data.subscriptionID
                                                                },
                                                                style: {
                                                                    size: 'rect',
                                                                    color: 'blue'
                                                                }
                                                            }).render('#paypal-button-container');
                                                        </script>

                                                        <div class="form-group">
                                                            <label class="control control--radio radio-tab">
                                                                <input class="homey_check_gateway" name="payment_gateway" value="paypal" type="radio">
                                                                <span class="control-text"><?php esc_html_e('Paypal', 'homey'); ?></span>
                                                                <span class="control__indicator"></span>
                                                                <span class="radio-tab-inner"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php } ?>

                                                <?php if( $enable_stripe != 'disabled' ) { ?>
                                                    <div class="payment-method-block stripe-method">
                                                        <div class="form-group">
                                                            <label class="control control--radio radio-tab">
                                                                <input class="homey_check_gateway" name="payment_gateway" value="stripe" type="radio">
                                                                <span class="control-text"><?php esc_html_e('Stripe', 'homey'); ?></span>
                                                                <span class="control__indicator"></span>
                                                                <span class="radio-tab-inner"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    if( $enable_stripe != 'disabled' ) {
                                        ?>
                                        <!--    <link href="--><?php //echo $baseUrl.$homeyPlugin;?><!--/assets/css/style.css" type="text/css" rel="stylesheet" />-->
                                        <script src="https://js.stripe.com/v3/"></script>
                                        <script src="<?php echo get_site_url(null, $homeyPlugin.'/assets/js/homey_membership_stripe.js')?>"></script>

                                        <div id="error-message"></div>
                                        <input name="basePluginUrl" id="basePluginUrl" value="<?php echo '/'.$homeyPlugin;?>" type="hidden">


                                    <?php } ?>
                                    <div style="display: none;" id="paypal-button-container"></div>
                                    <button id="homey_membership_payment" type="button" class="btn btn-primary btn-block"><?php echo esc_attr($homey_local['btn_process_pay']); ?></button>
                                    <!--                                </form>-->
                                </div>
                            </div>
                        <?php endif; ?>
                    </div><!-- membership-package-wrap -->
                </div><!-- col-xs-12 col-sm-12 col-md-8 col-lg-8 -->

            </div><!-- .row -->
        </div>   <!-- .container -->
        <?php if ( is_user_logged_in() ): ?>
            <script>
                var stripe = Stripe('<?php echo $hm_options['stripe_pk']; ?>');
                //Setup event handler to create a Checkout Session when button is clicked

                jQuery('input[name="payment_gateway"]').click(function () {

                    jQuery("#paypal-button-container").hide();
                    jQuery("#homey_membership_payment").show();

                    var selectedGateway = document.querySelector('input[name="payment_gateway"]:checked').value;
                    //alert(selectedGateway);
                    if(selectedGateway == 'paypal'){
                        jQuery("#homey_membership_payment").hide();
                        jQuery("#paypal-button-container").show();
                    }
                });

                document.getElementById("homey_membership_payment").addEventListener("click", function(evt) {
                    var selectedGateway = document.querySelector('input[name="payment_gateway"]:checked').value;

                    jQuery(this).text('please wait..');
                    jQuery(this).attr('disabled', 'disabled');

                    if(selectedGateway == 'paypal'){
                        // document.getElementById("paypal-form").submit();
                    }//paypal

                    else if(selectedGateway == 'stripe'){
                        createCheckoutSession('<?php echo $hm_stripe_plan_id; ?>', 1, '<?php echo $stripe_processor_link; ?>', '<?php echo $currency; ?>', '<?php echo $postID; ?>').then(function(data) {
                            // Call Stripe.js method to redirect to the new Checkout page
                            stripe.redirectToCheckout({
                                sessionId: data.id
                            }).then(handleResult)
                                .error(function (e) {
                                    console.log(e);
                                });
                        });
                    }else{
                        alert('please select payment gateway methods');
                    }

                });
            </script>
        <?php endif; ?>
    </section><!-- main-content-area listing-page grid-listing-page -->
<!--</editor-fold>-->

<?php
get_footer();
