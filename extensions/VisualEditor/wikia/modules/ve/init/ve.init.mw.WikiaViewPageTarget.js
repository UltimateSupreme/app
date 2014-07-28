/*!
 * VisualEditor MediaWiki Initialization WikiaViewPageTarget class.
 *
 * @copyright 2011-2013 VisualEditor Team and others; see AUTHORS.txt
 * @license The MIT License (MIT); see LICENSE.txt
 */

/*global mw, veTrack */

/**
 * Initialization MediaWiki view page target.
 *
 * @class
 * @extends ve.init.mw.ViewPageTarget
 *
 * @constructor
 */
ve.init.mw.WikiaViewPageTarget = function VeInitMwWikiaViewPageTarget() {
	// Parent constructor
	ve.init.mw.WikiaViewPageTarget.super.call( this );

	// Properties
	this.toolbarSaveButtonEnableTracked = false;
};

/* Inheritance */

OO.inheritClass( ve.init.mw.WikiaViewPageTarget, ve.init.mw.ViewPageTarget );

/* Static Properties */

ve.init.mw.WikiaViewPageTarget.static.toolbarGroups = [
	// History
	{ 'include': [ 'undo' ] },
	// Format
	{
		'type': 'menu',
		'indicator': 'down',
		'title': OO.ui.deferMsg( 'visualeditor-toolbar-format-tooltip' ),
		'include': [ { 'group': 'format' } ],
		'promote': [ 'paragraph' ],
		'demote': [ 'preformatted' ],
		'exclude': [ 'heading1' ]
	},
	// Style
	{ 'include': [ 'bold', 'italic', 'link' ] },
	{
		'type': 'list',
		'icon': 'text-style',
		'indicator': 'down',
		'title': OO.ui.deferMsg( 'visualeditor-toolbar-style-tooltip' ),
		'include': [ 'subscript', 'superscript', 'strikethrough', 'underline', 'indent', 'outdent', 'clear' ]
	},
	// Insert
	{
		'type': 'list',
		'label': OO.ui.deferMsg( 'visualeditor-toolbar-insert' ),
		'indicator': 'down',
		'include': [ 'wikiaMediaInsert', 'wikiaMapInsert', 'number', 'bullet', 'transclusion', 'reference', 'referenceList' ]
	}
];

ve.init.mw.WikiaViewPageTarget.static.actionsToolbarConfig = [
	{
		'include': [ 'notices' ]
	},
	{
		'type': 'list',
		'icon': 'menu',
		'indicator': 'down',
		'include': [ 'wikiaMeta', 'categories', 'wikiaHelp', 'wikiaCommandHelp', 'wikiaSourceMode' ]
	}
];

ve.init.mw.WikiaViewPageTarget.prototype.getNonEditableUIElements = function () {
	var $elements,
		ns = mw.config.get( 'wgNamespaceNumber' );

	if ( ns === 14 ) {
		// Category
		$elements = $( '#mw-content-text' ).children().filter( function () {
			var $this = $( this );
			return !(
				// Category thumbs
				$this.hasClass( 'category-gallery' ) ||
				// Category exhibition
				$this.is( '#mw-pages' ) ||
				// Category list
				$this.children( '#mw-pages' ).length
			);
		} );
	} else {
		$elements = $( '#mw-content-text' );
	}

	$elements = $elements.add( '.WikiaArticleCategories' );

	return $elements;
};

ve.init.mw.WikiaViewPageTarget.prototype.hidePageContent = function () {
	this.getNonEditableUIElements()
		.addClass( 've-init-mw-viewPageTarget-content' )
		.hide();

	$( 'body' ).addClass( 've' );
};

ve.init.mw.WikiaViewPageTarget.prototype.mutePageContent = function () {
	// Intentionally left empty
};

ve.init.mw.WikiaViewPageTarget.prototype.onSaveDialogReview = function () {
	ve.init.mw.ViewPageTarget.prototype.onSaveDialogReview.call( this );
	ve.track( 'wikia', {
		'action': ve.track.actions.CLICK,
		'label': 'dialog-save-review-changes-button',
		'duration': this.events.timings.saveReview - this.events.timings.saveWorkflowBegin
	} );
};

ve.init.mw.WikiaViewPageTarget.prototype.onToolbarCancelButtonClick = function () {
	if ( window.veTrack ) {
		veTrack( {
			action: 've-cancel-button-click',
			isDirty: !this.toolbarSaveButton.isDisabled() ? 'yes' : 'no'
		} );
	}
	ve.track( 'wikia', {
		'action': ve.track.actions.CLICK,
		'label': 'button-cancel',
		'duration': ve.now() - this.events.timings.surfaceReady
	} );
	mw.hook( 've.cancelButton' ).fire();
	/*
	// Trigger Qualaroo survey for anonymous users abandoning edit
	if ( mw.user.anonymous() && window._kiq ) {
		_kiq.push( ['set', { 'event': 'abandon_ve_cancel' } ] );
	}
	*/
	ve.init.mw.ViewPageTarget.prototype.onToolbarCancelButtonClick.call( this );
};

ve.init.mw.WikiaViewPageTarget.prototype.onToolbarMetaButtonClick = function () {
	ve.track( 'wikia', { 'action': ve.track.actions.CLICK, 'label': 'tool-page-settings' } );
	ve.init.mw.ViewPageTarget.prototype.onToolbarMetaButtonClick.call( this );
};

ve.init.mw.WikiaViewPageTarget.prototype.onToolbarSaveButtonClick = function () {
	if ( window.veTrack ) {
		veTrack( { action: 've-save-button-click' } );
	}

	if ( window.veOrientationEnabled !== undefined || window.veFocusMode !== undefined ) {
		window.optimizely = window.optimizely || [];
		window.optimizely.push( ['trackEvent', 've-save-button-click'] );
	}

	ve.track( 'wikia', {
		'action': ve.track.actions.CLICK,
		'label': 'button-publish',
		'duration': ve.now() - this.events.timings.surfaceReady
	} );
	ve.init.mw.ViewPageTarget.prototype.onToolbarSaveButtonClick.call( this );
};

ve.init.mw.WikiaViewPageTarget.prototype.setupSkinTabs = function () {
	// Intentionally left empty
};

ve.init.mw.WikiaViewPageTarget.prototype.showPageContent = function () {
	$( '.ve-init-mw-viewPageTarget-content' )
		.removeClass( 've-init-mw-viewPageTarget-content' )
		.show()
		.fadeTo( 0, 1 );
	$( 'body' ).removeClass( 've' );
};

ve.init.mw.WikiaViewPageTarget.prototype.updateToolbarSaveButtonState = function () {
	ve.init.mw.ViewPageTarget.prototype.updateToolbarSaveButtonState.call( this );
	if (
		!this.toolbarSaveButtonEnableTracked &&
		( this.toolbarSaveButtonEnableTracked = !this.toolbarSaveButton.isDisabled() )
	) {
		if ( window.veOrientationEnabled !== undefined ) {
			window.optimizely = window.optimizely || [];
			window.optimizely.push( ['trackEvent', 've-save-button-enable'] );
		}
		ve.track( 'wikia', {
			'action': ve.track.actions.ENABLE,
			'label': 'button-publish',
			'duration': ve.now() - this.events.timings.surfaceReady
		} );
	}
};

ve.init.mw.WikiaViewPageTarget.prototype.getToolbar = function () {
	return this.toolbar;
};

/**
 * @inheritdoc
 */
ve.init.mw.WikiaViewPageTarget.prototype.hideSpinner = function () {
	var $spinner = $( '.ve-spinner[data-type="loading"]' );
	if ( $spinner.is( ':visible' ) ) {
		$spinner.fadeOut( 400 );
	}
};

/**
 * @inheritdoc
 */
ve.init.mw.WikiaViewPageTarget.prototype.onLoadError = function ( jqXHR, status, error ) {
	ve.init.mw.ViewPageTarget.prototype.onLoadError.call( this, jqXHR, status, error );
	if ( window.veTrack ) {
		veTrack( {
			action: 've-load-error',
			status: status
		} );
	}
};

/**
 * @inheritdoc
 */
ve.init.mw.WikiaViewPageTarget.prototype.maybeShowDialogs = function () {
	var uri = new mw.Uri( location.href );
	// Parent method
	ve.init.mw.ViewPageTarget.prototype.maybeShowDialogs.call( this );

	if ( mw.user.anonymous() && !uri.query.redlink ) {
		window.optimizely = window.optimizely || [];
		window.optimizely.push( ['activate', 1248850316] );

		if ( window.veOrientationEnabled && !window.localStorage.getItem( 'WikiaVEOrientationViewed' ) ) {
			this.surface.getDialogs().getWindow( 'wikiaOrientation' ).open();
			window.localStorage.setItem( 'WikiaVEOrientationViewed', true );
		}
	}
};

/**
 * @inheritdoc
 */
ve.init.mw.WikiaViewPageTarget.prototype.replacePageContent = function ( html, categoriesHtml ) {
	var insertTarget,
		$mwContentText = $( '#mw-content-text' ),
		$content = $( $.parseHTML( html ) );

	if ( mw.config.get( 'wgNamespaceNumber' ) === 14 /* NS_CATEOGRY */ ) {
		$mwContentText.children().filter( function () {
			var $this = $( this );
			return !(
				// Category form
				$this.hasClass( 'category-gallery-form' ) ||
				// Category thumbs
				$this.hasClass( 'category-gallery' ) ||
				// Category exhibition
				$this.is( '#mw-pages' ) ||
				// Category list
				$this.children( '#mw-pages' ).length
			);
		} ).remove();

		insertTarget = window.CategoryExhibition ? '#mw-pages' : '.category-gallery';
		$content.insertBefore( insertTarget );
	} else {
		$mwContentText.empty().append( $content );
	}

	mw.hook( 'wikipage.content' ).fire( $mwContentText );
	$( '#catlinks' ).replaceWith( categoriesHtml );
};
