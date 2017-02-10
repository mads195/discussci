<?php
/**
 * session_helper short summary.
 *
 * session_helper description.
 *
 * @version 1.0
 * @author Martin
 */
function is_signed_in($oThisZ,$bRedirect=true) {
    $oThisZ->load->library('Twitter_lib');
    if(!$oThisZ->twitter_lib->is_signed_in()) { 
        if($bRedirect) {
            redirect('welcome');
        }
        else {
            return false;
        }
    }
    return true;
}