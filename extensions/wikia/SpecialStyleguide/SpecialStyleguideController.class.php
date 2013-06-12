<?php

class SpecialStyleguideController extends WikiaSpecialPageController {

	public function __construct() {
		parent::__construct( 'Styleguide' );
	}

	public function index() {
		wfProfileIn( __METHOD__ );

		RenderContentOnlyHelper::setRenderContentVar( true );
		RenderContentOnlyHelper::setRenderContentLevel( RenderContentOnlyHelper::LEAVE_NAV_ONLY );
		$this->response->addAsset( 'extensions/wikia/SpecialStyleguide/css/SpecialStyleguide.scss' );
		$this->wg->Out->setPageTitle( wfMessage( 'styleguide-pagetitle' )->plain() );

		$this->app->setGlobal( 'wgAutoloadClasses', dirname( __FILE__ ) . '/SpecialStyleguideGlobalHeaderControllerOverride.php', 'GlobalHeaderController' );

		$this->wg->Out->clearHTML();
		$this->wg->Out->addHtml(
			(new Wikia\Template\MustacheEngine)
				->setPrefix( dirname(__FILE__) . '/templates' )
				->setData( [
					'body' => $this->getPageContent('home')
				])
				->render('SpecialStyleguide_index.mustache')
		);

		wfProfileOut( __METHOD__ );

		// skip rendering
		$this->skipRendering();
	}

	public function getPageContent($pagename) {
		return (new Wikia\Template\MustacheEngine)
			->setPrefix( dirname(__FILE__) . '/templates' )
			->render('SpecialStyleguide_' . $pagename . '.mustache');
	}
}
