<?php
/*
 * @author Kamil Koterba
 * Helper functions for extension hook
 */

class EmailTemplatesHooksHelper {

	/**
	 * Default with of user avatar attached to email
	 */
	const EMAIL_AVATAR_DEFAULT_WIDTH = 48;

	/**
	 * on ComposeCommonSubjectMail hook
	 * modify subject of an email
	 * @param Title $title
	 * @param Array $keys
	 * @param String $subject
	 * @param User $editor
	 * @return bool
	 */
	public static function onComposeCommonSubjectMail( Title $title, &$keys, &$subject, $editor ) {
		wfProfileIn( __METHOD__ );

		$oEthHelper = new self();
		if ( array_key_exists( '$ACTION', $keys) ) {
			$action = $keys['$ACTION'];

			/* modify bodyHTML for blogpost action */
			if ( $action == 'blogpost' ) {
				$result = $oEthHelper->subjectBlogpost( $title, $keys, $subject, $editor );
				wfProfileOut( __METHOD__ );
				return $result;
			}
		}

		/* Default subject may be put here */

		wfProfileOut( __METHOD__ );
		return true;
	}


	public static function onComposeCommonBodyMail(Title $title, &$keys, &$body, User $editor, &$bodyHTML, &$postTransformKeys ) {
		global $wgEnableWikiaFollowedPages, $wgEnableWikiaFollowedPagesOnlyPrefs;
		wfProfileIn( __METHOD__ );

		$oEthHelper = new self();
		if ( array_key_exists( '$ACTION', $keys) ) {
			$action = $keys['$ACTION'];

			/*
			 * modify bodyHTML for blogpost action
			 * check if Follow Ext is enabled, otherwise parameter $keys['$PAGETITLE'] that we need hasn't been initialized on MailNotifyBuildKeys hook
			*/
			if ( $action == 'blogpost'
				&& ( !empty( $wgEnableWikiaFollowedPages ) || !empty( $wgEnableWikiaFollowedPagesOnlyPrefs ) )
			) {

				$result = $oEthHelper->bodyBlogpost( $title, $keys, $body, $editor, $bodyHTML, $postTransformKeys, $action );
				wfProfileOut( __METHOD__ );
				return $result;

			}
		}

		/* Default template may be put here */

		wfProfileOut( __METHOD__ );
		return true;
	}


	public function __construct() {
		$this->app = F::app();
	}


	/**
	 * Modify subjest of email
	 * This method is helper for onComposeCommonSubjectMail hook function
	 *
	 * @param Title $title
	 * @param array $keys
	 * @param string $subject
	 * @param User $editor
	 */
	public function subjectBlogpost( Title $title, &$keys, &$subject, $editor ) {
		wfProfileIn( __METHOD__ );
		$oPostTitle = Title::newFromText($keys['$PAGETITLE'], NS_BLOG_ARTICLE );
		//replace $PAGETITLE with pure post title (by default it would be replaced by post title with namespace)
		$subject = str_replace( '$PAGETITLE', $oPostTitle->getSubpageText(), $subject );
		wfProfileOut( __METHOD__ );
		return true;
	}


	/**
	 * Modify HTML body of email
	 * This method is helper for onComposeCommonBodyMail hook function
	 *
	 * @param Title $title
	 * @param array $keys
	 * @param string $body
	 * @param User $editor
	 * @param string $bodyHTML
	 * @param array $postTransformKeys
	 * @param string $action
	 * @return bool
	 */
	public function bodyBlogpost( Title $title, &$keys, &$body, User $editor, &$bodyHTML, &$postTransformKeys, $action = 'blogpost' ) {
		/* @var $wgLang Language */
		global $wgLang;
		wfProfileIn( __METHOD__ );

		$msgContentHTML = wfMsgHTMLwithLanguageAndAlternative(
			'enotif_body' . ( $action == '' ? '' : ( '_' . $action ) ),
			'enotif_body',
			$this->app->wg->LanguageCode
		);
		$content = $msgContentHTML[1];

		$username = $editor->getName();
		/*
		 * $title variable has wrong value. Use $keys['$PAGETITLE'] to get title of the post ($oPostTitle).
		 * $keys['$PAGETITLE'] is being generated by MailNotifyBuildKeys hook
		 */
		$oPostTitle = Title::newFromText($keys['$PAGETITLE'], NS_BLOG_ARTICLE );
		$oRevision = Revision::newFromId($oPostTitle->getLatestRevID());
		$timestamp = time();
		if ( $oRevision instanceof Revision ) {
			$timestamp = $oRevision->getTimestamp();
		}
		$date = $wgLang->date(wfTimestamp(TS_MW, $timestamp));
		/* render blog post info (avatar, author, date, title and short text) */
		$post_info_params = array(
			'language' => $this->app->wg->LanguageCode,
			'post_url' => $oPostTitle->getFullURL(),
			'post_title' => $oPostTitle->getSubpageText(),
			'username' => $username,
			'user_page_url' => $editor->getUserPage()->getFullURL(),
			'avatar_url' => AvatarService::renderAvatar($username, self::EMAIL_AVATAR_DEFAULT_WIDTH),
			'date' => $date,
		);
		$postInfoHTML = $this->app->renderView( "EmailTemplates", "PostInfo", $post_info_params );
		$keys['$POST_INFO'] = $postInfoHTML;

		/* render button HTML to be replaced in body of mail */
		$button_params = array(
			'language' => $this->app->wg->LanguageCode,
			'link_url' => $oPostTitle->getFullURL(),
			'link_text' => wfMessage( 'read-the-whole-post' )->plain()
		);
		$buttonHTML= $this->app->renderView( "EmailTemplates", "Button", $button_params );
		$keys['$BUTTON'] = $buttonHTML;

		$keys['$TITLE_STYLE'] = ' style="color:#2c85d5;font-size:17px;font-weight:bold;"';

		/* render body of mail */
		$body_params = array(
			'language' => $this->app->wg->LanguageCode,
			'content' => $content,
		);
		$bodyHTML = $this->app->renderView( "EmailTemplates", "NewBlogPostMail", $body_params );
		wfProfileOut( __METHOD__ );
		return true;
	}

}
