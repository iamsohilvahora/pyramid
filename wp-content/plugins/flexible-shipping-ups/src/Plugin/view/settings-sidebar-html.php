<?php
/**
 * Settings sidebar.
 *
 * @package WPDesk\FlexibleShippingUps
 */

/**
 * Params.
 *
 * @var $pro_url string .
 */
?>
<div class="wpdesk-metabox">
    <div class="wpdesk-stuffbox">
        <h3 class="title"><?php _e( 'Get Flexible Shipping UPS PRO!', 'flexible-shipping-ups' ); ?></h3>
        <div class="inside">
            <div class="main">
                <ul>
                    <li><span class="dashicons dashicons-yes"></span> <?php _e( 'Handling Fees', 'flexible-shipping-ups' ); ?></li>
                    <li><span class="dashicons dashicons-yes"></span> <?php _e( 'Delivery Dates', 'flexible-shipping-ups' ); ?></li>
                    <li><span class="dashicons dashicons-yes"></span> <?php _e( 'Box Packing', 'flexible-shipping-ups' ); ?></li>
                    <li><span class="dashicons dashicons-yes"></span> <?php _e( 'Access Points Select', 'flexible-shipping-ups' ); ?></li>
                    <li><span class="dashicons dashicons-yes"></span> <?php _e( 'Flat Rate for Access Points', 'flexible-shipping-ups' ); ?></li>
                    <li><span class="dashicons dashicons-yes"></span> <?php _e( 'Multicurrency Support', 'flexible-shipping-ups' ); ?></li>
                </ul>
                <a class="button button-primary" href="<?php echo $pro_url; ?>" target="_blank"><?php _e( 'Upgrade Now &rarr;', 'flexible-shipping-ups' ); ?></a>
            </div>
        </div>
    </div>
</div>
