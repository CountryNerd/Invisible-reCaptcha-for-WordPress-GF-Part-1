<?php


namespace InvisibleReCaptcha\Modules\ContactForms;


use InvisibleReCaptcha\MchLib\Utils\MchWpUtils;
use InvisibleReCaptcha\Modules\BasePublicModule;
use InvisibleReCaptcha\RequestHandler;

class ContactFormsPublicModule extends BasePublicModule
{
	public function __construct()
	{
		parent::__construct();

		if($this->getOption(ContactFormsAdminModule::OPTION_CF7_PROTECTION_ENABLED)){

			MchWpUtils::addFilterHook('wpcf7_form_elements', function($outputHtml = null){
				return $outputHtml . ContactFormsPublicModule::getInstance()->getReCaptchaHolderHtmlCode();
			}, PHP_INT_MAX);

			MchWpUtils::addFilterHook('wpcf7_spam', function(){
				return !RequestHandler::isInvisibleReCaptchaTokenValid();
			}, 9);
		}

	}




}