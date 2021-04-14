<div id="signup_block" class="container jivo-big" <?php if (!empty($login)) { ?> style="display: none" <?php } ?>>
    <div class="row">
        <div class="col align-self-start">
        </div>
        <div class="col-8 align-self-center">
            <form id="signup_form" method="POST" class="jivo-content">
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
                        <h5 class="jivo-form-caption"><?php _e('Create a JivoChat account','jivochat'); ?></h5>
                        <h5 class="jivo-form-caption"><?php _e('to connect with your website','jivochat'); ?></h5>
                    </div>
                    <div class="form-group col jivo-form-second-col"></div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <label for="email_signup"><?php _e('Your Business E-mail Address','jivochat'); ?></label>
                        <input type="email" class="form-control" id="email_signup" name="email" required>
                    </div>
                    <div class="form-group col jivo-form-second-col">
                        <div class="jivo-help-container">
                            <span id="help_email_signup" class="jivo-help">
                                <img src="<?php echo JIVO_PLUGIN_URL; ?>img/question.svg"
                                     class="question-svg"
                                     data-toggle="tooltip"
                                     data-placement="right"
                                     title="<?php esc_html_e('Please specify the email you will use to login to the agent’s app and admin panel.','jivochat'); ?>"
                                     data-trigger="hover focus click"
                                     data-template='<div class="tooltip" role="tooltip"><div class="tooltip-inner jivoButton"></div></div>'
                                />
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <label for="password_signup"><?php _e('Password','jivochat'); ?></label>
                        <input type="password" class="form-control" id="password_signup" name="userPassword" required pattern="(?=.*\d)(?=.*[A-Z]).{6,}">
                    </div>
                    <div class="form-group col jivo-form-second-col">
                        <div class="jivo-help-container">
                            <span id="help_password_signup" class="jivo-help">
                                <img src="<?php echo JIVO_PLUGIN_URL; ?>img/question.svg"
                                     class="question-svg"
                                     data-toggle="tooltip"
                                     data-placement="right"
                                     title="<?php esc_html_e('Please create a new JivoChat account password. Password is least 6 characters long, contains at least one digit and uppercase letter.','jivochat'); ?>"
                                     data-trigger="hover focus click"
                                     data-template='<div class="tooltip" role="tooltip"><div class="tooltip-inner jivoButton"></div></div>'
                                />
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <label for="name_signup"><?php _e('Agent Name','jivochat'); ?></label>
                        <input type="text" class="form-control" id="name_signup" name="userDisplayName" required>
                    </div>
                    <div class="form-group col jivo-form-second-col">
                        <div class="jivo-help-container">
                            <span id="help_name_signup" class="jivo-help">
                                <img src="<?php echo JIVO_PLUGIN_URL; ?>img/question.svg"
                                     class="question-svg"
                                     data-toggle="tooltip"
                                     data-placement="right"
                                     title="<?php esc_html_e('The agent name that will be displayed to website visitors in the JivoChat chat window.','jivochat'); ?>"
                                     data-trigger="hover focus click"
                                     data-template='<div class="tooltip" role="tooltip"><div class="tooltip-inner jivoButton"></div></div>'
                                />
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <label for="language_list"><?php _e('Widget Language','jivochat'); ?></label>
                        <select id="languageList" class="form-control" name="languageList" required></select>
                    </div>
                    <div class="form-group col jivo-form-second-col">
                        <div class="jivo-help-container">
                            <span class="jivo-help">
                                <img id="help_language_signup" src="<?php echo JIVO_PLUGIN_URL; ?>img/question.svg"
                                     class="question-svg"
                                     data-toggle="tooltip"
                                     data-placement="right"
                                     data-html="true"
                                     title='<div><?php esc_html_e('You can choose a language to have it in your JivoChat widget on your website. Suggested language for your JivoChat widget is: <b id="suggested_language"></b><div>If you want to change it, please select an alternative at dropdown list.</div>','jivochat'); ?></div>'
                                     data-trigger="hover focus click"
                                     data-template='<div class="tooltip" role="tooltip"><div class="tooltip-inner jivoButton"></div></div>'
                                />
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <button id="signup_button" type="submit" class="form-control btn jivoButton"><?php _e('Sign Up','jivochat'); ?></button>
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
                    <div class="form-group col jivo-form-first-col">
                        <div class="jivo-caption"><?php _e('By creating an account you agree to <a class="jivo-caption" href="https://www.jivochat.com/terms?utm_source=wordpress" target="_blank">Terms and Conditions</a> and <a class="jivo-caption" href="https://www.jivochat.com/privacy-notice?utm_source=wordpress" target="_blank">Privacy Policy</a>', 'jivochat') ?></div>
                    </div>
                    <div class="form-group col jivo-form-second-col"></div>
                </div>
                <div class="form-row">
                    <div class="form-group col jivo-form-first-col">
                        <div class="jivo-center jivo-form-bottom-link">
                            <?php _e('Already have an account? <span id="login_link" class="jivo-redirect">Log in</span>', 'jivochat') ?>
                        </div>
                    </div>
                    <div class="form-group col jivo-form-second-col"></div>
                </div>
            </form>
        </div>
    </div>
</div>