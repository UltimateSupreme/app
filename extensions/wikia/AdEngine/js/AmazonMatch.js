/*global define*/
/*jshint camelcase:false*/
define('ext.wikia.adEngine.amazonMatch', [
	'ext.wikia.adEngine.adTracker',
	'wikia.document',
	'wikia.log',
	'wikia.window'
], function (adTracker, doc, log, win) {
	'use strict';

	var logGroup = 'ext.wikia.adEngine.amazonMatch',
		amazonId = '3115',
		amazonResponse,
		amazonTiming,
		amazonCalled = false;

	function trackState(trackEnd) {
		log(['trackState', amazonResponse], 'debug', logGroup);

		var eventName,
			i, j,
			data,
			matches;

		if (amazonResponse) {
			eventName = 'lookupSuccess';
			matches = amazonResponse.replace('amzn_', '').match(/[\dx]+_tier\d/g);
			if (matches) {
				data = {};

				for(i = matches.length - 1; i > -1; i -= 1) {
					j = matches[i].split('_');
					data[j[1]] = data[j[1]] || [];
					data[j[1]].push(j[0]);
				}
			}
		} else {
			eventName = 'lookupError';
		}

		if (trackEnd) {
			eventName = 'lookupEnd';
		}

		adTracker.track(eventName + '/amazon', data || '(unknown)', 0);
	}

	function onAmazonResponse(response) {
		amazonTiming.measureDiff({}, 'end').track();
		log(['onAmazonResponse', response], 'debug', logGroup);

		if (response.status === 'ok') {
			amazonResponse = response.ads;
		}

		trackState(true);
	}

	function call() {
		log('call', 'debug', logGroup);

		amazonCalled = true;
		amazonTiming = adTracker.measureTime('amazon', {}, 'start');
		amazonTiming.track();

		win.amznads = { updateAds: onAmazonResponse };

		var url = encodeURIComponent(doc.location),
			s = doc.createElement('script');

		try { url = encodeURIComponent(win.top.location.href); } catch(e) {}

		s.id = logGroup;
		s.async = true;
		s.src = '//aax.amazon-adsystem.com/e/dtb/bid?src=' + amazonId + '&u=' + url + "&cb=" + Math.round(Math.random()*10000000);
		doc.body.appendChild(s);

	}

	function wasCalled() {
		log(['wasCalled', amazonCalled], 'debug', logGroup);
		return amazonCalled;
	}

	return {
		call: call,
		trackState: function () { trackState(); },
		wasCalled: wasCalled
	};
});
