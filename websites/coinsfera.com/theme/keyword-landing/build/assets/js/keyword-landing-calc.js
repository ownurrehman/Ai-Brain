/**
 * Keyword Landing rate calculator.
 *
 * Every element is found by data attribute, never by class, so each design can
 * lay the calculator out however it likes without touching this file. A design
 * only has to include the attributes it wants; anything missing is skipped.
 *
 * Contract, on a root carrying [data-cfkl-calc]:
 *   [data-calc-mode="buy|sell"]  mode buttons or radios
 *   [data-calc-coin]             <select> or buttons carrying data-value
 *   [data-calc-currency]         <select> or buttons carrying data-value
 *   [data-calc-fiat]             fiat amount <input>
 *   [data-calc-crypto]           crypto amount <input>
 *   [data-calc-quick]            preset buttons carrying data-amount (fiat)
 *   [data-calc-out="KEY"]        read-only output, see render()
 *   [data-calc-cta]              <a> whose href gets the prefilled message
 */
( function () {
	'use strict';

	var CONFIG = window.CFKL_CALC || null;

	if ( ! CONFIG || ! CONFIG.coins ) {
		return;
	}

	var REFRESH_MS = 60000;

	/**
	 * Format a number for the visitor's language.
	 *
	 * @param {number} value    Amount.
	 * @param {number} decimals Maximum decimals.
	 * @return {string} Localised number.
	 */
	function formatNumber( value, decimals ) {
		if ( ! isFinite( value ) ) {
			return '—';
		}

		try {
			return new Intl.NumberFormat( CONFIG.locale, {
				minimumFractionDigits: value >= 1000 ? 0 : Math.min( 2, decimals ),
				maximumFractionDigits: decimals
			} ).format( value );
		} catch ( e ) {
			return value.toFixed( decimals );
		}
	}

	/**
	 * Human wording for the age of the current quote.
	 *
	 * @param {number} updated Unix seconds.
	 * @return {string} Localised age.
	 */
	function formatAge( updated ) {
		if ( ! updated ) {
			return '';
		}

		var seconds = Math.max( 0, Math.round( Date.now() / 1000 - updated ) );

		if ( seconds < 10 ) {
			return CONFIG.i18n.justNow;
		}

		if ( seconds < 90 ) {
			return CONFIG.i18n.secondsAgo.replace( '%d', seconds );
		}

		return CONFIG.i18n.minutesAgo.replace( '%d', Math.round( seconds / 60 ) );
	}

	/**
	 * One calculator instance.
	 *
	 * @param {HTMLElement} root Element carrying [data-cfkl-calc].
	 */
	function Calculator( root ) {
		this.root = root;
		this.coin = root.getAttribute( 'data-calc-default-coin' ) || 'BTC';
		this.currency = root.getAttribute( 'data-calc-default-currency' ) || 'usd';
		this.mode = root.getAttribute( 'data-calc-default-mode' ) || 'buy';
		this.driver = 'fiat';

		if ( ! CONFIG.coins[ this.coin ] ) {
			this.coin = Object.keys( CONFIG.coins )[ 0 ];
		}

		this.fiatInput = root.querySelector( '[data-calc-fiat]' );
		this.cryptoInput = root.querySelector( '[data-calc-crypto]' );
		this.cta = root.querySelector( '[data-calc-cta]' );

		this.bind();
		this.seed();
		this.render();
	}

	/**
	 * Read an output slot.
	 *
	 * @param {string} key Output key.
	 * @return {HTMLElement|null} Element.
	 */
	Calculator.prototype.out = function ( key ) {
		return this.root.querySelector( '[data-calc-out="' + key + '"]' );
	};

	/**
	 * Write text into an output slot when the design includes it.
	 *
	 * @param {string} key   Output key.
	 * @param {string} value Text.
	 */
	Calculator.prototype.write = function ( key, value ) {
		var node = this.out( key );

		if ( node ) {
			node.textContent = value;
		}
	};

	/**
	 * Wire up every control the design chose to render.
	 */
	Calculator.prototype.bind = function () {
		var self = this;

		this.root.addEventListener( 'input', function ( event ) {
			var target = event.target;

			if ( target === self.fiatInput ) {
				self.driver = 'fiat';
				self.render();
			} else if ( target === self.cryptoInput ) {
				self.driver = 'crypto';
				self.render();
			} else if ( target.hasAttribute( 'data-calc-coin' ) ) {
				self.coin = target.value;
				self.render();
			} else if ( target.hasAttribute( 'data-calc-currency' ) ) {
				self.currency = target.value;
				self.render();
			}
		} );

		this.root.addEventListener( 'click', function ( event ) {
			var button = event.target.closest(
				'[data-calc-mode], [data-calc-quick], [data-calc-coin][data-value], [data-calc-currency][data-value]'
			);

			if ( ! button || ! self.root.contains( button ) ) {
				return;
			}

			if ( button.hasAttribute( 'data-calc-quick' ) ) {
				event.preventDefault();
				self.driver = 'fiat';

				if ( self.fiatInput ) {
					self.fiatInput.value = button.getAttribute( 'data-amount' );
				}
			} else if ( button.hasAttribute( 'data-calc-mode' ) ) {
				self.mode = button.getAttribute( 'data-calc-mode' );
				self.setPressed( '[data-calc-mode]', 'data-calc-mode', self.mode );
			} else if ( button.hasAttribute( 'data-calc-coin' ) ) {
				event.preventDefault();
				self.coin = button.getAttribute( 'data-value' );
				self.setPressed( '[data-calc-coin][data-value]', 'data-value', self.coin );
			} else if ( button.hasAttribute( 'data-calc-currency' ) ) {
				event.preventDefault();
				self.currency = button.getAttribute( 'data-value' );
				self.setPressed( '[data-calc-currency][data-value]', 'data-value', self.currency );
			}

			self.render();
		} );
	};

	/**
	 * Mark the active option in a button group.
	 *
	 * @param {string} selector  Group selector.
	 * @param {string} attribute Attribute holding each option's value.
	 * @param {string} active    Selected value.
	 */
	Calculator.prototype.setPressed = function ( selector, attribute, active ) {
		var options = this.root.querySelectorAll( selector );

		Array.prototype.forEach.call( options, function ( option ) {
			var on = option.getAttribute( attribute ) === active;

			option.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
			option.classList.toggle( 'is-active', on );
		} );
	};

	/**
	 * Apply the starting selection to the rendered controls.
	 */
	Calculator.prototype.seed = function () {
		var coinSelect = this.root.querySelector( 'select[data-calc-coin]' );
		var currencySelect = this.root.querySelector( 'select[data-calc-currency]' );

		if ( coinSelect ) {
			coinSelect.value = this.coin;
		}

		if ( currencySelect ) {
			currencySelect.value = this.currency;
		}

		this.setPressed( '[data-calc-mode]', 'data-calc-mode', this.mode );
		this.setPressed( '[data-calc-coin][data-value]', 'data-value', this.coin );
		this.setPressed( '[data-calc-currency][data-value]', 'data-value', this.currency );
	};

	/**
	 * The price one coin trades at, after the desk's spread.
	 *
	 * @return {number} Effective unit rate in the chosen currency.
	 */
	Calculator.prototype.effectiveRate = function () {
		var coin = CONFIG.coins[ this.coin ];
		var market = coin ? Number( coin[ this.currency ] ) : 0;

		if ( ! market ) {
			return 0;
		}

		var spread = 'sell' === this.mode ? -CONFIG.spread.sell : CONFIG.spread.buy;

		return market * ( 1 + spread / 100 );
	};

	/**
	 * Recalculate and paint every slot the design rendered.
	 */
	Calculator.prototype.render = function () {
		var coin = CONFIG.coins[ this.coin ];

		if ( ! coin ) {
			return;
		}

		var currency = CONFIG.currencies[ this.currency ] || CONFIG.currencies.usd;
		var rate = this.effectiveRate();
		var buying = 'buy' === this.mode;

		var fiat = parseFloat( ( this.fiatInput && this.fiatInput.value ) || '0' ) || 0;
		var crypto = parseFloat( ( this.cryptoInput && this.cryptoInput.value ) || '0' ) || 0;

		if ( 'fiat' === this.driver ) {
			crypto = rate > 0 ? fiat / rate : 0;

			if ( this.cryptoInput ) {
				this.cryptoInput.value = crypto ? crypto.toFixed( coin.dp ) : '';
			}
		} else {
			fiat = crypto * rate;

			if ( this.fiatInput ) {
				this.fiatInput.value = fiat ? fiat.toFixed( 2 ) : '';
			}
		}

		this.write( 'rate', currency.symbol + formatNumber( rate, rate >= 100 ? 0 : 4 ) );
		this.write( 'rate-plain', formatNumber( rate, rate >= 100 ? 0 : 4 ) );
		this.write( 'total', currency.symbol + formatNumber( fiat, 2 ) );
		this.write( 'total-plain', formatNumber( fiat, 2 ) );
		this.write( 'crypto', formatNumber( crypto, coin.dp ) );
		this.write( 'coin', this.coin );
		this.write( 'coin-label', coin.label );
		this.write( 'currency', currency.label );
		this.write( 'currency-symbol', currency.symbol );
		this.write( 'unit', '1 ' + this.coin + ' = ' + currency.symbol + formatNumber( rate, rate >= 100 ? 0 : 4 ) );
		this.write( 'direction', buying ? CONFIG.i18n.youPay : CONFIG.i18n.youGet );
		this.write( 'spread', ( buying ? CONFIG.spread.buy : CONFIG.spread.sell ).toFixed( 1 ) + '%' );
		this.write( 'status', CONFIG.stale ? CONFIG.i18n.stale : CONFIG.i18n.live );
		this.write( 'updated', formatAge( CONFIG.updated ) );

		this.renderChange( coin );
		this.renderCta( fiat, crypto, currency );

		this.root.setAttribute( 'data-calc-state', CONFIG.stale ? 'stale' : 'live' );
		this.root.setAttribute( 'data-calc-active-mode', this.mode );
	};

	/**
	 * Paint the 24h move, hiding it when the fallback feed has no change data.
	 *
	 * @param {Object} coin Active coin record.
	 */
	Calculator.prototype.renderChange = function ( coin ) {
		var node = this.out( 'change' );

		if ( ! node ) {
			return;
		}

		if ( null === coin.change || undefined === coin.change ) {
			node.hidden = true;
			return;
		}

		var up = coin.change >= 0;

		node.hidden = false;
		node.textContent = ( up ? '+' : '' ) + coin.change.toFixed( 2 ) + '%';
		node.setAttribute( 'data-trend', up ? 'up' : 'down' );
	};

	/**
	 * Prefill the enquiry link with the exact quote on screen.
	 *
	 * @param {number} fiat     Fiat amount.
	 * @param {number} crypto   Crypto amount.
	 * @param {Object} currency Active currency record.
	 */
	Calculator.prototype.renderCta = function ( fiat, crypto, currency ) {
		if ( ! this.cta || ! CONFIG.whatsapp || ! fiat ) {
			return;
		}

		var template = this.cta.getAttribute( 'data-calc-message' ) ||
			'Hi Coinsfera, I would like to {mode} {crypto} {coin} for about {fiat} {currency}. Is the rate available today?';

		var message = template
			.replace( '{mode}', this.mode )
			.replace( '{crypto}', crypto.toFixed( CONFIG.coins[ this.coin ].dp ) )
			.replace( '{coin}', this.coin )
			.replace( '{fiat}', fiat.toFixed( 2 ) )
			.replace( '{currency}', currency.label );

		this.cta.href = 'https://wa.me/' + CONFIG.whatsapp + '?text=' + encodeURIComponent( message );
	};

	var instances = [];

	/**
	 * Pull fresh rates and repaint every calculator on the page.
	 */
	function refresh() {
		if ( ! CONFIG.endpoint || ! window.fetch || document.hidden ) {
			return;
		}

		window.fetch( CONFIG.endpoint, { credentials: 'omit' } )
			.then( function ( response ) {
				return response.ok ? response.json() : null;
			} )
			.then( function ( data ) {
				if ( ! data || ! data.coins ) {
					return;
				}

				Object.keys( data.coins ).forEach( function ( symbol ) {
					if ( ! CONFIG.coins[ symbol ] ) {
						return;
					}

					var incoming = data.coins[ symbol ];

					// Coerce explicitly: the feed may serialise a rate as a string.
					CONFIG.coins[ symbol ].usd = Number( incoming.usd );
					CONFIG.coins[ symbol ].eur = Number( incoming.eur );
					CONFIG.coins[ symbol ].try = Number( incoming.try );
					CONFIG.coins[ symbol ].change =
						null === incoming.change || undefined === incoming.change
							? null
							: Number( incoming.change );
				} );

				CONFIG.updated = data.updated;
				CONFIG.stale = data.stale;

				instances.forEach( function ( instance ) {
					instance.render();
				} );
			} )
			.catch( function () {
				// A failed refresh keeps the last good numbers on screen.
			} );
	}

	/**
	 * Boot every calculator in the document.
	 */
	function init() {
		var roots = document.querySelectorAll( '[data-cfkl-calc]' );

		Array.prototype.forEach.call( roots, function ( root ) {
			instances.push( new Calculator( root ) );
		} );

		if ( ! instances.length ) {
			return;
		}

		window.setInterval( refresh, REFRESH_MS );

		// Keep the age counter honest between refreshes.
		window.setInterval( function () {
			instances.forEach( function ( instance ) {
				instance.write( 'updated', formatAge( CONFIG.updated ) );
			} );
		}, 10000 );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
