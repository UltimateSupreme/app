<?php
/**
 * WikiaHomePage
 *
 * @author Andrzej 'nAndy' Łukaszewski
 * @author Hyun Lim
 * @author Marcin Maciejewski
 * @author Saipetch Kongkatong
 * @author Sebastian Marzjan
 * @author Damian Jóźwiak
 */

$dir = dirname(__FILE__) . '/';

$wgExtensionCredits['other'][] = array(
	'name'			=> 'WikiaHomePage',
	'author'		=> 'Andrzej "nAndy" Łukaszewski, Hyun Lim, Marcin Maciejewski, Saipetch Kongkatong, Sebastian Marzjan, Damian Jóźwiak',
	'description'	=> 'WikiaHomePage',
	'version'		=> 1.0
);

$wgAutoloadClasses['WikiaHomePageController'] = $dir.'WikiaHomePageController.class.php';
$wgAutoloadClasses['WikiaHubsApiController'] = $dir . '../WikiaHubsServices/api/WikiaHubsApiController.class.php';

//i18n mapping
$wgExtensionMessagesFiles['WikiaHomePage'] = $dir.'WikiaHomePage.i18n.php';
JSMessages::registerPackage('WikiaHomePage', array('wikia-home-page-*'));

// services
$wgAutoloadClasses['RedirectService'] = 'includes/wikia/services/RedirectService.class.php';

// hooks
$wgHooks['GetHTMLAfterBody'][] = 'WikiaHomePageController::onGetHTMLAfterBody';
$wgHooks['OutputPageBeforeHTML'][] = 'WikiaHomePageController::onOutputPageBeforeHTML';
$wgHooks['WikiaMobileAssetsPackages'][] = 'WikiaHomePageController::onWikiaMobileAssetsPackages';
$wgHooks['ArticleCommentCheck'][] = 'WikiaHomePageController::onArticleCommentCheck';
$wgHooks['AfterGlobalHeader'][] = 'WikiaHomePageController::onAfterGlobalHeader';
$wgHooks['GetRailModuleList'][] = 'WikiaHomePageController::onGetRailModuleList';
$wgHooks['BeforePageDisplay'][] = 'WikiaHomePageController::onBeforePageDisplay';
$wgHooks['AfterOasisSettingsInitialized'][] = 'WikiaHomePageController::onAfterOasisSettingsInitialized';
$wgHooks['ArticleFromTitle'][] = 'WikiaHomePageController::onArticleFromTitle';
