<?php
/**
 * Plugin Name: Mailhog for WordPress
 * Plugin URI: https://hamish-fleming.com
 * Description: Redirects all WordPress emails to Mailhog
 * Version: 1.0.0
 * Author: Hamish Fleming
 * Author URI: https://hamish-fleming.com
 */
namespace HamishFleming\MailhogForWordPress;
//Recommended by wordpress
defined( 'ABSPATH' ) OR exit;

/*
   * Add the MailHog to your wordpress projects
   * By Khalid Ahmada
   * MailHog @see https://github.com/mailhog/MailHog
   */

  class WP_MAILHOG
  {

    function __construct()
    {
      // Config only on local
      if ($this->isLocal()) {
        $this->AddSMTP();
      }

    }

	public static function get_mailer() {
		global $wp_version;
		if( $wp_version < '5.5') {
		    require_once(ABSPATH . WPINC . '/class-phpmailer.php');
		    require_once(ABSPATH . WPINC . '/class-smtp.php');
		    $mail = new PHPMailer( true );
		}
		else {
		    require_once(ABSPATH . WPINC . '/PHPMailer/PHPMailer.php');
		    require_once(ABSPATH . WPINC . '/PHPMailer/SMTP.php');
		    require_once(ABSPATH . WPINC . '/PHPMailer/Exception.php');
		    $mail = new PHPMailer\PHPMailer\PHPMailer( true );
		}
		return WP_MAILHOG::set_mailer($mail);
	}

	public static function set_mailer($mail) {
	    $mail->isSMTP();
	    $mail->Host = 'mailhog';
	    $mail->Port = '1025';
	    $mail->SMTPAuth = false;
	    $mail->SMTPSecure = '';
	    $mail->SMTPAutoTLS = false;
	    return $mail;
	}

	public static function send_test_email() {
		$mail = WP_MAILHOG::get_mailer();
		$mail->From = 'webadmin@localhost.com';
		$mail->FromName = 'Boss Hog';
		$mail->addAddress('test@localhost.com');
		$mail->Subject = 'Test Email';
		$mail->Body = 'This is a test email';
		$mail->send();
	}


    /**
     * Config Your local rule
     * default is check if the host is *.test or  *.local
     * @return bool
     */
    private function isLocal()
    {

		return true;
      if (defined('WP_HOME')) {
        if (strpos(WP_HOME, '.test') !== false ||
          strpos(WP_HOME, '.local') !== false
        ) {
          return true;
        }
      }

      return false;

    }

    /*
     * Wordpress default hook to config php mail
     */
    private function AddSMTP()
    {
	add_action('wp_mail_failed', 'action_wp_mail_failed', 10, 1);
      add_action('phpmailer_init', array($this, 'configEmailSMTP'));
    }


    /*
     * Config MailTramp SMTP
     */
    public function configEmailSMTP( $phpmailer)
    {
      $phpmailer->IsSMTP();
      $phpmailer->Host='mailhog';
      $phpmailer->Port=1025;
      $phpmailer->Username='';
      $phpmailer->Password='';
      $phpmailer->SMTPAuth=false;
        $phpmailer->SMTPSecure = '';
	$phpmailer->SMTPAutoTLS = false;
    }
  }
// add Mailhog Action
  new WP_MAILHOG();

