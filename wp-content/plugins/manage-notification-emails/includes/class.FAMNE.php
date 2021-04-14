<?php
/**
 * Manage notification emails settings page class
 *
 * initializes the plugin.
 * 
 * This file is part of the Manage Notification Emails plugin
 * You can find out more about this plugin at http://www.freeamigos.mx
 * Copyright (c) 2006-2015  Virgial Berveling
 *
 * @package WordPress
 * @author Virgial Berveling
 * @copyright 2006-2015
 *
 * since: 1.6.0
 */

if (!class_exists('FAMNE')):
class FAMNE{

    static public function get_option($name)
    {
        if ($name == 'fa_mne_version') return get_site_option($name);
        
        return get_option($name);
    }

    static public function update_option($name,$options)
    {
        if ($name == 'fa_mne_version') return update_site_option($name,$options);
        
        return update_option($name,$options);

    }
    static public function update_check()
    {
        if (self::get_option( 'fa_mne_version' ) != FA_MNE_VERSION) {
            $options = self::get_option( 'famne_options' );
            $current_version = self::get_option( 'fa_mne_version' );
            
            /* Is this the first install, then set all defaults to active */
            if ($options === false)
            {
                $options = array(
                    'wp_new_user_notification_to_user'      => '1', 
                    'wp_new_user_notification_to_admin'     => '1', 
                    'wp_notify_postauthor'                  => '1',
                    'wp_notify_moderator'                   => '1',
                    'wp_password_change_notification'       => '1',
                    'send_password_change_email'            => '1',
                    'send_email_change_email'               => '1',
                    'send_password_forgotten_email'         => '1',
                    'send_password_admin_forgotten_email'   => '1',
                    'auto_core_update_send_email'           => '1',
                    'auto_plugin_update_send_email'         => '1',
                    'auto_theme_update_send_email'          => '1'
                );

                self::update_option('famne_options',$options);

                /* UPDATE DONE! */
                self::update_option('fa_mne_version',FA_MNE_VERSION);
                return;
            }

            if ( version_compare($current_version, '1.5.1') <=0)
            {
                /** update to 1.6.0
                 * setting the newly added options to checked as default
                 */
                $options['auto_plugin_update_send_email'] = '1';
                $options['auto_theme_update_send_email'] = '1';
                self::update_option('famne_options',$options);
            }

            if ($current_version == '1.1.0')
            {
                /** update 1.1.0 to 1.2.0
                 * setting the newly added options to checked as default
                 */

                $options['send_password_forgotten_email'] = '1';
                $options['send_password_admin_forgotten_email'] ='1';

                self::update_option('famne_options',$options);
            }
            
            
            /** update to 1.4.1
             * setting the newly added options to checked as default
             */

            if ( version_compare($current_version, '1.4.0') <=0)
            {
                $options['auto_core_update_send_email'] ='1';
                self::update_option('famne_options',$options);
            }
            
            
            
            /** update 1.0 to 1.1 fix:
             * update general wp_new_user_notification option into splitted options    
             */
            if ( version_compare($current_version, '1.0.0') <=0)
            {
                unset($options['wp_new_user_notification']);
                $options['wp_new_user_notification_to_user'] ='1';
                $options['wp_new_user_notification_to_admin'] ='1';
                self::update_option('famne_options',$options);
            }

            /* UPDATE DONE! */
            self::update_option('fa_mne_version',FA_MNE_VERSION);
        }
    }
}
endif;