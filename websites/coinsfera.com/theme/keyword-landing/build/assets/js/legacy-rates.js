/**
 * Homepage ticker + coin-page calculators, served from the cached rate feed.
 *
 * The old custom.js still asks CryptoCompare on every keystroke via
 * getSingleCoinPrice. This file rebinds those inputs so each coin is fetched
 * once from admin-ajax (now a cache read) and reused, and it paints the
 * homepage cards from the public REST feed so they do not depend on the
 * WPCode snippet's POST shape.
 */
(function ($) {
	'use strict';

	var live = window.CFKL_LIVE || {};
	var ajaxUrl = live.ajax || window.coinsfera_ajax_url || (window.ajax_object && window.ajax_object.ajaxurl) || '';
	var ratesUrl = live.rates || '/wp-json/cfkl/v1/rates';
	var cache = {};
	var pending = {};
	var TTL = 60000;

	window.coinsfera_ajax_url = window.coinsfera_ajax_url || ajaxUrl;

	function fmtUsd(n) {
		n = Number(n);
		if (!isFinite(n) || n <= 0) {
			return '';
		}
		if (n >= 1000) {
			return '$' + Math.round(n).toLocaleString('en-US');
		}
		if (n >= 1) {
			return '$' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
		}
		return '$' + n.toLocaleString('en-US', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
	}

	function paintTicker(coins) {
		if (!coins) {
			return;
		}
		Object.keys(coins).forEach(function (sym) {
			var row = coins[sym];
			var usd = row && row.usd;
			if (!usd) {
				return;
			}
			var change = typeof row.change === 'number' ? row.change : 0;
			var changeLabel = (change > 0 ? '+' : '') + change.toFixed(2) + '%';
			var klass = change < 0 ? 'low-rate' : 'high-rate';
			$('#PRICE_' + sym + ', #price_' + sym).text(fmtUsd(usd));
			$('#CHANGE_' + sym + ', #change_' + sym)
				.text(changeLabel)
				.removeClass('low-rate high-rate')
				.addClass(klass);
		});
	}

	function loadBoard() {
		if (!$('.curr-rate, .cashpoint-rate').length) {
			return;
		}
		$.getJSON(ratesUrl).done(function (data) {
			paintTicker(data && data.coins);
		});
	}

	function priceOf(sym, cb) {
		var now = Date.now();
		if (cache[sym] && now - cache[sym].t < TTL) {
			cb(cache[sym].p);
			return;
		}
		if (pending[sym]) {
			pending[sym].push(cb);
			return;
		}
		pending[sym] = [cb];
		$.ajax({
			url: ajaxUrl,
			type: 'GET',
			dataType: 'json',
			data: { action: 'getSingleCoinPrice', fsym: sym, tsyms: 'USD' }
		}).done(function (response) {
			var price = response && parseFloat(response.USD);
			if (isFinite(price) && price > 0) {
				cache[sym] = { p: price, t: Date.now() };
			}
		}).always(function () {
			var list = pending[sym] || [];
			delete pending[sym];
			var price = cache[sym] && cache[sym].p;
			list.forEach(function (fn) {
				if (price) {
					fn(price);
				}
			});
		});
	}

	function bindCalculator() {
		if (!ajaxUrl || !$('.calc-box').length) {
			return;
		}

		$(document).off('change input', '.price-value');
		$(document).off('change input', '.coin-value');

		$(document).on('change input', '.price-value', function () {
			var $input = $(this);
			var box = $input.closest('.calc-box');
			var mode = box.data('calc-mode') || 'buy';
			if (mode === 'sell' && $input.is(':focus') === false) {
				return;
			}
			var fiat = parseFloat($input.val());
			var coin = $input.data('coin');
			if (!isFinite(fiat) || fiat <= 0) {
				box.find('.coin-value').val(0);
				return;
			}
			priceOf(coin, function (actual) {
				box.find('.coin-value').val((fiat / actual).toFixed(6));
			});
		});

		$(document).on('change input', '.coin-value', function () {
			var $input = $(this);
			var box = $input.closest('.calc-box');
			var mode = box.data('calc-mode') || 'buy';
			if (mode === 'buy') {
				return;
			}
			var crypto = parseFloat($input.val());
			var coin = $input.data('coin') || box.find('.price-value').data('coin');
			if (!isFinite(crypto) || crypto <= 0) {
				box.find('.price-value').val(0);
				return;
			}
			priceOf(coin, function (actual) {
				box.find('.price-value').val((crypto * actual).toFixed(2));
			});
		});

		$('.price-value[data-coin]').each(function () {
			var coin = $(this).data('coin');
			if (coin) {
				priceOf(coin, function () {});
			}
		});
	}

	$(function () {
		loadBoard();
		bindCalculator();
	});

	$(window).on('load', bindCalculator);
})(jQuery);
