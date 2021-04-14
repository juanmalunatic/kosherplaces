<div id="login_block" class="container jivo-small"
    <?php if (!empty($login)) { ?> style="display: block"
    <?php } else { ?> style="display: none" <?php } ?>
>
    <div class="row">
        <div class="col align-self-start">
        </div>
        <div class="col-8 align-self-center">
            <form id="login_form" method="POST" class="jivo-content">
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <h1 class="jivo-center">
                            <?php require 'jivo_logo.php'; ?>
                        </h1>
                    </div>
                    <div class="form-group col jivo-form-second-col"></div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <h5 class="jivo-form-caption"><?php _e('Login to your JivoChat account','jivochat'); ?></h5>
                        <h5 class="jivo-form-caption"><?php _e('to connect it to your store','jivochat'); ?></h5>
                    </div>
                    <div class="form-group col jivo-form-second-col"></div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <label for="email_login"><?php _e('Your Jivo E-mail Address','jivochat'); ?></label>
                        <input type="email" class="form-control" id="email_login" name="email" required>
                    </div>
                    <div class="form-group col jivo-form-second-col">
                        <div class="jivo-help-container">
                            <span id="help_email_signup" class="jivo-help">
                                <img src="<?php echo JIVO_PLUGIN_URL; ?>img/question.svg"
                                     class="question-svg"
                                     data-toggle="tooltip"
                                     data-placement="right"
                                     title="<?php esc_html_e('Please enter the e-mail address you use for login.','jivochat'); ?>"
                                     data-trigger="hover focus click"
                                     data-template='<div class="tooltip" role="tooltip"><div class="tooltip-inner jivoButton"></div></div>'
                                />
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <label for="password_login"><?php _e('Password','jivochat'); ?></label>
                        <input type="password" class="form-control" id="password_login" name="userPassword" required pattern="(?=.*\d)(?=.*[A-Z]).{6,}">
                    </div>
                    <div class="form-group col jivo-form-second-col">
                        <div class="jivo-help-container">
                            <span id="help_password_signup" class="jivo-help">
                                <img src="<?php echo JIVO_PLUGIN_URL; ?>img/question.svg"
                                     class="question-svg"
                                     data-toggle="tooltip"
                                     data-placement="right"
                                     title="<?php esc_html_e('Please enter the password for your JivoChat account.','jivochat'); ?>"
                                     data-trigger="hover focus click"
                                     data-template='<div class="tooltip" role="tooltip"><div class="tooltip-inner jivoButton"></div></div>'
                                />
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <button id="login_button" type="submit" class="form-control btn jivoButton"><?php _e('Login','jivochat'); ?></button>
                    </div>
                    <div class="form-group col jivo-form-second-col"></div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-center jivo-form-first-col">
                        <b style="color:red;"><?php echo $error; ?></b>
                    </div>
                    <div class="form-group col jivo-form-second-col"></div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-center jivo-form-first-col">
                        <?php _e('Forgot your password? You can reset it <a class="jivo-redirect" href="https://app.jivosite.com/password/reset?utm_source=wordpress" target="_blank">here</a>', 'jivochat'); ?>
                    </div>
                    <div class="form-group col jivo-form-second-col"></div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <div  class="jivo-center jivo-form-bottom-link">
                            <?php _e('You don\'t have a Jivo account? <span id="signup_link" class="jivo-redirect">Sign up</span>', 'jivochat') ?>
                        </div>
                    </div>
                    <div class="form-group col jivo-form-second-col"></div>
                </div>
            </form>
        </div>
    </div>
</div>