<?php
/**
 * Invoices - template/user_dashboard_invoices
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 07/04/16
 * Time: 11:34 PM
 */
global $homey_local, $dashboard_invoices;
$invoice_data = homey_get_invoice_meta( get_the_ID() );
$user_info = get_userdata($invoice_data['invoice_buyer_id']);
$invoice_detail = add_query_arg( 'invoice_id', get_the_ID(), $dashboard_invoices );
?>
<tr>
    <td data-label="<?php esc_html_e('Order', 'homey'); ?>">
        #<?php echo get_the_ID(); ?>
    </td>
    <td data-label="<?php esc_html_e('Date', 'homey'); ?>">
        <?php echo get_the_date(homey_convert_date(homey_option('homey_date_format'))); ?>
    </td>
    <td data-label="<?php echo esc_attr($homey_local['billing_for']); ?>">
        <?php

        if($invoice_data['invoice_billion_for'] == 'reservation') {
            
            echo esc_attr($homey_local['resv_fee_text']);

        } elseif($invoice_data['invoice_billion_for'] == 'listing') {
            if( $invoice_data['upgrade'] == 1 ) {
                echo esc_attr($homey_local['upgrade_text']);

            } else {
                echo get_the_title( get_post_meta( get_the_ID(), 'homey_invoice_item_id', true) );
            }
        } elseif($invoice_data['invoice_billion_for'] == 'upgrade_featured') {
                echo esc_attr($homey_local['upgrade_text']);
                
        } elseif($invoice_data['invoice_billion_for'] == 'package') {
            echo esc_attr($homey_local['inv_package']);
        }

        ?>
    </td>
    <td data-label="<?php esc_html_e('Billing Type', 'homey'); ?>">
        <?php echo esc_html_e( $invoice_data['invoice_billing_type'], 'homey' ); ?>
    </td>
    <td data-label="<?php esc_html_e('Status', 'homey'); ?>">
        <?php
        $invoice_status = get_post_meta(  get_the_ID(), 'invoice_payment_status', true );
        if( $invoice_status == 0 ) {
            echo '<span class="label label-warning">'.esc_attr($homey_local['not_paid']).'</span>';
        } else {
            echo '<span class="label label-success">'.esc_attr($homey_local['paid']).'</span>';
        }
        ?>
    </td>
    
    
    <td data-label="<?php esc_html_e('Payment Method', 'homey'); ?>">
        <?php echo esc_html($invoice_data['invoice_payment_method']);?>
    </td>
    <td data-label="<?php esc_html_e('Total', 'homey'); ?>">
        <strong><?php echo homey_formatted_price( $invoice_data['invoice_item_price'] );?></strong>
    </td>
    <td data-label="<?php esc_html_e('Actions', 'homey'); ?>">
        <div class="custom-actions">
            <button class="btn btn-secondary" onclick="location.href='<?php echo esc_url($invoice_detail); ?>';">
                <?php echo esc_attr($homey_local['inv_btn_details']);?>
            </button>
        </div>
    </td>
</tr>