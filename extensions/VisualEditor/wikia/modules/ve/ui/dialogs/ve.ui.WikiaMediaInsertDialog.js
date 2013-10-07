/*!
 * VisualEditor user interface WikiaMediaInsertDialog class.
 */

/**
 * Dialog for inserting MediaWiki media objects.
 *
 * @class
 * @extends ve.ui.MWDialog
 *
 * @constructor
 * @param {ve.ui.Surface} surface
 * @param {Object} [config] Config options
 */
ve.ui.WikiaMediaInsertDialog = function VeUiMWMediaInsertDialog( surface, config ) {
	// Parent constructor
	ve.ui.MWDialog.call( this, surface, config );
};

/* Inheritance */

ve.inheritClass( ve.ui.WikiaMediaInsertDialog, ve.ui.MWDialog );

/* Static Properties */

ve.ui.WikiaMediaInsertDialog.static.name = 'wikiaMediaInsert';

ve.ui.WikiaMediaInsertDialog.static.icon = 'media';

/* Methods */

ve.ui.WikiaMediaInsertDialog.prototype.initialize = function () {
	// Parent method
	ve.ui.MWDialog.prototype.initialize.call( this );

	// Properties
	this.cartModel = new ve.dm.WikiaCart()
	this.cart = new ve.ui.WikiaCartWidget( this.cartModel );
	this.$cart = this.$$( '<div>' );
	this.search = new ve.ui.MWMediaSearchWidget( {
		'$$': this.frame.$$,
		'size': 130
	} );
	this.contentPanel = new ve.ui.PagedLayout( { '$$': this.frame.$$, 'attachPagesPanel': true } );

	// Events
	this.search.connect( this, { 'select': 'onSearchSelect' } );

	// Initialization
	this.contentPanel.addPage( 'search', { '$content': this.search.$ } );
	this.$cart
		.addClass( 've-ui-wikiaCartWidget-wrapper' )
		.append( this.cart.$ );
	this.$body.append( this.$cart, this.contentPanel.$ );
	this.frame.$content.addClass( 've-ui-wikiaMediaInsertDialog-content' );
};

ve.ui.WikiaMediaInsertDialog.prototype.onSearchSelect = function ( item ) {
	this.cartModel.addItems( [ new ve.dm.WikiaCartItem( item.title ) ] );
};

/* Registration */

ve.ui.dialogFactory.register( ve.ui.WikiaMediaInsertDialog );
