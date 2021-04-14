<script src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js" type="text/javascript"></script>
<div class="wrap">
    <h1>
        <?php require 'jivo_logo.php'; ?>
    </h1>
    <b style="color:red;"><?php echo $error; ?></b>
        <div class="gray_form">
            <?php
                list($code) = explode('-', get_bloginfo("language"));
                printf(
                        __('Unfortunately, your server configuration does not allow the plugin to connect to JivoChat servers to create account. Please, go to <a target="_blank" href="https://app.jivosite.com/?lang=%s">https://app.jivosite.com</a> and sign up. During the signup process you will be offered to download another Wordpress module that does not require to communicate over the network','jivosite'),
                        $code
                );
            ?>
        </div>
</div>