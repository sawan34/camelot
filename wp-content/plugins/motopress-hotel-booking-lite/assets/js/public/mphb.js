(function( $ ) {
	$( function() {
		MPHB.DateRules = can.Construct.extend( {}, {
	dates: {},
	init: function( dates ) {
		this.dates = dates;
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {Boolean}
	 */
	canCheckIn: function( date ) {
		var formattedDate = this.formatDate( date );
		if ( !this.dates.hasOwnProperty( formattedDate ) ) {
			return true;
		}
		return !this.dates[formattedDate].not_check_in && !this.dates[formattedDate].not_stay_in;
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {Boolean}
	 */
	canCheckOut: function( date ) {
		var formattedDate = this.formatDate( date );
		if ( !this.dates.hasOwnProperty( formattedDate ) ) {
			return true;
		}
		return !this.dates[formattedDate].not_check_out;
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {Boolean}
	 */
	canStayIn: function( date ) {
		var formattedDate = this.formatDate( date );
		if ( !this.dates.hasOwnProperty( formattedDate ) ) {
			return true;
		}
		return !this.dates[formattedDate].not_stay_in;
	},
	/**
	 *
	 * @param {Date} dateFrom
	 * @param {Date} stopDate
	 * @returns {Date}
	 */
	getNearestNotStayInDate: function( dateFrom, stopDate ) {
		var nearestDate = MPHB.Utils.cloneDate( stopDate );
		var dateFromFormatted = $.datepick.formatDate( 'yyyy-mm-dd', dateFrom );
		var stopDateFormatted = $.datepick.formatDate( 'yyyy-mm-dd', stopDate );

		$.each( this.dates, function( ruleDate, rule ) {
			if ( ruleDate > stopDateFormatted ) {
				return false;
			}
			if ( dateFromFormatted > ruleDate ) {
				return true;
			}
			if ( rule.not_stay_in ) {
				nearestDate = $.datepick.parseDate( 'yyyy-mm-dd', ruleDate );
				return false;
			}
		} );
		return nearestDate;
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {string}
	 */
	formatDate: function( date ) {
		return $.datepick.formatDate( 'yyyy-mm-dd', date );
	}
} );

MPHB.Datepicker = can.Control.extend( {}, {
	form: null,
	hiddenElement: null,
	init: function( el, args ) {
		this.form = args.form;
		this.setupHiddenElement();
		this.initDatepick();
	},
	setupHiddenElement: function() {
		var hiddenElementId = this.element.attr( 'id' ) + '-hidden';
		this.hiddenElement = $( '#' + hiddenElementId );

		// fix date
		if ( !this.hiddenElement.val() ) {
//			this.element.val( '' );
		} else {
			var date = $.datepick.parseDate( MPHB._data.settings.dateTransferFormat, this.hiddenElement.val() );
			var fixedValue = $.datepick.formatDate( MPHB._data.settings.dateFormat, date );
			this.element.val( fixedValue );
		}
	},
	initDatepick: function() {
		var defaultSettings = {
			dateFormat: MPHB._data.settings.dateFormat,
			altFormat: MPHB._data.settings.dateTransferFormat,
			altField: this.hiddenElement,
			minDate: MPHB.HotelDataManager.myThis.today,
			monthsToShow: MPHB._data.settings.numberOfMonthDatepicker,
			firstDay: MPHB._data.settings.firstDay,
			pickerClass: MPHB._data.settings.datepickerClass
		};
		var datepickSettings = $.extend( defaultSettings, this.getDatepickSettings() );
		this.element.datepick( datepickSettings );
	},
	/**
	 *
	 * @returns {Object}
	 */
	getDatepickSettings: function() {
		return {};
	},
	/**
	 * @return {Date|null}
	 */
	getDate: function() {
		var dateStr = this.element.val();
		var date = null;
		try {
			date = $.datepick.parseDate( MPHB._data.settings.dateFormat, dateStr );
		} catch ( e ) {
			date = null;
		}
		return date;
	},
	/**
	 *
	 * @param {string} format Optional. Datepicker format by default.
	 * @returns {String} Date string or empty string.
	 */
	getFormattedDate: function( format ) {
		if ( typeof (format) === 'undefined' ) {
			format = MPHB._data.settings.dateFormat;
		}
		var date = this.getDate();
		return date ? $.datepick.formatDate( format, date ) : '';
	},
	/**
	 * @param {Date} date
	 */
	setDate: function( date ) {
		this.element.datepick( 'setDate', date );
	},
	/**
	 * @param {string} option
	 */
	getOption: function( option ) {
		return this.element.datepick( 'option', option );
	},
	/**
	 * @param {string} option
	 * @param {mixed} value
	 */
	setOption: function( option, value ) {
		this.element.datepick( 'option', option, value );
	},
	/**
	 *
	 * @returns {Date|null}
	 */
	getMinDate: function() {
		var minDate = this.getOption( 'minDate' );
		return minDate !== null && minDate !== '' ? MPHB.Utils.cloneDate( minDate ) : null;
	},
	/**
	 *
	 * @returns {Date|null}
	 */
	getMaxDate: function() {
		var maxDate = this.getOption( 'maxDate' );
		return maxDate !== null && maxDate !== '' ? MPHB.Utils.cloneDate( maxDate ) : null;
	},
	/**
	 *
	 * @returns {undefined}
	 */
	clear: function() {
		this.element.datepick( 'clear' );
	},
	/**
	 * @param {Date} date
	 * @param {string} format Optional. Default 'yyyy-mm-dd'.
	 */
	formatDate: function( date, format ) {
		format = typeof (format) !== 'undefined' ? format : 'yyyy-mm-dd';
		return $.datepick.formatDate( format, date );
	},
	/**
	 *
	 * @returns {undefined}
	 */
	refresh: function() {
		$.datepick._update( this.element[0], true );
		$.datepick._updateInput( this.element[0], false );
	}

} );

MPHB.FlexsliderGallery = can.Control.extend( {}, {
	sliderEl: null,
	navSliderEl: null,
	groupId: null,
	init: function( sliderEl, args ) {
		this.sliderEl = sliderEl;

		this.groupId = sliderEl.data( 'group' );

		var navSliderEl = $( '.mphb-gallery-thumbnail-slider[data-group="' + this.groupId + '"]' );

		if ( navSliderEl.length ) {
			this.navSliderEl = navSliderEl;
		}

		var self = this;

		$( window ).on( 'load', function() {
			self.initSliders();
		} );
	},
	initSliders: function() {
		var sliderAtts = this.sliderEl.data( 'flexslider-atts' );

		if ( this.navSliderEl ) {
			var navSliderAtts = this.navSliderEl.data( 'flexslider-atts' );
			navSliderAtts['asNavFor'] = '.mphb-flexslider-gallery-wrapper[data-group="' + this.groupId + '"]';
			navSliderAtts['itemWidth'] = this.navSliderEl.find( 'ul > li img' ).width()

			sliderAtts['sync'] = '.mphb-gallery-thumbnail-slider[data-group="' + this.groupId + '"]';

			// The slider being synced must be initialized first
			this.navSliderEl
				.addClass( 'flexslider mphb-flexslider mphb-gallery-thumbnails-slider' )
				.flexslider( navSliderAtts );
		}

		this.sliderEl
			.addClass( 'flexslider mphb-flexslider mphb-gallery-slider' )
			.flexslider( sliderAtts );
	}
} );

MPHB.HotelDataManager = can.Construct.extend( {
	myThis: null,
	ROOM_STATUS_AVAILABLE: 'available',
	ROOM_STATUS_NOT_AVAILABLE: 'not-available',
	ROOM_STATUS_BOOKED: 'booked',
	ROOM_STATUS_PAST: 'past',
	MIN_PRIORITY: 3652 // Random big value
}, {
	today: null,
	roomTypesData: {},
	globalRule: null,
	dateRules: null,
	typeRules: {},
	seasons: {},
	init: function( data ) {
		MPHB.HotelDataManager.myThis = this;
		this.initRoomTypesData( data.room_types_data, data.rules );
		this.initRules( data.rules );
		this.initSeasons( data.seasons );
		this.setToday( $.datepick.parseDate( MPHB._data.settings.dateTransferFormat, data.today ) );
	},
	/**
	 *
	 * @returns {undefined}
	 */
	initRoomTypesData: function( roomTypesData, rules ) {
		var self = this;
		$.each( roomTypesData, function( id, data ) {
			var roomTypeData = new MPHB.RoomTypeData( id, data );

			// Block all rooms with global rules (where "Accommodation Type" = "All"
			// and "Accommodation" = "All")
			$.each( rules.dates, function ( dateFormatted, restrictions ) {
				if ( restrictions['not_stay_in'] ) {
					roomTypeData.blockAllRoomsOnDate( dateFormatted );
				}
			} );

			// Block all rooms with custom rules, where "Accommodation Type" = id
			// and "Accommodation" = "All"
			if ( rules.blockedTypes.hasOwnProperty( id ) ) {
				$.each( rules.blockedTypes[id], function ( dateFormatted, restrictions ) {
					if ( restrictions['not_stay_in'] ) {
						roomTypeData.blockAllRoomsOnDate( dateFormatted );
					}
				} );
			}

			self.roomTypesData[id] = roomTypeData;
		} );
	},
	initRules: function( rules ) {
		this.globalRule = new MPHB.ReservationRule( rules.global );
		this.dateRules = new MPHB.DateRules( rules.dates )

		var self = this;
		$.each( rules.blockedTypes, function( id, dates ) {
			self.typeRules[id] = new MPHB.DateRules( dates );
		} );
	},
	initSeasons: function( seasons ) {
		var dateFormat = MPHB._data.settings.dateTransferFormat;
		var globalRule = this.globalRule.getData();

		var self = this;

		$.each( seasons.list, function ( id, season ) {
			var startDate	 = $.datepick.parseDate( dateFormat, season.start_date );
			var endDate		 = $.datepick.parseDate( dateFormat, season.end_date );
			var allowedDays	 = season.allowed_days;
			var seasonRules	 = $.extend( {}, globalRule, seasons.rules[id] );
			var priority	 = seasons.priorities[id] != undefined ? seasons.priorities[id] : MPHB.HotelDataManager.MIN_PRIORITY;

			self.seasons[id] = {
				"startDate":	 startDate,
				"endDate":		 endDate,
				"allowedDays":	 allowedDays,
				"rule":			 new MPHB.ReservationRule( seasonRules ),
				"priority":		 priority
			};
		} );
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {undefined}
	 */
	setToday: function( date ) {
		this.today = date;
	},
	/**
	 *
	 * @param {int|string} id ID of roomType
	 * @returns {MPHB.RoomTypeData|false}
	 */
	getRoomTypeData: function( id ) {
		return this.roomTypesData.hasOwnProperty( id ) ? this.roomTypesData[id] : false;
	},
	/**
	 *
	 * @param {Object} dateData
	 * @param {Date} date
	 * @returns {Object}
	 */
	fillDateCellData: function( dateData, date ) {
		var rulesTitles = [ ];
		var rulesClasses = [ ];
		var roomTypeId = dateData.roomTypeId;

		if ( this.notStayIn( date, roomTypeId ) ) {
			rulesTitles.push( MPHB._data.translations.notStayIn );
			rulesClasses.push( 'mphb-not-stay-in-date' );
		}
		if ( this.notCheckIn( date, roomTypeId ) ) {
			rulesTitles.push( MPHB._data.translations.notCheckIn );
			rulesClasses.push( 'mphb-not-check-in-date' );
		}
		if ( this.notCheckOut( date, roomTypeId ) ) {
			rulesTitles.push( MPHB._data.translations.notCheckOut );
			rulesClasses.push( 'mphb-not-check-out-date' );
		}

		if ( rulesTitles.length ) {
			dateData.title += ' ' + MPHB._data.translations.rules + ' ' + rulesTitles.join( ', ' );
		}

		if ( rulesClasses.length ) {
			dateData.dateClass += (dateData.dateClass.length ? ' ' : '') + rulesClasses.join( ' ' );
		}

		return dateData;
	},
	notStayIn: function( date, roomTypeId ) {
		var canStay = this.dateRules.canStayIn( date );

		if ( this.typeRules[roomTypeId] ) {
			canStay = canStay && this.typeRules[roomTypeId].canStayIn( date );
		}

		return !canStay;
	},
	notCheckIn: function( date, roomTypeId ) {
		var canCheckIn = this.dateRules.canCheckIn( date );

		canCheckIn = canCheckIn && this.globalRule.isCheckInSatisfy( date );

		if ( this.typeRules[roomTypeId] ) {
			canCheckIn = canCheckIn && this.typeRules[roomTypeId].canCheckIn( date );
		}

		return !canCheckIn;
	},
	notCheckOut: function( date, roomTypeId ) {
		var canCheckOut = this.dateRules.canCheckOut( date );

		canCheckOut = canCheckOut && this.globalRule.isCheckOutSatisfy( date );

		if ( this.typeRules[roomTypeId] ) {
			canCheckOut = canCheckOut && this.typeRules[roomTypeId].canCheckOut( date );
		}

		return !canCheckOut;
	},
	/**
	 *
	 * @param {Date} checkInDate Check-in date object.
	 * @returns {Date} Min check-out date object.
	 */
	getSeasonMinCheckOutDate: function( checkInDate ) {
		var season = this.findSeasonByCheckInDate( checkInDate );
		if ( season ) {
			return season.rule.getMinCheckOutDate( checkInDate );
		} else {
			return this.globalRule.getMinCheckOutDate( checkInDate );
		}
	},
	/**
	 *
	 * @param {Date} checkInDate Check-in date object.
	 * @returns {Date} Max check-out date object.
	 */
	getSeasonMaxCheckOutDate: function( checkInDate ) {
		var season = this.findSeasonByCheckInDate( checkInDate );
		if ( season ) {
			return season.rule.getMaxCheckOutDate( checkInDate );
		} else {
			return this.globalRule.getMaxCheckOutDate( checkInDate );
		}
	},
	/**
	 *
	 * @param {Date} checkInDate Check-in date object.
	 * @param {Date} checkOutDate Check-out date object.
	 * @returns {Boolean}
	 */
	isCheckOutSatisfySeason: function( checkInDate, checkOutDate ) {
		var season = this.findSeasonByCheckInDate( checkInDate );
		if ( season ) {
			return season.rule.isCheckOutSatisfy( checkOutDate );
		} else {
			return this.globalRule.isCheckOutSatisfy( checkOutDate );
		}
	},
	/**
	 *
	 * @param {Date} checkInDate Check-in date object.
	 * @returns {Object}
	 */
	findSeasonByCheckInDate: function( checkInDate ) {
		var checkInDay = checkInDate.getDay();

		var foundSeason = null;
		var currentPriority = MPHB.HotelDataManager.MIN_PRIORITY;

		$.each( this.seasons, function( id, season ) {
			// The smaller the value, the higher the priority
			if ( season.priority < currentPriority
				 && ( checkInDate >= season.startDate && checkInDate <= season.endDate )
			) {
				// Check allowed days
				if ( season.allowedDays.indexOf( checkInDay ) != -1 ) {
					foundSeason = season;
					currentPriority = season.priority;
				}
			}
		} );

		return foundSeason;
	}
} );
MPHB.ReservationRule = can.Construct.extend( {}, {
	minDays: null,
	maxDays: null,
	checkInDays: null,
	checkOutDays: null,
	init: function( data ) {
		this.minDays = data.min_stay_length;
		this.maxDays = data.max_stay_length;
		this.checkInDays = data.check_in_days;
		this.checkOutDays = data.check_out_days;
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {Boolean}
	 */
	isCheckOutSatisfy: function( date ) {
		var checkOutDay = date.getDay();
		return $.inArray( checkOutDay, this.checkOutDays ) !== -1;
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {Boolean}
	 */
	isCheckInSatisfy: function( date ) {
		var checkInDay = date.getDay();
		return $.inArray( checkInDay, this.checkInDays ) !== -1;
	},
	/**
	 *
	 * @param {Date} checkInDate
	 * @param {Date} checkOutDate
	 * @returns {Boolean}
	 */
	isCorrect: function( checkInDate, checkOutDate ) {

		if ( typeof checkInDate === 'undefined' || typeof checkOutDate === 'undefined' ) {
			return true;
		}

		if ( !this.isCheckInSatisfy( checkInDate ) ) {
			return false;
		}

		if ( !this.isCheckOutSatisfy( checkOutDate ) ) {
			return false;
		}

		var minAllowedCheckOut = $.datepick.add( MPHB.Utils.cloneDate( checkInDate ), this.minDays );
		var maxAllowedCheckOut = $.datepick.add( MPHB.Utils.cloneDate( checkInDate ), this.maxDays );

		return checkOutDate >= minAllowedCheckOut && checkOutDate <= maxAllowedCheckOut;
	},
	/**
	 *
	 * @param {Date} checkInDate
	 * @returns {Date}
	 */
	getMinCheckOutDate: function( checkInDate ) {
		return $.datepick.add( MPHB.Utils.cloneDate( checkInDate ), this.minDays, 'd' );
	},
	/**
	 *
	 * @param {Date} checkInDate
	 * @returns {Date}
	 */
	getMaxCheckOutDate: function( checkInDate ) {
		return $.datepick.add( MPHB.Utils.cloneDate( checkInDate ), this.maxDays, 'd' );
	},
	getData: function() {
		return {
			"check_in_days":   this.checkInDays,
			"check_out_days":  this.checkOutDays,
			"min_stay_length": this.minDays,
			"max_stay_length": this.maxDays
		};
	}
} );
MPHB.Utils = can.Construct.extend( {
	/**
	 *
	 * @param {Date} date
	 * @returns {String}
	 */
	formatDateToCompare: function( date ) {
		return $.datepick.formatDate( 'yyyymmdd', date );
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {Date}
	 */
	cloneDate: function( date ) {
		return new Date( date.getTime() );
	}
}, {} );
MPHB.Gateway = can.Construct.extend( {}, {
	amount: 0,
	paymentDescription: '',
	init: function( args ) {
		this.billingSection = args.billingSection;
		this.initSettings( args.settings );
	},
	initSettings: function( settings ) {
		this.amount = settings.amount;
		this.paymentDescription = settings.paymentDescription;
	},
	canSubmit: function() {
		return true;
	},
	updateData: function( data ) {
		this.amount = data.amount;
		this.paymentDescription = data.paymentDescription;
	},
	afterSelection: function( newFieldset ) {},
	cancelSelection: function() {}
} );
/**
 *
 * @requires ./gateway.js
 */
MPHB.BeanstreamGateway = MPHB.Gateway.extend( {}, {
	scriptUrl: '',
	isCanSubmit: false,
	loadHandler: null,
	validityHandler: null,
	tokenRequestHandler: null,
	tokenUpdatedHandler: null,
	initSettings: function( settings ) {
		this._super( settings );
		this.scriptUrl = settings.scriptUrl || 'https://payform.beanstream.com/v1.1.0/payfields/beanstream_payfields.js';
		this.validityHandler = this.validityChanged.bind(this);
		this.tokenRequestHandler = this.tokenRequested.bind(this);
		this.tokenUpdatedHandler = this.tokenUpdated.bind(this);
	},
	canSubmit: function() {
		return this.isCanSubmit;
	},
	afterSelection: function( newFieldset ) {
		this._super( newFieldset );

		if ( newFieldset.length > 0 ) {
			var script = document.createElement( 'script' );
			// <script> must have id "fields-script" or it will fail to init
			script.id = 'payfields-script';
			script.src = this.scriptUrl;
			script.dataset.submitform = 'true';
			// Use async load only. Otherwise the script will wait infinitely for window.load event
			script.dataset.async = 'true';

			// Create new handler for Beanstream "loaded" (inited) event
			if ( this.loadHandler != null ) {
				$(document).off( 'beanstream_payfields_loaded', this.loadHandler );
			}
			this.loadHandler = function( data ) {
				$( '[data-beanstream-id]' ).appendTo( newFieldset );
			};
			$(document).on( 'beanstream_payfields_loaded', this.loadHandler );

			newFieldset.append( script );
			newFieldset.removeClass( 'mphb-billing-fields-hidden' );
		}

		// See all available events: https://github.com/Beanstream/checkoutfields#payfields-
		$(document).on( 'beanstream_payfields_inputValidityChanged', this.validityHandler )
				   .on( 'beanstream_payfields_tokenRequested', this.tokenRequestHandler )
				   .on( 'beanstream_payfields_tokenUpdated', this.tokenUpdatedHandler );
	},
	cancelSelection: function() {
		$(document).off( 'beanstream_payfields_inputValidityChanged', this.validityHandler )
				   .off( 'beanstream_payfields_tokenRequested', this.tokenRequestHandler )
				   .off( 'beanstream_payfields_tokenUpdated', this.tokenUpdatedHandler );
	},
	validityChanged: function( event ) {
		var eventDetail = event.eventDetail || event.originalEvent.eventDetail;
		if ( !eventDetail.isValid ) {
			this.isCanSubmit = false;
		}
	},
	tokenRequested: function( event ) {
		this.billingSection.showPreloader();
	},
	tokenUpdated: function( event ) {
		var eventDetail = event.eventDetail || event.originalEvent.eventDetail;
		if ( eventDetail.success ) {
			this.isCanSubmit = true;
		} else {
			this.isCanSubmit = false;
			this.billingSection.showError( MPHB._data.translations.tokenizationFailure.replace( '(%s)', eventDetail.message ) );
		}
		this.billingSection.hidePreloader();
	}
} );
/**
 *
 * @requires ./gateway.js
 */
MPHB.StripeGateway = MPHB.Gateway.extend( {}, {
	publicKey: '',
	imageUrl: '',
	locale: '',
	allowRememberMe: false,
	needBillingAddress: false,
	useBitcoin: false,
	panelLabel: '',
	handler: null,
	init: function( args ) {
		this._super( args );
		this.initHandler();
	},
	initSettings: function( settings ) {
		this._super( settings );
		this.publicKey = settings.publicKey;
		this.imageUrl = settings.checkoutImageUrl;
		this.allowRememberMe = settings.allowRememberMe;
		this.needBillingAddress = settings.needBillingAddress;
		this.useBitcoin = settings.useBitcoin;
		this.locale = settings.locale;
	},
	initHandler: function() {

		var self = this;
		var configureAtts = {
			key: this.publicKey,
			image: this.imageUrl,
			locale: this.locale,
			name: MPHB._data.settings.siteName,
			bitcoin: this.useBitcoin,
			currency: MPHB._data.settings.currency.toLowerCase(),
			billingAddress: this.needBillingAddress,
			allowRememberMe: this.allowRememberMe,
//			closed: function() {},
		};
		if ( self.panelLabel ) {
			configureAtts['panelLabel'] = self.panelLabel;
		}
		this.handler = StripeCheckout.configure( configureAtts );

		// Close Checkout on page navigation:
		window.addEventListener( 'popstate', function() {
			self.handler.close();
		} );
	},
	openModal: function() {
		var self = this;
		this.handler.open( {
			amount: self.amount,
			description: self.paymentDescription,
			token: function( token, args ) {

				self.storeToken( token );

				if ( self.needBillingAddress ) {
					self.storeBillingData( args );
				}

				self.storeEmail( token.email );
				self.billingSection.parentForm.element.submit();
				self.billingSection.showPreloader();
			},
		} );
	},
	/**
	 *
	 * @returns {Boolean}
	 */
	canSubmit: function() {
		if ( this.isTokenStored() ) {
			return true;
		}

		try {
			this.openModal();
		} catch ( e ) {
			console.log( 'error:', e );
		}

		return false;
	},
	/**
	 *
	 * @param {Object} token
	 * @returns {undefined}
	 */
	storeToken: function( token ) {
		var $tokenEl = this.billingSection.billingFieldsWrapperEl.find( '[name="mphb_stripe_token"]' );
		$tokenEl.val( token.id );
	},
	/**
	 *
	 * @returns {Boolean}
	 */
	isTokenStored: function() {
		var $tokenEl = this.billingSection.billingFieldsWrapperEl.find( '[name="mphb_stripe_token"]' );
		return $tokenEl.length && $tokenEl.val() !== '';
	},
	/**
	 *
	 * @param {string} email
	 * @returns {undefined}
	 */
	storeEmail: function( email ) {
		this.billingSection.billingFieldsWrapperEl.find( '[name="mphb_stripe_email"]' ).val( email );
	},
	/**
	 *
	 * @param {Object} data
	 * @returns {undefined}
	 */
	storeBillingData: function( data ) {
		var self = this;
		var acceptableFields = [
			'billing_address_city',
			'billing_address_country',
			'billing_address_country_code',
			'billing_address_line1',
			'billing_address_line2',
			'billing_address_state',
			'billing_address_zip',
			'billing_name'
		];

		$.each( acceptableFields, function( key, field ) {
			if ( data.hasOwnProperty( field ) ) {
				var fieldEl = self.billingSection.billingFieldsWrapperEl.find( '[name="mphb_stripe_' + field + '"]' );
				if ( fieldEl.length ) {
					fieldEl.val( data[field] );
				}
			}
		} );

	}
} );
/**
 *
 * @requires ./gateway.js
 * @requires ./stripe-gateway.js
 */
MPHB.BillingSection = can.Control.extend( {}, {
	updateBillingFieldsTimeout: null,
	parentForm: null,
	billingFieldsWrapperEl: null,
	gateways: {},
	lastGatewayId: null,
	init: function( el, args ) {
		this.parentForm = args.form;
		this.billingFieldsWrapperEl = this.element.find( '.mphb-billing-fields' );
		this.initGateways( args.gateways );
	},
	initGateways: function( gateways ) {
		var self = this;
		$.each( gateways, function( gatewayId, gatewaySettings ) {
			var gateway = null;
			switch ( gatewayId ) {
				case 'stripe':
					gateway = new MPHB.StripeGateway( {
						'billingSection': self,
						'settings': MPHB._data.gateways[gatewayId]
					} );
					break;
				case 'braintree':
					gateway = new MPHB.BraintreeGateway( {
						'billingSection': self,
						'settings': MPHB._data.gateways[gatewayId]
					} );
					break;
				case 'beanstream':
					gateway = new MPHB.BeanstreamGateway( {
						'billingSection': self,
						'settings': MPHB._data.gateways[gatewayId]
					} );
					break;
				default:
					gateway = new MPHB.Gateway( {
						'billingSection': self,
						'settings': MPHB._data.gateways[gatewayId]
					} );
					break;
			}
			if ( typeof gateway !== 'undefined' ) {
				self.gateways[gatewayId] = gateway;
			}
		} );
		this.notifySelectedGateway();
	},
	'[name="mphb_gateway_id"] change': function( el, e ) {
		var self = this;
		var gatewayId = el.val();
		this.showPreloader();
		this.billingFieldsWrapperEl.empty().addClass( 'mphb-billing-fields-hidden' );
		clearTimeout( this.updateBillingFieldsTimeout );
		this.updateBillingFieldsTimeout = setTimeout( function() {
			var formData = self.parentForm.parseFormToJSON();
			$.ajax( {
				url: MPHB._data.ajaxUrl,
				type: 'GET',
				dataType: 'json',
				data: {
					action: 'mphb_get_billing_fields',
					mphb_nonce: MPHB._data.nonces.mphb_get_billing_fields,
					mphb_gateway_id: gatewayId,
					formValues: formData
				},
				success: function( response ) {
					if ( response.hasOwnProperty( 'success' ) ) {
						if ( response.success ) {
							// Disable previous selected gateway
							if ( self.lastGatewayId ) {
								self.gateways[self.lastGatewayId].cancelSelection();
							}

							self.billingFieldsWrapperEl.html( response.data.fields );
							if ( response.data.hasVisibleFields ) {
								self.billingFieldsWrapperEl.removeClass( 'mphb-billing-fields-hidden' );
							} else {
								self.billingFieldsWrapperEl.addClass( 'mphb-billing-fields-hidden' );
							}

							self.notifySelectedGateway( gatewayId );

						} else {
							self.showError( response.data.message );
						}
					} else {
						self.showError( MPHB._data.translations.errorHasOccured );
					}
				},
				error: function( jqXHR ) {
					self.showError( MPHB._data.translations.errorHasOccured );
				},
				complete: function( jqXHR ) {
					self.hidePreloader();
				}
			} );
		}, 500 );
	},
	hideErrors: function() {
		this.parentForm.hideErrors();
	},
	showError: function( message ) {
		this.parentForm.showError( message );
	},
	showPreloader: function() {
		this.parentForm.showPreloader();
	},
	hidePreloader: function() {
		this.parentForm.hidePreloader();
	},
	canSubmit: function() {
		var gatewayId = this.getSelectedGateway();
		return !this.gateways.hasOwnProperty( gatewayId ) || this.gateways[gatewayId].canSubmit();
	},
	getSelectedGateway: function() {
		var gatewayIdFields = this.element.find( '[name="mphb_gateway_id"]' );
		var selectedGatewayField = gatewayIdFields.length === 1 ? gatewayIdFields : gatewayIdFields.filter(':checked');
		return selectedGatewayField.val();
	},
	notifySelectedGateway: function( gatewayId ) {
		gatewayId = gatewayId || this.getSelectedGateway();
		if ( gatewayId ) {
			this.gateways[gatewayId].afterSelection( this.billingFieldsWrapperEl );
		}
		this.lastGatewayId = gatewayId;
	},
	updateGatewaysData: function( gatewaysData ) {
		var self = this;
		$.each( gatewaysData, function( gatewayId, gatewayData ) {
			if ( self.gateways.hasOwnProperty( gatewayId ) ) {
				self.gateways[gatewayId].updateData( gatewayData );
			}
		} );
	}
} );
/**
 *
 * @requires ./gateway.js
 */
MPHB.BraintreeGateway = MPHB.Gateway.extend( {}, {
	clientToken: '',
	checkout: null, // Used to remove all fields and events of the Braintree SDK
	initSettings: function( settings ) {
		this._super( settings );
		this.clientToken = settings.clientToken;
	},
	/**
	 *
	 * @returns {Boolean}
	 */
	canSubmit: function() {
		return this.isNonceStored();
	},
	/**
	 *
	 * @param {String} nonce
	 * @returns {undefined}
	 */
	storeNonce: function( nonce ) {
		var $nonceEl = this.billingSection.billingFieldsWrapperEl.find( '[name="mphb_braintree_payment_nonce"]' );
		$nonceEl.val( nonce );
	},
	/**
	 *
	 * @returns {Boolean}
	 */
	isNonceStored: function() {
		var $nonceEl = this.billingSection.billingFieldsWrapperEl.find( '[name="mphb_braintree_payment_nonce"]' );
		return $nonceEl.length && $nonceEl.val() != '';
	},
	afterSelection: function ( newFieldset ) {
		this._super( newFieldset );
		if ( braintree != undefined ) {
			var containerId = 'mphb-braintree-container-' + this.clientToken.substr(0, 8);
			newFieldset.append('<div id="' + containerId + '"></div>');

			var self = this;
			braintree.setup( this.clientToken, 'dropin', {
				container: containerId,
				onReady: function( integration ) {
					// We can use integration's teardown() method to remove all DOM elements and attached events
					self.checkout = integration;
				},
				onPaymentMethodReceived: function( response ) {
					self.storeNonce( response.nonce );
					self.billingSection.parentForm.element.submit();
					self.billingSection.showPreloader();
				}
			} );

			newFieldset.removeClass( 'mphb-billing-fields-hidden' );
		}
	},
	cancelSelection: function() {
		this._super();
		if ( this.checkout != null ) {
			var self = this;
			this.checkout.teardown( function() {
				self.checkout = null; // braintree.setup() can safely be run again
			} );
		}
	}
} );

MPHB.CouponSection = can.Control.extend( {}, {
	applyCouponTimeout: null,
	parentForm: null,
	appliedCouponEl: null,
	couponEl: null,
	messageHolderEl: null,
	init: function( el, args ) {
		this.parentForm = args.form;
		this.couponEl = el.find( '[name="mphb_coupon_code"]' );
		this.appliedCouponEl = el.find( '[name="mphb_applied_coupon_code"]' );
		this.messageHolderEl = el.find( '.mphb-coupon-message' );
	},
	'.mphb-apply-coupon-code-button click': function( el, e ) {
		e.preventDefault();
		e.stopPropagation();

		this.clearMessage();

		var couponCode = this.couponEl.val();
		if ( !couponCode.length ) {
			this.showMessage( MPHB._data.translations.emptyCouponCode );
			return;
		}

		this.appliedCouponEl.val( '' );

		var self = this;
		this.showPreloader();

		clearTimeout( this.applyCouponTimeout );
		this.applyCouponTimeout = setTimeout( function() {
			var formData = self.parentForm.parseFormToJSON();
			$.ajax( {
				url: MPHB._data.ajaxUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'mphb_apply_coupon',
					mphb_nonce: MPHB._data.nonces.mphb_apply_coupon,
					mphb_coupon_code: couponCode,
					formValues: formData
				},
				success: function( response ) {
					if ( response.hasOwnProperty( 'success' ) ) {
						if ( response.success ) {

							self.parentForm.setCheckoutData( response.data );

							self.couponEl.val( '' );
							self.appliedCouponEl.val( response.data.coupon.applied_code );
							self.showMessage( response.data.coupon.message );

						} else {
							self.showMessage( response.data.message );
						}
					} else {
						self.showMessage( MPHB._data.translations.errorHasOccured );
					}
				},
				error: function( jqXHR ) {
					self.showMessage( MPHB._data.translations.errorHasOccured );
				},
				complete: function( jqXHR ) {
					self.hidePreloader();
				}
			} );
		}, 500 );
	},
	removeCoupon: function() {
		this.appliedCouponEl.val( '' );
		this.clearMessage();
	},
	showPreloader: function() {
		this.parentForm.showPreloader();
	},
	hidePreloader: function() {
		this.parentForm.hidePreloader();
	},
	clearMessage: function() {
		this.messageHolderEl.html( '' ).addClass( 'mphb-hide' );
	},
	showMessage: function( message ) {
		this.messageHolderEl.html( message ).removeClass( 'mphb-hide' );
	}
} );
/**
 *
 * @requires ./billing-section.js
 * @requires ./coupon-section.js
 */
MPHB.CheckoutForm = can.Control.extend( {
	myThis: null
}, {
	priceBreakdownTableEl: null,
	bookBtnEl: null,
	errorsWrapperEl: null,
	preloaderEl: null,
	billingSection: null,
	couponSection: null,
	waitResponse: false,
	updateInfoTimeout: null,
	freeBooking: false,
	init: function( el, args ) {
		MPHB.CheckoutForm.myThis = this;
		this.bookBtnEl = this.element.find( 'input[type=submit]' );
		this.errorsWrapperEl = this.element.find( '.mphb-errors-wrapper' );
		this.preloaderEl = this.element.find( '.mphb-preloader' );
		this.priceBreakdownTableEl = this.element.find( 'table.mphb-price-breakdown' );
		if ( MPHB._data.settings.useBilling ) {
			this.billingSection = new MPHB.BillingSection( this.element.find( '#mphb-billing-details' ), {
				'form': this,
				'gateways': MPHB._data.gateways
			} );
		}
		if ( MPHB._data.settings.useCoupons ) {
			this.couponSection = new MPHB.CouponSection( this.element.find( '#mphb-coupon-details' ), {
				'form': this,
			} );
		}
	},
	setTotal: function( value ) {
		var totalField = this.element.find( '.mphb-total-price-field' );
		if ( totalField.length ) {
			totalField.html( value );
		}
	},
	setDeposit: function( value ) {
		var depositField = this.element.find( '.mphb-deposit-amount-field' );
		if ( depositField.length ) {
			depositField.html( value );
		}
	},
	setupPriceBreakdown: function( priceBreakdown ) {
		this.priceBreakdownTableEl.replaceWith( priceBreakdown );
		this.priceBreakdownTableEl = this.element.find( 'table.mphb-price-breakdown' );
	},
	updateCheckoutInfo: function() {
		var self = this;
		self.hideErrors();
		self.showPreloader();
		clearTimeout( this.updateInfoTimeout );
		this.updateInfoTimeout = setTimeout( function() {
			var data = self.parseFormToJSON();
			$.ajax( {
				url: MPHB._data.ajaxUrl,
				type: 'GET',
				dataType: 'json',
				data: {
					action: 'mphb_update_checkout_info',
					mphb_nonce: MPHB._data.nonces.mphb_update_checkout_info,
					formValues: data
				},
				success: function( response ) {
					if ( response.hasOwnProperty( 'success' ) ) {
						if ( response.success ) {
							self.setCheckoutData( response.data );
						} else {
							self.showError( response.data.message );
						}
					} else {
						self.showError( MPHB._data.translations.errorHasOccured );
					}
				},
				error: function( jqXHR ) {
					self.showError( MPHB._data.translations.errorHasOccured );
				},
				complete: function( jqXHR ) {
					self.hidePreloader();
				}
			} );
		}, 500 );
	},
	setCheckoutData: function( data ) {
		this.setTotal( data.total );
		this.setupPriceBreakdown( data.priceBreakdown );

		if ( MPHB._data.settings.useBilling ) {
			this.setDeposit( data.deposit );
			this.billingSection.updateGatewaysData( data.gateways );

			if ( data.isFree ) {
				this.setFreeMode();
			} else {
				this.unsetFreeMode();
			}
		}
	},
	setFreeMode: function() {
		this.freeBooking = true;
		this.billingSection.element.addClass( 'mphb-hide' );
		this.element.append( $( '<input />', {
			'type': 'hidden',
			'name': 'mphb_gateway_id',
			'value': 'manual',
			'id': 'mphb-manual-payment-input'
		} ) );
	},
	unsetFreeMode: function() {
		this.freeBooking = false;
		this.billingSection.element.removeClass( 'mphb-hide' );
		this.element.find( '#mphb-manual-payment-input' ).remove();
	},
	'.mphb_sc_checkout-guests-chooser change': function( el, e ) {
		this.updateCheckoutInfo();
	},
	'.mphb_sc_checkout-rate change': function( el, e ) {
		this.updateCheckoutInfo();
	},
	'.mphb_sc_checkout-service, .mphb_sc_checkout-service-adults change': function( el, e ) {
		this.updateCheckoutInfo();
	},
	// See also assets/js/admin/dev/controls/price-breakdown-ctrl.js
	'.mphb-price-breakdown-expand click': function( el, e ) {
		e.preventDefault();
		$( el ).blur(); // Don't save a:focus style on last clicked item
		var tr = $( el ).parents( 'tr.mphb-price-breakdown-group' );
		tr.find( '.mphb-price-breakdown-rate' ).toggleClass( 'mphb-hide' );
		tr.nextUntil( 'tr.mphb-price-breakdown-group' ).toggleClass( 'mphb-hide' );
	},
	hideErrors: function() {
		this.errorsWrapperEl.empty().addClass( 'mphb-hide' );
	},
	showError: function( message ) {
		this.errorsWrapperEl.html( message ).removeClass( 'mphb-hide' );
	},
	showPreloader: function() {
		this.waitResponse = true;
		this.bookBtnEl.attr( 'disabled', 'disabled' );
		this.preloaderEl.removeClass( 'mphb-hide' );
	},
	hidePreloader: function() {
		this.waitResponse = false;
		this.bookBtnEl.removeAttr( 'disabled' );
		this.preloaderEl.addClass( 'mphb-hide' );
	},
	parseFormToJSON: function() {
		return this.element.serializeJSON();
	},
	'submit': function( el, e ) {
		if ( this.waitResponse ) {
			return false;
		}
		if ( MPHB._data.settings.useBilling && !this.freeBooking && !this.billingSection.canSubmit() ) {
			return false;
		}
	},
	'#mphb-price-details .mphb-remove-coupon click': function( el, e ) {
		e.preventDefault();
		e.stopPropagation();

		if ( MPHB._data.settings.useCoupons ) {
			this.couponSection.removeCoupon();
			this.updateCheckoutInfo();
		}
	}
} );
MPHB.DirectBooking = can.Control.extend(
	{},
	{
		reservationForm: null, // form.mphb-booking-form
		quantitySection: null, // div.mphb-reserve-room-section
		quantitySelect: null, // select.mphb-rooms-quantity
		availableLabel: null, // span.mphb-available-rooms-count
		typeId: 0,
		init: function( el, args ) {
			this.reservationForm = args.reservationForm;
			this.quantitySection = el.find( '.mphb-reserve-room-section' );
			this.quantitySelect = this.quantitySection.find( '.mphb-rooms-quantity' );
			this.availableLabel = this.quantitySection.find( '.mphb-available-rooms-count' );
			this.typeId = el.find('input[name="mphb_room_type_id"]').val();
			this.typeId = parseInt( this.typeId );
		},
		hideQuantitySection: function() {
			this.quantitySection.addClass( 'mphb-hide' );
		},
		showQuantitySection: function() {
			this.quantitySection.removeClass( 'mphb-hide' );
		},
		resetQuantityOptions: function( count ) {
			this.quantitySelect.empty();

			for ( var i = 1; i <= count; i++ ) {
				var option = '<option value="' + i + '">' + i + '</option>';
				this.quantitySelect.append( option );
			}

			this.quantitySelect.val( 1 ); // Otherwise the last option will be active

			// Also update text "of %d accommodation(-s) available."
			if ( count == 1 ) {
				this.availableLabel.text( MPHB._data.translations.countRoomsAvailable_singular.replace( '%d', count ) );
			} else {
				this.availableLabel.text( MPHB._data.translations.countRoomsAvailable_plural.replace( '%d', count ) );
			}
		},
		/**
		 * See also MPHB.ReservationForm.onDatepickChange().
		 */
		"input.mphb-datepick change": function( el, e ) {
			this.hideQuantitySection();
		},
		".mphb-reserve-btn click": function( el, e ) {
			e.preventDefault();
			e.stopPropagation();

			this.reservationForm.clearErrors();
			this.reservationForm.setFormWaitingMode();

			var checkIn = this.reservationForm.checkInDatepicker.getFormattedDate();
			var checkOut = this.reservationForm.checkOutDatepicker.getFormattedDate();

			if ( checkIn == '' || checkOut == '' ) {
				if ( checkIn == '' ) {
					this.reservationForm.showError( MPHB._data.translations.checkInNotValid );
				} else {
					this.reservationForm.showError( MPHB._data.translations.checkOutNotValid );
				}
				this.reservationForm.setFormNormalMode();
				return;
			}

			var self = this;
			$.ajax( {
				url: MPHB._data.ajaxUrl,
				type: 'GET',
				dataType: 'json',
				data: {
					action: 'mphb_get_free_accommodations_amount',
					mphb_nonce: MPHB._data.nonces.mphb_get_free_accommodations_amount,
					typeId: this.typeId,
					checkInDate: checkIn,
					checkOutDate: checkOut
				},
				success: function( response ) {
					if ( response.success ) {
						self.resetQuantityOptions( response.data.freeCount );
						self.showQuantitySection();
					} else {
						self.reservationForm.showError( response.data.message );
					}
				},
				error: function( jqXHR ) {
					self.reservationForm.showError( MPHB._data.translations.errorHasOccured );
				},
				complete: function( jqXHR ) {
					self.reservationForm.setFormNormalMode();
				}
			} );
		}
	}
);

MPHB.ReservationForm = can.Control.extend( {
	MODE_SUBMIT: 'submit',
	MODE_NORMAL: 'normal',
	MODE_WAITING: 'waiting'
}, {
	/**
	 * @var jQuery
	 */
	formEl: null,
	/**
	 * @var MPHB.RoomTypeCheckInDatepicker
	 */
	checkInDatepicker: null,
	/**
	 * @var MPHB.RoomTypeCheckOutDatepicker
	 */
	checkOutDatepicker: null,
	/**
	 * @var jQuery
	 */
	reserveBtn: null,
	/**
	 * @var jQuery
	 */
	reserveBtnPreloader: null,
	/**
	 * @var jQuery
	 */
	errorsWrapper: null,
	/**
	 * @var jQuery
	 */
	directBooking: null,
	/**
	 * @var String
	 */
	mode: null,
	/**
	 * @var int
	 */
	roomTypeId: null,
	/**
	 * @var MPHB.RoomTypeData
	 */
	roomTypeData: null,
	setup: function( el, args ) {
		this._super( el, args );
		this.mode = MPHB.ReservationForm.MODE_NORMAL;
	},
	init: function( el, args ) {
		this.formEl = el;
		this.roomTypeId = parseInt( this.formEl.attr( 'id' ).replace( /^booking-form-/, '' ) );
		this.roomTypeData = MPHB.HotelDataManager.myThis.getRoomTypeData( this.roomTypeId );
		this.errorsWrapper = this.formEl.find( '.mphb-errors-wrapper' );
		this.initCheckInDatepicker();
		this.initCheckOutDatepicker();
		this.initReserveBtn();

		// Init direct booking
		if ( MPHB._data.settings.isDirectBooking == '1' ) {
			this.directBooking = new MPHB.DirectBooking( el, { "reservationForm": this } );
		}

		var self = this;
		$( window ).on( 'mphb-update-date-room-type-' + this.roomTypeId, function() {
			self.refreshDatepickers();
		} );

		// Enable reservation rules on check-out date
		if ( this.checkInDatepicker.getDate() ) {
			this.updateCheckOutLimitations();
		}
	},
	proceedToCheckout: function() {
		this.mode = MPHB.ReservationForm.MODE_SUBMIT;
		this.unlock();
		this.formEl.submit();
	},
	showError: function( message ) {
		this.clearErrors();
		var errorMessage = $( '<p>', {
			'class': 'mphb-error',
			'html': message
		} );
		this.errorsWrapper.append( errorMessage ).removeClass( 'mphb-hide' );
	},
	clearErrors: function() {
		this.errorsWrapper.empty().addClass( 'mphb-hide' );
	},
	lock: function() {
		this.element.find( '[name]' ).attr( 'disabled', 'disabled' );
		this.reserveBtn.attr( 'disabled', 'disabled' ).addClass( 'mphb-disabled' );
		this.reserveBtnPreloader.removeClass( 'mphb-hide' );
	},
	unlock: function() {
		this.element.find( '[name]' ).removeAttr( 'disabled' );
		this.reserveBtn.removeAttr( 'disabled' ).removeClass( 'mphb-disabled' );
		this.reserveBtnPreloader.addClass( 'mphb-hide' );
	},
	setFormWaitingMode: function() {
		this.mode = MPHB.ReservationForm.MODE_WAITING;
		this.lock();
	},
	setFormNormalMode: function() {
		this.mode = MPHB.ReservationForm.MODE_NORMAL;
		this.unlock();
	},
	initCheckInDatepicker: function() {
		var checkInEl = this.formEl.find( 'input[type="text"][id^=mphb_check_in_date]' );
		this.checkInDatepicker = new MPHB.RoomTypeCheckInDatepicker( checkInEl, {'form': this} );
	},
	initCheckOutDatepicker: function() {
		var checkOutEl = this.formEl.find( 'input[type="text"][id^=mphb_check_out_date]' );
		this.checkOutDatepicker = new MPHB.RoomTypeCheckOutDatepicker( checkOutEl, {'form': this} );
	},
	initReserveBtn: function() {
		this.reserveBtn = this.formEl.find( '.mphb-reserve-btn' );
		this.reserveBtnPreloader = this.formEl.find( '.mphb-preloader' );

		this.setFormNormalMode();
	},
	/**
	 *
	 * @param {bool} setDate
	 * @returns {undefined}
	 */
	updateCheckOutLimitations: function( setDate ) {
		if ( typeof setDate === 'undefined' ) {
			setDate = true;
		}
		var limitations = this.retrieveCheckOutLimitations( this.checkInDatepicker.getDate(), this.checkOutDatepicker.getDate() );

		this.checkOutDatepicker.setOption( 'minDate', limitations.minDate );
		this.checkOutDatepicker.setOption( 'maxDate', limitations.maxDate );
		this.checkOutDatepicker.setDate( setDate ? limitations.date : null );
	},
	/**
	 *
	 * @param {type} checkInDate
	 * @param {type} checkOutDate
	 * @returns {Object} with keys
	 *	- {Date} minDate
	 *	- {Date} maxDate
	 *	- {Date|null} date
	 */
	retrieveCheckOutLimitations: function( checkInDate, checkOutDate ) {

		var minDate = MPHB.HotelDataManager.myThis.today;
		var maxDate = null;
		var recommendedDate = null;

		if ( checkInDate !== null ) {
			var minDate = MPHB.HotelDataManager.myThis.getSeasonMinCheckOutDate( checkInDate );

			var maxDate = MPHB.HotelDataManager.myThis.getSeasonMaxCheckOutDate( checkInDate );
			maxDate = this.roomTypeData.getNearestLockedDate( checkInDate, maxDate );
			maxDate = this.roomTypeData.getNearestHaveNotPriceDate( checkInDate, maxDate );
			maxDate = MPHB.HotelDataManager.myThis.dateRules.getNearestNotStayInDate( checkInDate, maxDate );

			if ( this.isCheckOutDateNotValid( checkInDate, checkOutDate, minDate, maxDate ) ) {
				recommendedDate = this.retrieveRecommendedCheckOutDate( checkInDate, minDate, maxDate );
			} else {
				recommendedDate = checkOutDate;
			}
		}

		return {
			minDate: minDate,
			maxDate: maxDate,
			date: recommendedDate
		};
	},
	/**
	 *
	 * @param {Date} minDate
	 * @param {Date} maxDate
	 * @returns {Date|null}
	 */
	retrieveRecommendedCheckOutDate: function( checkInDate, minDate, maxDate ) {
		var recommendedDate = null;
		var expectedDate = MPHB.Utils.cloneDate( minDate );

		while ( MPHB.Utils.formatDateToCompare( expectedDate ) <= MPHB.Utils.formatDateToCompare( maxDate ) ) {

			var prevDay = $.datepick.add( MPHB.Utils.cloneDate( expectedDate ), -1, 'd' );

			if (
				!this.isCheckOutDateNotValid( checkInDate, expectedDate, minDate, maxDate ) &&
				this.roomTypeData.hasPriceForDate( prevDay )
			) {
				recommendedDate = expectedDate;
				break;
			}
			expectedDate = $.datepick.add( expectedDate, 1, 'd' );
		}

		return recommendedDate;

	},
	/**
	 *
	 * @param {Date} checkOutDate
	 * @param {Date} minDate
	 * @param {Date} maxDate
	 * @returns {Boolean}
	 */
	isCheckOutDateNotValid: function( checkInDate, checkOutDate, minDate, maxDate ) {
		return checkOutDate === null
			|| MPHB.Utils.formatDateToCompare( checkOutDate ) < MPHB.Utils.formatDateToCompare( minDate )
			|| MPHB.Utils.formatDateToCompare( checkOutDate ) > MPHB.Utils.formatDateToCompare( maxDate )
			|| !MPHB.HotelDataManager.myThis.isCheckOutSatisfySeason( checkInDate, checkOutDate )
			|| !MPHB.HotelDataManager.myThis.dateRules.canCheckOut( checkOutDate )
	},
	clearDatepickers: function() {
		this.checkInDatepicker.clear();
		this.checkOutDatepicker.clear();
	},
	refreshDatepickers: function() {
		this.checkInDatepicker.refresh();
		this.checkOutDatepicker.refresh();
	},
	/**
	 * See also MPHB.DirectBooking["input.mphb-datepick change"].
	 */
	onDatepickChange: function() {
		if ( this.directBooking != null ) {
			this.directBooking.hideQuantitySection();
		}
	}

} );
MPHB.RoomTypeCalendar = can.Control.extend( {}, {
	roomTypeData: null,
	roomTypeId: null,
	init: function( el, args ) {
		this.roomTypeId = parseInt( el.attr( 'id' ).replace( /^mphb-calendar-/, '' ) );
		this.roomTypeData = MPHB.HotelDataManager.myThis.getRoomTypeData( this.roomTypeId );
		var self = this;
		el.hide().datepick( {
			onDate: function( date, current ) {
				var dateData = {
					selectable: false,
					dateClass: 'mphb-date-cell',
					title: '',
					roomTypeId: self.roomTypeId
				};

				if ( current ) {
					dateData = self.roomTypeData.fillDateData( dateData, date );
				} else {
					dateData.dateClass += ' mphb-extra-date';
				}

				return dateData;
			},
			minDate: MPHB.HotelDataManager.myThis.today,
			monthsToShow: MPHB._data.settings.numberOfMonthCalendar,
			firstDay: MPHB._data.settings.firstDay,
			pickerClass: MPHB._data.settings.datepickerClass
		} ).show();

		$( window ).on( 'mphb-update-room-type-data-' + this.roomTypeId, function( e ) {
			self.refresh();
		} );

	},
	refresh: function() {
		this.element.hide();
		$.datepick._update( this.element[0], true );
		this.element.show();
	}

} );
/**
 *
 * @requires ./../datepicker.js
 */
MPHB.RoomTypeCheckInDatepicker = MPHB.Datepicker.extend( {}, {
	isDirectBooking: false,
	init: function( el, args ) {
		this._super( el, args );
		this.isDirectBooking = MPHB._data.settings.isDirectBooking == '1' ? true : false;
	},
	/**
	 *
	 * @returns {Object}
	 */
	getDatepickSettings: function() {
		var self = this;
		return {
			onDate: function( date, current ) {
				var dateData = {
					dateClass: 'mphb-date-cell',
					selectable: false,
					title: ''
				}

				if ( current ) {
					dateData = self.form.roomTypeData.fillDateData( dateData, date );

					var canCheckIn = MPHB.HotelDataManager.myThis.globalRule.isCheckInSatisfy( date )
						&& MPHB.HotelDataManager.myThis.dateRules.canCheckIn( date );

					if ( self.isDirectBooking ) {
						var status = self.form.roomTypeData.getDateStatus( date );
						canCheckIn = canCheckIn && status === MPHB.HotelDataManager.ROOM_STATUS_AVAILABLE;
					}

					if ( canCheckIn ) {
						dateData.selectable = true;
					}

				} else {
					dateData.dateClass += ' mphb-extra-date';
				}

				if ( dateData.selectable ) {
					dateData.dateClass += ' mphb-date-selectable';
				}

				return dateData;
			},
			onSelect: function( dates ) {
				self.form.updateCheckOutLimitations();
				self.form.onDatepickChange();
			},
			pickerClass: 'mphb-datepick-popup mphb-check-in-datepick ' + MPHB._data.settings.datepickerClass,
		};
	},
	/**
	 * @param {Date} date
	 */
	setDate: function( date ) {

		if ( date == null ) {
			return this._super( date );
		}

		if ( !MPHB.HotelDataManager.myThis.globalRule.isCheckInSatisfy( date ) ) {
			return this._super( null );
		}

		if ( !MPHB.HotelDataManager.myThis.dateRules.canCheckIn( date ) ) {
			return this._super( null );
		}

		return this._super( date );
	}

} );
/**
 *
 * @requires ./../datepicker.js
 */
MPHB.RoomTypeCheckOutDatepicker = MPHB.Datepicker.extend( {}, {
	/**
	 *
	 * @returns {Object}
	 */
	getDatepickSettings: function() {
		var self = this;
		return {
			onDate: function( date, current ) {
				var dateData = {
					dateClass: 'mphb-date-cell',
					selectable: false,
					title: ''
				};
				if ( current ) {
					var checkInDate = self.form.checkInDatepicker.getDate();
					var earlierThanMin = self.getMinDate() !== null && MPHB.Utils.formatDateToCompare( date ) < MPHB.Utils.formatDateToCompare( self.getMinDate() );
					var laterThanMax = self.getMaxDate() !== null && MPHB.Utils.formatDateToCompare( date ) > MPHB.Utils.formatDateToCompare( self.getMaxDate() );

					if ( checkInDate !== null && MPHB.Utils.formatDateToCompare( date ) === MPHB.Utils.formatDateToCompare( checkInDate ) ) {
						dateData.dateClass += ' mphb-check-in-date';
						dateData.title += MPHB._data.translations.checkInDate;
					}

					if ( earlierThanMin ) {
						var minStayDate = checkInDate ? MPHB.HotelDataManager.myThis.globalRule.getMinCheckOutDate( checkInDate ) : false;
						if ( MPHB.Utils.formatDateToCompare( date ) < MPHB.Utils.formatDateToCompare( checkInDate ) ) {
							dateData.dateClass += ' mphb-earlier-min-date mphb-earlier-check-in-date';
						} else if ( minStayDate && MPHB.Utils.formatDateToCompare( date ) < MPHB.Utils.formatDateToCompare( minStayDate ) ) {
							dateData.dateClass += ' mphb-earlier-min-date';
							dateData.title += (dateData.title.length ? ' ' : '') + MPHB._data.translations.lessThanMinDaysStay;
						}
					}

					if ( laterThanMax ) {
						var maxStayDate = checkInDate ? MPHB.HotelDataManager.myThis.globalRule.getMaxCheckOutDate( checkInDate ) : false;
						if ( !maxStayDate || MPHB.Utils.formatDateToCompare( date ) < MPHB.Utils.formatDateToCompare( maxStayDate ) ) {
							dateData.title += (dateData.title.length ? ' ' : '') + MPHB._data.translations.laterThanMaxDate;
						} else {
							dateData.title += (dateData.title.length ? ' ' : '') + MPHB._data.translations.moreThanMaxDaysStay;
						}
						dateData.dateClass += ' mphb-later-max-date';
					}

					dateData = self.form.roomTypeData.fillDateData( dateData, date );

					var canCheckOut = !earlierThanMin && !laterThanMax &&
						MPHB.HotelDataManager.myThis.globalRule.isCheckOutSatisfy( date ) &&
						MPHB.HotelDataManager.myThis.dateRules.canCheckOut( date );

					if ( canCheckOut ) {
						dateData.selectable = true;
					}
				} else {
					dateData.dateClass += ' mphb-extra-date';
				}

				if ( dateData.selectable ) {
					dateData.dateClass += ' mphb-selectable-date';
				} else {
					dateData.dateClass += ' mphb-unselectable-date';
				}

				return dateData;
			},
			onSelect: function( dates ) {
				self.form.onDatepickChange();
			},
			pickerClass: 'mphb-datepick-popup mphb-check-out-datepick ' + MPHB._data.settings.datepickerClass,
		};
	},
	/**
	 * @param {Date} date
	 */
	setDate: function( date ) {

		if ( date == null ) {
			return this._super( date );
		}

		if ( !MPHB.HotelDataManager.myThis.globalRule.isCheckOutSatisfy( date ) ) {
			return this._super( null );
		}

		if ( !MPHB.HotelDataManager.myThis.dateRules.canCheckOut( date ) ) {
			return this._super( null );
		}

		return this._super( date );
	},
} );
MPHB.RoomTypeData = can.Construct.extend( {}, {
	id: null,
	bookedDates: {},
	blockedDates: {}, // Blocked by custom rules. { %Date%: %Blocked rooms count% }
	havePriceDates: {},
	activeRoomsCount: 0,
	/**
	 *
	 * @param {Object}	data
	 * @param {Object}	data.bookedDates
	 * @param {Object}	data.havePriceDates
	 * @param {int}		data.activeRoomsCount
	 * @returns {undefined}
	 */
	init: function( id, data ) {
		this.id = id;
		this.setRoomsCount( data.activeRoomsCount );
		this.setDates( data.dates );
	},
	update: function( data ) {
		if ( data.hasOwnProperty( 'activeRoomsCount' ) ) {
			this.setRoomsCount( data.activeRoomsCount );
		}

		if ( data.hasOwnProperty( 'dates' ) ) {
			this.setDates( data.dates );
		}

		$( window ).trigger( 'mphb-update-room-type-data-' + this.id );
	},
	/**
	 *
	 * @param {int} count
	 * @returns {undefined}
	 */
	setRoomsCount: function( count ) {
		this.activeRoomsCount = count;
	},
	/**
	 *
	 * @param {Object} dates
	 * @param {Object} dates.bookedDates
	 * @param {Object} dates.havePriceDates
	 * @returns {undefined}
	 */
	setDates: function( dates ) {
		this.bookedDates = dates.hasOwnProperty( 'booked' ) ? dates.booked : {};
		this.blockedDates = dates.hasOwnProperty( 'blocked' ) ? dates.blocked : {};
		this.havePriceDates = dates.hasOwnProperty( 'havePrice' ) ? dates.havePrice : {};
	},
	blockAllRoomsOnDate: function( dateFormatted ){
		this.blockedDates[dateFormatted] = this.activeRoomsCount;
	},
	/**
	 *
	 * @param {Date} dateFrom
	 * @param {Date} stopDate
	 * @returns {Date|false} Nearest locked room date if exists or false otherwise.
	 */
	getNearestLockedDate: function( dateFrom, stopDate ) {
		var nearestDate = stopDate;
		var self = this;

		var dateFromFormatted = $.datepick.formatDate( 'yyyy-mm-dd', dateFrom );
		var stopDateFormatted = $.datepick.formatDate( 'yyyy-mm-dd', stopDate );

		$.each( self.getLockedDates(), function( dateFormatted, lockedRoomsCount ) {

			if ( stopDateFormatted < dateFormatted ) {
				return false;
			}

			if ( dateFromFormatted > dateFormatted ) {
				return true;
			}

			if ( lockedRoomsCount >= self.activeRoomsCount ) {
				nearestDate = $.datepick.parseDate( 'yyyy-mm-dd', dateFormatted );
				return false;
			}

		} );
		return nearestDate;
	},
	/**
	 *
	 * @param {Date} dateFrom
	 * @param {Date} stopDate
	 * @returns {Date}
	 */
	getNearestHaveNotPriceDate: function( dateFrom, stopDate ) {
		var nearestDate = MPHB.Utils.cloneDate( stopDate );
		var expectedDate = MPHB.Utils.cloneDate( dateFrom );

		while ( MPHB.Utils.formatDateToCompare( expectedDate ) <= MPHB.Utils.formatDateToCompare( stopDate ) ) {
			if ( !this.hasPriceForDate( expectedDate ) ) {
				nearestDate = expectedDate;
				break;
			}
			expectedDate = $.datepick.add( expectedDate, 1, 'd' );
		}

		return nearestDate;
	},
	/**
	 *
	 * @returns {Object}
	 */
	getLockedDates: function() {
		var dates = $.extend( {}, this.bookedDates );
		$.each( this.blockedDates, function( dateFormatted, blockedRoomsCount ) {
			if ( !dates.hasOwnProperty( dateFormatted ) ) {
				dates[dateFormatted] = blockedRoomsCount;
			} else {
				dates[dateFormatted] += blockedRoomsCount;
			}
		} );
		return dates;
	},
	/**
	 *
	 * @returns {Object}
	 */
	getHavePriceDates: function() {
		var dates = {};
		return $.extend( dates, this.havePriceDates );
	},
	/**
	 *
	 * @param {Date}
	 * @returns {String}
	 */
	getDateStatus: function( date ) {
		var status = MPHB.HotelDataManager.ROOM_STATUS_AVAILABLE;

		if ( this.isEarlierThanToday( date ) ) {
			status = MPHB.HotelDataManager.ROOM_STATUS_PAST;
		} else if ( this.isDateBooked( date ) ) {
			status = MPHB.HotelDataManager.ROOM_STATUS_BOOKED;
		} else if ( !this.hasPriceForDate( date ) ) {
			status = MPHB.HotelDataManager.ROOM_STATUS_NOT_AVAILABLE;
		} else if ( !this.getAvailableRoomsCount( date ) ) {
			status = MPHB.HotelDataManager.ROOM_STATUS_NOT_AVAILABLE;
		}

		return status;
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {Boolean}
	 */
	isDateBooked: function( date ) {
		var dateFormatted = $.datepick.formatDate( 'yyyy-mm-dd', date );
		return this.bookedDates.hasOwnProperty( dateFormatted ) && this.bookedDates[dateFormatted] >= this.activeRoomsCount;
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {Boolean}
	 */
	hasPriceForDate: function( date ) {
		var dateFormatted = $.datepick.formatDate( 'yyyy-mm-dd', date );
		return $.inArray( dateFormatted, this.havePriceDates ) !== -1;
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {int}
	 */
	getAvailableRoomsCount: function( date ) {
		var dateFormatted = $.datepick.formatDate( 'yyyy-mm-dd', date );
		var count = this.activeRoomsCount;

		if ( this.bookedDates.hasOwnProperty( dateFormatted ) ) {
			count -= this.bookedDates[dateFormatted];
		}

		if ( this.blockedDates.hasOwnProperty( dateFormatted ) ) {
			count -= this.blockedDates[dateFormatted];
		}

		if ( count < 0 ) {
			count = 0;
		}

		return count;
	},
	/**
	 *
	 * @param {Object} dateData
	 * @param {Date} date
	 * @returns {Object}
	 */
	fillDateData: function( dateData, date ) {
		var status = this.getDateStatus( date );
		var titles = [ ];
		var classes = [ ];

		switch ( status ) {
			case MPHB.HotelDataManager.ROOM_STATUS_PAST:
				classes.push( 'mphb-past-date' );
				titles.push( MPHB._data.translations.past );
				break;
			case MPHB.HotelDataManager.ROOM_STATUS_AVAILABLE:
				classes.push( 'mphb-available-date' );
				titles.push( MPHB._data.translations.available + '(' + this.getAvailableRoomsCount( date ) + ')' );
				break;
			case MPHB.HotelDataManager.ROOM_STATUS_NOT_AVAILABLE:
				classes.push( 'mphb-not-available-date' );
				titles.push( MPHB._data.translations.notAvailable );
				break;
			case MPHB.HotelDataManager.ROOM_STATUS_BOOKED:
				classes.push( 'mphb-booked-date' );
				titles.push( MPHB._data.translations.booked );
				break;
		}

		dateData.dateClass += (dateData.dateClass.length ? ' ' : '') + classes.join( ' ' );
		dateData.title += (dateData.title.length ? ', ' : '') + titles.join( ', ' );

		dateData = MPHB.HotelDataManager.myThis.fillDateCellData( dateData, date );

		return dateData;
	},
	appendRulesToTitle: function( date, title ) {
		var rulesTitles = [ ];

		if ( !MPHB.HotelDataManager.myThis.dateRules.canStayIn( date ) ) {
			rulesTitles.push( MPHB._data.translations.notStayIn );
		}
		if ( !MPHB.HotelDataManager.myThis.dateRules.canCheckIn( date ) ) {
			rulesTitles.push( MPHB._data.translations.notCheckIn );
		}
		if ( !MPHB.HotelDataManager.myThis.dateRules.canCheckOut( date ) ) {
			rulesTitles.push( MPHB._data.translations.notCheckOut );
		}

		if ( rulesTitles.length ) {
			title += ' ' + MPHB._data.translations.rules + ' ' + rulesTitles.join( ', ' );
		}

		return title;
	},
	/**
	 *
	 * @param {Date} date
	 * @returns {Boolean}
	 */
	isEarlierThanToday: function( date ) {
		return MPHB.Utils.formatDateToCompare( date ) < MPHB.Utils.formatDateToCompare( MPHB.HotelDataManager.myThis.today );
	},
} );
/**
 *
 * @requires ./../datepicker.js
 */
MPHB.SearchCheckInDatepicker = MPHB.Datepicker.extend( {}, {
	/**
	 *
	 * @returns {Object}
	 */
	getDatepickSettings: function() {
		var self = this;
		return {
			onSelect: function( dates ) {
				self.form.updateCheckOutLimitations();
			},
			onDate: function( date, current ) {
				var dateData = {
					dateClass: 'mphb-date-cell',
					selectable: false,
					title: ''
				};

				if ( current ) {

					var canCheckIn = MPHB.HotelDataManager.myThis.globalRule.isCheckInSatisfy( date ) &&
						MPHB.HotelDataManager.myThis.dateRules.canCheckIn( date );

					if ( canCheckIn ) {
						dateData.selectable = true;
					}

					dateData = MPHB.HotelDataManager.myThis.fillDateCellData( dateData, date );

				} else {
					dateData.dateClass += ' mphb-extra-date';
				}

				if ( dateData.selectable ) {
					dateData.dateClass += ' mphb-selectable-date';
				} else {
					dateData.dateClass += ' mphb-unselectable-date';
				}

				return dateData;
			},
			pickerClass: 'mphb-datepick-popup mphb-check-in-datepick ' + MPHB._data.settings.datepickerClass,
		};
	}
} );
/**
 *
 * @requires ./../datepicker.js
 */
MPHB.SearchCheckOutDatepicker = MPHB.Datepicker.extend( {}, {
	/**
	 *
	 * @returns {Object}
	 */
	getDatepickSettings: function() {
		var self = this;
		return {
			onDate: function( date, current ) {
				var dateData = {
					dateClass: 'mphb-date-cell',
					selectable: false,
					title: ''
				};

				if ( current ) {

					var checkInDate = self.form.checkInDatepicker.getDate();
					var earlierThanMin = self.getMinDate() !== null && MPHB.Utils.formatDateToCompare( date ) < MPHB.Utils.formatDateToCompare( self.getMinDate() );
					var laterThanMax = self.getMaxDate() !== null && MPHB.Utils.formatDateToCompare( date ) > MPHB.Utils.formatDateToCompare( self.getMaxDate() );

					if ( checkInDate !== null && MPHB.Utils.formatDateToCompare( date ) === MPHB.Utils.formatDateToCompare( checkInDate ) ) {
						dateData.dateClass += ' mphb-check-in-date';
						dateData.title += MPHB._data.translations.checkInDate;
					}

					if ( earlierThanMin ) {
						if ( MPHB.Utils.formatDateToCompare( date ) < MPHB.Utils.formatDateToCompare( checkInDate ) ) {
							dateData.dateClass += ' mphb-earlier-min-date mphb-earlier-check-in-date';
						} else {
							dateData.dateClass += ' mphb-earlier-min-date';
							dateData.title += (dateData.title.length ? ' ' : '') + MPHB._data.translations.lessThanMinDaysStay;
						}
					}

					if ( laterThanMax ) {
						var maxStayDate = checkInDate ? MPHB.HotelDataManager.myThis.globalRule.getMaxCheckOutDate( checkInDate ) : false;
						if ( !maxStayDate || MPHB.Utils.formatDateToCompare( date ) < MPHB.Utils.formatDateToCompare( maxStayDate ) ) {
							dateData.title += (dateData.title.length ? ' ' : '') + MPHB._data.translations.laterThanMaxDate;
						} else {
							dateData.title += (dateData.title.length ? ' ' : '') + MPHB._data.translations.moreThanMaxDaysStay;
						}
						dateData.dateClass += ' mphb-later-max-date';
					}

					dateData = MPHB.HotelDataManager.myThis.fillDateCellData( dateData, date );

					var canCheckOut = !earlierThanMin && !laterThanMax &&
						MPHB.HotelDataManager.myThis.globalRule.isCheckOutSatisfy( date ) &&
						MPHB.HotelDataManager.myThis.dateRules.canCheckOut( date );

					if ( canCheckOut ) {
						dateData.selectable = true;
					}

				} else {
					dateData.dateClass += ' mphb-extra-date';
				}

				if ( dateData.selectable ) {
					dateData.dateClass += ' mphb-selectable-date';
				} else {
					dateData.dateClass += ' mphb-unselectable-date';
				}

				return dateData;
			},
			pickerClass: 'mphb-datepick-popup mphb-check-out-datepick ' + MPHB._data.settings.datepickerClass,
		};
	}
} );
MPHB.SearchForm = can.Control.extend( {}, {
	checkInDatepickerEl: null,
	checkOutDatepickerEl: null,
	checkInDatepicker: null,
	checkOutDatepicker: null,
	init: function( el, args ) {

		this.checkInDatepickerEl = this.element.find( '.mphb-datepick[name=mphb_check_in_date]' );
		this.checkOutDatepickerEl = this.element.find( '.mphb-datepick[name=mphb_check_out_date]' );

		this.checkInDatepicker = new MPHB.SearchCheckInDatepicker( this.checkInDatepickerEl, {'form': this} );
		this.checkOutDatepicker = new MPHB.SearchCheckOutDatepicker( this.checkOutDatepickerEl, {'form': this} );

		// Enable reservation rules on check-out date
		if ( this.checkInDatepicker.getDate() ) {
			this.updateCheckOutLimitations();
		}

	},
	/**
	 *
	 * @param {bool} isSetDate
	 * @returns {undefined}
	 */
	updateCheckOutLimitations: function( setDate ) {
		if ( typeof setDate === 'undefined' ) {
			setDate = true;
		}
		var limitations = this.retrieveCheckOutLimitations( this.checkInDatepicker.getDate(), this.checkOutDatepicker.getDate() );

		this.checkOutDatepicker.setOption( 'minDate', limitations.minDate );
		this.checkOutDatepicker.setOption( 'maxDate', limitations.maxDate );
		this.checkOutDatepicker.setDate( setDate ? limitations.date : null );
	},
	retrieveCheckOutLimitations: function( checkInDate, checkOutDate ) {

		var minDate = MPHB.HotelDataManager.myThis.today;
		var maxDate = null;
		var recommendedDate = null;

		if ( checkInDate !== null ) {
			var minDate = MPHB.HotelDataManager.myThis.getSeasonMinCheckOutDate( checkInDate );

			var maxDate = MPHB.HotelDataManager.myThis.getSeasonMaxCheckOutDate( checkInDate );
			maxDate = MPHB.HotelDataManager.myThis.dateRules.getNearestNotStayInDate( checkInDate, maxDate );

			if ( this.isCheckOutDateNotValid( checkInDate, checkOutDate, minDate, maxDate ) ) {
				recommendedDate = this.retrieveRecommendedCheckOutDate( checkInDate, minDate, maxDate );
			} else {
				recommendedDate = checkOutDate;
			}

		}

		return {
			minDate: minDate,
			maxDate: maxDate,
			date: recommendedDate
		};
	},
	retrieveRecommendedCheckOutDate: function( checkInDate, minDate, maxDate ) {
		var recommendedDate = null;
		var expectedDate = MPHB.Utils.cloneDate( minDate );

		while ( MPHB.Utils.formatDateToCompare( expectedDate ) <= MPHB.Utils.formatDateToCompare( maxDate ) ) {
			if ( !this.isCheckOutDateNotValid( checkInDate, expectedDate, minDate, maxDate ) ) {
				recommendedDate = expectedDate;
				break;
			}
			expectedDate = $.datepick.add( expectedDate, 1, 'd' );
		}

		return recommendedDate;

	},
	isCheckOutDateNotValid: function( checkInDate, checkOutDate, minDate, maxDate ) {
		return checkOutDate === null
			|| MPHB.Utils.formatDateToCompare( checkOutDate ) < MPHB.Utils.formatDateToCompare( minDate )
			|| MPHB.Utils.formatDateToCompare( checkOutDate ) > MPHB.Utils.formatDateToCompare( maxDate )
			|| !MPHB.HotelDataManager.myThis.isCheckOutSatisfySeason( checkInDate, checkOutDate )
			|| !MPHB.HotelDataManager.myThis.dateRules.canCheckOut( checkOutDate );
	}

} );
MPHB.RoomBookSection = can.Control.extend( {}, {
	roomTypeId: null,
	roomTitle: '',
	roomPrice: 0,
	quantitySelect: null,
	bookButton: null,
	confirmButton: null,
	removeButton: null,
	messageHolder: null,
	messageWrapper: null,
	form: null,
	init: function( el, args ) {
		this.reservationCart = args.reservationCart;
		this.roomTypeId = parseInt( el.attr( 'data-room-type-id' ) );
		this.roomTitle = el.attr( 'data-room-type-title' );
		this.roomPrice = parseFloat( el.attr( 'data-room-price' ) );
		this.confirmButton = el.find( '.mphb-confirm-reservation' );
		this.quantitySelect = el.find( '.mphb-rooms-quantity' );
		this.messageWrapper = el.find( '.mphb-rooms-reservation-message-wrapper' );
		this.messageHolder = el.find( '.mphb-rooms-reservation-message' );
	},
	/**
	 *
	 * @returns {int}
	 */
	getRoomTypeId: function() {
		return this.roomTypeId;
	},
	/**
	 *
	 * @returns {Number}
	 */
	getPrice: function() {
		return this.roomPrice;
	},
	'.mphb-book-button click': function( el, e ) {
		e.preventDefault();
		e.stopPropagation();

		var quantity = parseInt( this.quantitySelect.val() );
		this.reservationCart.addToCart( this.roomTypeId, quantity );

		var messagePattern = ( 1 == quantity ) ? MPHB._data.translations.roomsAddedToReservation_singular : MPHB._data.translations.roomsAddedToReservation_plural;
		var message = messagePattern.replace( '%1$d', quantity ).replace( '%2$s', this.roomTitle );
		this.messageHolder.html( message );

		this.element.addClass( 'mphb-rooms-added' );
	},
	'.mphb-remove-from-reservation click': function( el, e ) {
		e.preventDefault();
		e.stopPropagation();

		this.reservationCart.removeFromCart( this.roomTypeId );

		this.messageHolder.empty();
		this.element.removeClass( 'mphb-rooms-added' );
	},
	'.mphb-confirm-reservation click': function( el, e ) {
		e.preventDefault();
		e.stopPropagation();
		this.reservationCart.confirmReservation();
	}
} );
/**
 *
 * @requires ./room-book-section.js
 */
MPHB.ReservationCart = can.Control.extend( {}, {
	cartForm: null,
	cartDetails: null,
	roomBookSections: {},
	cartContents: {},
	init: function( el, args ) {
		this.cartForm = el.find( '#mphb-reservation-cart' );
		this.cartDetails = el.find( '.mphb-reservation-details' );
		this.initRoomBookSections( el.find( '.mphb-reserve-room-section' ) );
	},
	initRoomBookSections: function( sections ) {
		var self = this;
		var bookSection;
		$.each( sections, function( index, roomSection ) {
			bookSection = new MPHB.RoomBookSection( $( roomSection ), {
				reservationCart: self,
			} );
			self.roomBookSections[bookSection.getRoomTypeId()] = bookSection;
		} );
	},
	addToCart: function( roomTypeId, quantity ) {
		this.cartContents[roomTypeId] = quantity;
		this.updateCartView();
		this.updateCartInputs();
	},
	removeFromCart: function( roomTypeId ) {
		delete this.cartContents[roomTypeId];
		this.updateCartView();
		this.updateCartInputs();
	},
	calcRoomsInCart: function() {
		var count = 0;

		$.each( this.cartContents, function( roomTypeId, quantity ) {
			count += quantity;
		} );

		return count;
	},
	calcTotalPrice: function() {
		var total = 0;
		var price = 0;
		var self = this;

		$.each( this.cartContents, function( roomTypeId, quantity ) {
			price = self.roomBookSections[roomTypeId].getPrice();
			total += price * quantity;
		} );

		return total;
	},
	updateCartView: function() {
		if ( !$.isEmptyObject( this.cartContents ) ) {

			var roomsCount = this.calcRoomsInCart();
			var messageTemplate = ( 1 == roomsCount ) ? MPHB._data.translations.countRoomsSelected_singular : MPHB._data.translations.countRoomsSelected_plural;
			var cartMessage = messageTemplate.replace( '%s', roomsCount )
			this.cartDetails.find( '.mphb-cart-message' ).html( cartMessage );

			var total = this.calcTotalPrice();
			var totalMessage = MPHB._data.translations.priceFormat.replace( '%s', total );
			this.cartDetails.find( '.mphb-cart-total-price>.mphb-cart-total-price-value' ).html( totalMessage );

			this.cartForm.removeClass( 'mphb-empty-cart' );
		} else {
			this.cartForm.addClass( 'mphb-empty-cart' );
		}
	},
	updateCartInputs: function() {
		// empty inputs
		this.cartForm.find( '[name^="mphb_rooms_details"]' ).remove();

		var self = this;
		$.each( this.cartContents, function( roomTypeId, quantity ) {
			var input = $( '<input />', {
				name: 'mphb_rooms_details[' + roomTypeId + ']',
				type: 'hidden',
				value: quantity

			} );
			self.cartForm.prepend( input );
		} );
	},
	confirmReservation: function() {
		this.cartForm.submit();
	},
} );


new MPHB.HotelDataManager( MPHB._data );

if ( MPHB._data.page.isCheckoutPage ) {
	new MPHB.CheckoutForm( $( '.mphb_sc_checkout-form' ) );
}

if ( MPHB._data.page.isSearchResultsPage ) {
	new MPHB.ReservationCart( $( '.mphb_sc_search_results-wrapper' ) );
}

var calendars = $( '.mphb-calendar.mphb-datepick' );
$.each( calendars, function( index, calendarEl ) {
	new MPHB.RoomTypeCalendar( $( calendarEl ) );
} );

var reservationForms = $( '.mphb-booking-form' );
$.each( reservationForms, function( index, formEl ) {
	new MPHB.ReservationForm( $( formEl ) );
} );

var searchForms = $( 'form.mphb_sc_search-form,form.mphb_widget_search-form' );
$.each( searchForms, function( index, formEl ) {
	new MPHB.SearchForm( $( formEl ) );
} );

var flexsliderGalleries = $( '.mphb-flexslider-gallery-wrapper' );
$.each( flexsliderGalleries, function( index, flexsliderGallery ) {
	new MPHB.FlexsliderGallery( flexsliderGallery );
} );

// Fix for kbwood/datepick (function show() -> $.ui.version.substring(2))
if ( $.ui == undefined ) {
	$.ui = {};
}
if ( $.ui.version == undefined ) {
	$.ui.version = '1.5-';
}

	} );
})( jQuery );