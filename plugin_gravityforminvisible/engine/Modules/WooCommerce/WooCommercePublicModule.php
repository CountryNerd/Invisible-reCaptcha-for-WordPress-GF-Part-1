<?php


namespace InvisibleReCaptcha\Modules\WooCommerce;


use InvisibleReCaptcha\MchLib\Utils\MchWpUtils;
use InvisibleReCaptcha\Modules\BasePublicModule;
use InvisibleReCaptcha\Modules\WordPress\WordPressAdminModule;
use InvisibleReCaptcha\Modules\WordPress\WordPressPublicModule;
use InvisibleReCaptcha\RequestHandler;
use \WP_Error as WP_Error;

class WooCommercePublicModule extends BasePublicModule
{

	private $checkRegistrationHookId = null;

	public function __construct()
	{
		parent::__construct();

		if($this->getOption(WooCommerceAdminModule::OPTION_LOGIN_FORM_PROTECTION_ENABLED)){
			$this->activateLoginHooks();
		}

		if($this->getOption(WooCommerceAdminModule::OPTION_REGISTRATION_FORM_PROTECTION_ENABLED)){
			$this->activateRegistrationHooks();
		}

		if($this->getOption(WooCommerceAdminModule::OPTION_LOST_PASSWORD_FORM_PROTECTION_ENABLED)){
			$this->activateLostPasswordHooks();
		}

		if($this->getOption(WooCommerceAdminModule::OPTION_RESET_PASSWORD_FORM_PROTECTION_ENABLED)){
			$this->activateResetPasswordHooks();
		}

		if($this->getOption(WooCommerceAdminModule::OPTION_PRODUCT_REVIEW_FORM_PROTECTION_ENABLED)){
			$this->activateProductReviewHooks();
		}

	}

	public function activateLoginHooks()
	{
		MchWpUtils::addActionHook('woocommerce_login_form', function(){WooCommercePublicModule::getInstance()->renderReCaptchaHolderHtmlCode();}, PHP_INT_MAX);

		MchWpUtils::addFilterHook('woocommerce_process_login_errors', function($wpError){

			if(RequestHandler::isInvisibleReCaptchaTokenValid())
				return $wpError;

			return new WP_Error(\InvisibleReCaptcha::PLUGIN_SLUG, __('Your entry appears to be spam!', 'invisible-recaptcha'));

		});

	}

	public function activateRegistrationHooks()
	{
		$this->addActionHook('woocommerce_register_form', array($this, 'renderTokenFieldIntoRegistrationForm'), 999);
		$this->addActionHook('woocommerce_before_checkout_process', array($this, 'removeRegistrationHookOnCheckout'));
		$this->addActionHook('woocommerce_checkout_process', array($this, 'removeRegistrationHookOnCheckout'));

		$this->checkRegistrationHookId = $this->addFilterHook('woocommerce_process_registration_errors', array($this, 'validateRegistrationRequest'), 10, 4);

	}

	public function renderTokenFieldIntoRegistrationForm()
	{
		if(WordPressPublicModule::getInstance()->getOption(WordPressAdminModule::OPTION_REGISTRATION_FORM_PROTECTION_ENABLED)){
			WordPressPublicModule::getInstance()->removeRegistrationHooks();
		}

		$this->renderReCaptchaHolderHtmlCode();

	}

	public function removeRegistrationHookOnCheckout()
	{
		if( (! defined( 'WOOCOMMERCE_CHECKOUT' )) || (! WOOCOMMERCE_CHECKOUT) )
			return;

		$this->removeHookByIndex($this->checkRegistrationHookId);
	}

	public function validateRegistrationRequest($wpError, $userName, $password, $emailAddress)
	{
		if(RequestHandler::isInvisibleReCaptchaTokenValid())
			return $wpError;

		return new WP_Error(\InvisibleReCaptcha::PLUGIN_SLUG, __('Your entry appears to be spam!', 'invisible-recaptcha'));
	}


	public function activateLostPasswordHooks()
	{
		MchWpUtils::addActionHook('woocommerce_lostpassword_form', function(){WooCommercePublicModule::getInstance()->renderReCaptchaHolderHtmlCode();}, PHP_INT_MAX);

		MchWpUtils::addFilterHook('allow_password_reset', function(){

			return RequestHandler::isInvisibleReCaptchaTokenValid();

		}, PHP_INT_MAX);

	}


	public function activateResetPasswordHooks()
	{
		MchWpUtils::addActionHook('woocommerce_resetpassword_form', function(){WooCommercePublicModule::getInstance()->renderReCaptchaHolderHtmlCode();}, PHP_INT_MAX);

		MchWpUtils::addActionHook('validate_password_reset', function(){

			if( RequestHandler::isInvisibleReCaptchaTokenValid() )
				return;

			wp_redirect(home_url('/'));
			exit;

		}, PHP_INT_MAX);


	}



	public function activateProductReviewHooks()
	{
		if( ! WordPressPublicModule::getInstance()->getOption(WordPressAdminModule::OPTION_COMMENTS_FORM_PROTECTION_ENABLED)){
			MchWpUtils::addActionHook('comment_form', function(){WooCommercePublicModule::getInstance()->renderReCaptchaHolderHtmlCode();}, PHP_INT_MAX);
		}

		$this->addFilterHook('preprocess_comment', array($this, 'validateProductReviewRequest'), 1);

	}

	public function validateProductReviewRequest($arrComment)
	{
		WordPressPublicModule::getInstance()->removeHookByIndex(WordPressPublicModule::getInstance()->getCommentValidationHookIndex());

		if(is_admin() && current_user_can( 'moderate_comments' ))
			return $arrComment;


		$arrComment['comment_post_ID'] = (!empty($arrComment['comment_post_ID']) && is_numeric($arrComment['comment_post_ID'])) ? (int)$arrComment['comment_post_ID'] : 0;

		if(empty($arrComment['comment_post_ID']) || empty($_POST['rating']) || absint($_POST['rating']) < 0 || absint($_POST['rating']) > 5 || 'product' !== strtolower(get_post_type($arrComment['comment_post_ID'])) )
		{
			return $arrComment; // not WooCommerce product review
		}

		$arrWordPressCommentsType = array('pingback' => 1, 'trackback' => 1);

		if( (!empty($arrComment['comment_type']) && isset($arrWordPressCommentsType[strtolower($arrComment['comment_type'])]) ) ) {
			wp_die( '<p>' . __( 'Link Notifications are disabled!', 'invisible-recaptcha' ) . '</p>', __( 'Comment Submission Failure' ), array( 'response' => 200 ) );
		}

		if(RequestHandler::isInvisibleReCaptchaTokenValid())
			return $arrComment;

		$postPermaLink = get_permalink($arrComment['comment_post_ID']);

		empty($postPermaLink) ? wp_safe_redirect(home_url('/')) : wp_safe_redirect($postPermaLink);

		exit;
	}


}