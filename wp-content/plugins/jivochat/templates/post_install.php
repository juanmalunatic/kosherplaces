<div class="container jivo-center jivo-big">
    <div class="row justify-content-center">
        <div class="col-6 jivo-content">
            <h1>
                <?php require 'jivo_logo.php'; ?>
            </h1>
            <div class="jivo-margin">
                <h5><?php _e('Congratulations, you have successfully installed JivoChat widget to your website!', 'jivochat'); ?></h5>
            </div>
            <div class="jivo-margin">
                <?php _e('You are now able to see live chat widget on your webiste. Now you can customize & configure your JivoChat widget. Click the button below to go to web app and start testing it!', 'jivochat'); ?>
            </div>
            <div class="jivo-margin">
                <a class="btn jivoButton" href="<?=JIVO_INTEGRATION_URL.'/login?partnerId=wordpress&token='.get_option('jivosite_token').'&lang='.get_option('jivosite_lang_code')?>" target="_blank"><?php _e('Go to Web Application','jivochat'); ?></a>
            </div>
            <div class="jivo-margin">
                <?php _e('You can either use our web app, our desktop apps for Windows, macOS, or mobile apps for iOS and Android. We recommend using desktop and mobile apps simultaneously.', 'jivochat'); ?>
            </div>
            <div class="jivo-margin">
                <a class="btn jivoButton" href="https://<?php esc_html_e('www.jivochat.com','jivochat'); ?>/apps?utm_source=wordpress" target="_blank"><?php _e('Install Apps','jivochat'); ?></a>
            </div>
            <div class="jivo-margin">
                <div><?php _e('Have questions? Don\'t worry, we offer 24/7 live support!', 'jivochat'); ?></div>
                <div><?php _e('Ask us anything and we will help you onboarding.', 'jivochat'); ?></div>
            </div>
            <div class="jivo-margin">
                <a class="btn jivoButton" href="https://<?php esc_html_e('www.jivochat.com','jivochat'); ?>?utm_source=wordpress" target="_blank"><?php _e('Live Chat with Us','jivochat'); ?></a>
                <a class="btn jivoButton" href="https://<?php esc_html_e('www.jivochat.com','jivochat'); ?>/help?utm_source=wordpress" target="_blank"><?php _e('Knowledge Base','jivochat'); ?></a>
            </div>
        </div>
    </div>
</div>