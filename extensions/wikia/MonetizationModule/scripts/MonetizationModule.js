/**
 * JS file for Monetization Module.
 */

require(['wikia.tracker', 'wikia.geo'], function (Tracker, geo) {
	'use strict';

	var track;

	track = Tracker.buildTrackingFunction({
		trackingMethod: 'internal',
		action: Tracker.ACTIONS.CLICK,
		geo: geo.getCountryCode()
	});

	var MonetizationModule = {
		init: function () {
			// track impression for each placement
			$('.monetization-module').each(function () {
				var $this = $(this),
					trackCategory = $this.attr('id'),
					value = $this.children().children().length,	// check if the ad is blocked
					type = $this.attr('data-mon-type'),
					slot = $this.attr('data-mon-slot');

				track({
					category: trackCategory,
					label: 'module-impression',
					action: Tracker.ACTIONS.IMPRESSION,
					value: value,
					type: type,
					slot: slot
				});
			});

			this.initEllipses();
			this.initClickTrackingEcommerce();
		},
		initEllipses: function () {
			$(window)
				.on('resize.monetizationmodule', function () {
					$('.monetization-module').find('.placard a').ellipses({
						maxLines: 3
					});
				})
				.trigger('resize.monetizationmodule');
		},
		initClickTrackingEcommerce: function () {
			var elements = [
				'.product-thumb',
				'.product-name',
				'.product-price'
			];

			$('div[data-mon-type=ecommerce]').on('click', elements.join(', '), function () {
				var $this = $(this),
					$module = $this.closest('.monetization-module'),
					$products = $module.find('.affiliate'),
					$product = $this.parent(),
					trackCategory = $module.attr('id'),
					trackLabel = $this.attr('class'),
					trackValue = $products.index($product),
					type = $module.attr('data-mon-type'),
					slot = $module.attr('data-mon-slot'),
					productName = $product.attr('data-mon-pname'),
					productCode = $product.attr('data-mon-pcode'),
					productUrl = $this.find('a').attr('href');

				track({
					category: trackCategory,
					label: trackLabel,
					value: trackValue,
					type: type,
					slot: slot,
					title: productName,
					pcode: productCode,
					url: productUrl
				});
			});
		}
	};

	MonetizationModule.init();
});
