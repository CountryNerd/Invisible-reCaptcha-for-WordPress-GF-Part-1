<?php


namespace InvisibleReCaptcha\Modules\GravityForms;


use InvisibleReCaptcha\MchLib\Utils\MchHtmlUtils;
use InvisibleReCaptcha\Modules\BaseAdminModule;

class GravityFormsAdminModule extends BaseAdminModule
{
	CONST OPTION_GF_PROTECTION_ENABLED = 'GF';

	public function getDefaultOptions()
	{
		static $arrDefaultSettingOptions = null;
		if(null !== $arrDefaultSettingOptions)
			return $arrDefaultSettingOptions;

		$arrDefaultSettingOptions = array(

			self::OPTION_GF_PROTECTION_ENABLED  => array(
				'Value'      => null,
				'LabelText'  => __('Enable Protection for Gravity Forms', 'invisible-recaptcha'),
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
				<h3>'.__('Gravity Forms Protection Settings', 'invisible-recaptcha').'</h3>
			</div>';
	}

}
