<?php
/**
 * @package MediaWiki
 * @subpackage SpecialPage
 * @author Piotr Molski <moli@wikia-inc.com> for Wikia.com
 * @copyright (C) 2008, Wikia Inc.
 * @licence GNU General Public Licence 2.0 or later
 */
if ( !defined( 'MEDIAWIKI' ) ) {
    echo "This is MediaWiki extension named MultiWikiDelete.\n";
    exit( 1 ) ;
}

require_once ( dirname(__FILE__) . '/MultiTasksCore.php' );

$wgExtensionCredits['specialpage'][] = array(
    "name" => "Multi Wiki Delete",
    "description" => "Special Multi Wiki Delete",
    "author" => "Bartek Łapiński, Piotr Molski"
);

$wgExtensionMessagesFiles["Multidelete"] = dirname(__FILE__) . '/MultiTasks.i18n.php';
$wgExtensionMessagesFiles['MultideleteAliases'] = __DIR__ . '/MultiTasks.aliases.php';

$wgAvailableRights[] = 'multidelete';
$wgGroupPermissions['staff']['multidelete'] = true;
$wgGroupPermissions['helper']['multidelete'] = true;
$wgGroupPermissions['vstf']['multidelete'] = true;

extAddSpecialPage( dirname(__FILE__) . '/SpecialMultiDelete_body.php', 'Multidelete', 'Multidelete' );

require_once( $IP . "/extensions/wikia/TaskManager/BatchTask.php" );

$wgSpecialPageGroups['Multidelete'] = 'pagetools';
