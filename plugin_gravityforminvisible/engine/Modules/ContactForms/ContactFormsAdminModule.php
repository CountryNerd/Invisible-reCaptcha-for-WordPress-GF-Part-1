<?php


namespace InvisibleReCaptcha\Modules\ContactForms;


use InvisibleReCaptcha\MchLib\Utils\MchHtmlUtils;
use InvisibleReCaptcha\Modules\BaseAdminModule;

class ContactFormsAdminModule extends BaseAdminModule
{
	CONST OPTION_CF7_PROTECTION_ENABLED = 'CF7';

	public function getDefaultOptions()
	{
		static $arrDefaultSettingOptions = null;
		if(null !== $arrDefaultSettingOptions)
			return $arrDefaultSettingOptions;

		$arrDefaultSettingOptions = array(

			self::OPTION_CF7_PROTECTION_ENABLED  => array(
				'Value'      => null,
				'LabelText'  => __('Enable Protection for Contact Form 7', 'invisible-recaptcha'),
				'InputType'  => MchHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

		);

		return $arrDefaultSettingOptions;

	}

	public function validateModuleSettingsFields($arrOptions)
	{
		$arrOptions = $this->sanitizeModuleSettings($arrOptions);
		return $arrOptions;
	}


	public function renderModuleSettingsSectionHeader( array $arrSectionInfo ) {
		echo '<div class="mch-settings-section-header">
				<h3>'.__('Contact Forms Protection Settings', 'invisible-recaptcha').'</h3>
			</div>';
	}

}