<?php

namespace InvisibleReCaptcha\Modules\GravityForms;

use InvisibleReCaptcha\MchLib\Utils\MchWpUtils;
use InvisibleReCaptcha\Modules\BasePublicModule;
use InvisibleReCaptcha\RequestHandler;


class GravityFormsPublicModule extends BasePublicModule
{
        public function __construct()
        {
                parent::__construct();
		session_start();
		$isLastPage = FALSE;

                if($this->getOption(GravityFormsAdminModule::OPTION_GF_PROTECTION_ENABLED)){
			$this->addActionHook('gform_post_paging', array($this, 'getLastPage'), 10, 3 );
                        $this->addFilterHook('gform_submit_button', array($this, 'addCaptcha'), PHP_INT_MAX, 2);
			$this->addFilterHook('gform_validation', array($this, 'validateForm'), PHP_INT_MAX);
                }

        }

	public function getLastPage( $form,$source_page_number, $current_page_number ) {
	
		$this->isMultiPage = True;
		if ( $current_page_number >= (sizeOf($form["pagination"]['pages']) + 1)) {
        		$this->isLastPage = True;
    		}
	}
	
        public function addCaptcha($button, $form) {
		if(sizeOf($form["pagination"]['pages']) > 0) {
                	if($form['ewuaddonirecaptcha']['enabled'] == 1 && $_SESSION["verifiedByInvisibleRecaptcha"] == 0 && $this->isLastPage) {
				$_SESSION["verifiedByInvisibleRecaptcha"] = 1;
                		return $button .  GravityFormsPublicModule::getInstance()->getReCaptchaHolderHtmlCode();
                	} 
			else {
                		return $button;
			}
		} else {
			return $button .  GravityFormsPublicModule::getInstance()->getReCaptchaHolderHtmlCode();
		}
        }

	public function validateForm($validation_result) {

		$form = $validation_result['form'];
                if($form['ewuaddonirecaptcha']['enabled'] == 1 and $this->isLastPage) {
                	if(RequestHandler::isInvisibleReCaptchaTokenValid()) {
                        	$validation_result['form'] = $form;
				return $validation_result;
                	}

                	return new WP_Error(\InvisibleReCaptcha::PLUGIN_SLUG, __('Your entry appears to be spam!', 'invisible-recaptcha'));
			
		}else {
			return $validation_result;
		}
	}
}
