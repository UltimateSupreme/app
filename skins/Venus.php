<?php
if (!defined('MEDIAWIKI')) die();
/**
 * Venus Skin
 *
 * @author Consumer Team
 */

require_once('includes/SkinTemplate.php');

class SkinVenus extends WikiaSkin {
	function __construct() {
		global $wgOut;
		parent::__construct( 'VenusTemplate', 'venus' );

		$wgOut->addModuleStyles( 'skins.venus' );

		//non-strict checks of css/js/scss assets/packages
		$this->strictAssetUrlCheck = false;
	}
}


class VenusTemplate extends WikiaSkinTemplate {
	function execute() {
		F::app()->setSkinTemplateObj($this);
		$response = $this->app->sendRequest( 'Venus', 'index' );
		$response->sendHeaders();
		$response->render();
	}
}
?>
