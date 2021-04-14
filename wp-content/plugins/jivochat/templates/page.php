<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<link rel="stylesheet" href="<?=JIVO_PLUGIN_URL.'jivosite.css'?>">
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

<?php if(! get_option('jivosite_widget_id')) { ?>
    <?php require 'signup.php'?>
    <?php require 'login.php'?>
<?php } elseif (get_option('jivosite_widget_id') && !empty($post_install)) { ?>
    <?php require 'post_install.php' ?>
<?php } elseif (get_option('jivosite_widget_id') && empty($post_install)) { ?>
    <?php require 'reset.php'?>
<?php } ?>

<script type="text/javascript">
    (function($) {
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();

            $('#signup_form').submit(function () {
                $('#signup_button').attr('disabled', true);
            });
            $('#login_form').submit(function () {
                $('#login_button').attr('disabled', true);
            });
            $('#reset_form').submit(function () {
                $('#reset_button').attr('disabled', true);
            });

            $('#login_link').click(
                function () {
                    $('#login_block').css('display', 'block');
                    $('#signup_block').css('display', 'none');
                }
            );
            $('#signup_link').click(
                function () {
                    $('#login_block').css('display', 'none');
                    $('#signup_block').css('display', 'block');
                    $(window).scrollTop(0);
                }
            );

            const languageList = <?=$languageList?>;
            const suggestedLanguageName = languageList[0].name;
            languageList.forEach(function (language) {
                let lang = language.code;
                if (language.name == 'Spanish (General)') {
                    lang = lang.split(',');
                    lang = lang[0];
                }
                const $option = $('<option>').attr('value', lang).attr('class', 'dropdown-item').text(language.name);
                $('#languageList').append($option);
            });

            $('#help_language_signup').click(function () {
                $('#suggested_language').text(suggestedLanguageName);
            });
            $('#help_language_signup').hover(function () {
                $('#suggested_language').text(suggestedLanguageName);
            });
        });
    })(jQuery);
</script>