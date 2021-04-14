<div class="container jivo-center jivo-small">
    <div class="row justify-content-center">
        <div class="col-6 jivo-content">
            <h1>
                <?php require 'jivo_logo.php'; ?>
            </h1>
            <div class="jivo-margin">
                <div><?php _e('Have questions? Don\'t worry, we offer 24/7 live support!', 'jivochat'); ?></div>
                <div><?php _e('Ask us anything and we will help you onboarding.', 'jivochat'); ?></div>
            </div>
            <div class="jivo-margin">
                <a class="btn jivoButton" href="https://<?php esc_html_e('www.jivochat.com','jivochat'); ?>?utm_source=wordpress" target="_blank"><?php _e('Live Chat with Us','jivochat'); ?></a>
                <a class="btn jivoButton" href="https://<?php esc_html_e('www.jivochat.com','jivochat'); ?>/help?utm_source=wordpress" target="_blank"><?php _e('Knowledge Base','jivochat'); ?></a>
            </div>
            <div class="jivo-margin">
                <div><?php _e('If you would like to use another JivoChat account, you can use this log out button to login with another account.', 'jivochat'); ?></div>
            </div>
            <div class="jivo-margin">
                <form id="reset_form" method="POST">
                    <input type="hidden" name="reset" value="reset">
                    <input id="reset_button" class="btn jivoButton" type="submit" value="<?php esc_html_e('Log Out','jivochat'); ?>">
                </form>
            </div>
        </div>
    </div>
</div>
