/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	
	window.Dashboard = module.exports = {

	    el: '#devos-dashboard',

	    data: function () {
	        return {
	            data: window.$data,
	            config: window.$config,
	            currentView: 'dashboard'
	        };
	    },

	    created: function () {
	        UIkit.$win.on('hashchange', function () {
	            this.checkView()
	        }.bind(this));
	        this.checkView();
	    },

	    ready: function () {
	        //this.$nextTick(function () {
	        //    UIkit.init(UIkit.$(this.$el));
	        //});
	    },

	    methods: {
	        checkView: function () {
	            var hash = location.hash.replace('#', '');
	            if (hash && hash !== this.currentView) {
	                this.setView(hash);
	            }
	        },
	        setView: function (view) {
	            if (this.$options.components[view]) {
	                this.currentView = view;
	                window.history.pushState({}, '', this.config.current + '#' + view)
	            } else {
	                UIkit.notify('Component niet gevonden', 'danger');
	            }
	        }
	    },

	    components: {
	        'dashboard': __webpack_require__(3),
	        'verzendingen': __webpack_require__(4),
	        'afzenders': __webpack_require__(5)
	    }

	};

	UIkit.$(function () {

	    new Vue(module.exports);

	});


/***/ },
/* 1 */,
/* 2 */
/***/ function(module, exports) {

	var __vue_template__ = "<div>\n\n    <div v-if=\"error\" class=\"uk-alert uk-alert-danger\">{{ error }}</div>\n\n    <div class=\"uk-flex uk-flex-space-between uk-flex-middle\">\n        <div>\n            <div class=\"uk-form-icon\">\n                <i class=\"uk-icon-search\"></i>\n                <input class=\"uk-margin-remove uk-form-width-small\" type=\"search\" v-model=\"filter.search\" debounce=\"500\">\n            </div>\n        </div>\n        <div>\n            <small>{{ total }} verzending<span v-show=\"total != 1\">en</span></small>\n        </div>\n        <div>\n            <button class=\"uk-button\" @click=\"editShipment(0)\">\n                <i v-spinner=\"editloading[0]\" icon=\"plus\"></i>Nieuwe verzending</button>\n        </div>\n    </div>\n\n\n    <table class=\"uk-table uk-table-hover\" v-show=\"shipments\">\n        <thead>\n        <tr>\n            <th>Pakket</th>\n            <th class=\"uk-width-1-4\">Ontvanger</th>\n            <th class=\"uk-width-1-4\">Afzender</th>\n            <th class=\"uk-width-1-5\">Status</th>\n        </tr>\n        </thead>\n        <tfoot>\n        <tr>\n            <td colspan=\"4\">\n                <v-pagination :page.sync=\"page\" :pages=\"pages\" v-show=\"pages > 1\"></v-pagination>\n            </td>\n        </tr>\n        </tfoot>\n        <tbody>\n        <tr v-for=\"shipment in shipments | orderBy filter.order direction | count\">\n            <td>\n                <dl>\n                    <dd>\n                        <i class=\"uk-icon-cubes uk-icon-justify uk-margin-small-right\" title=\"Product type\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ getValueLabel('product_short_description', shipment.product_short_description) }}</span>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon-bolt uk-icon-justify uk-margin-small-right\" title=\"Express\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ getValueLabel('express_flag', shipment.data.express_flag) }}</span>\n                        <i v-show=\"shipment.data.express_service_flag\" class=\"uk-icon-flag uk-text-danger uk-margin-small-left\" title=\"Express Service aan\" data-uk-tooltip=\"{delay: 200}\"></i>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon-ticket uk-icon-justify uk-margin-small-right\" title=\"NL pakket nummer\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.domestic_parcel_number_nl }}</span>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon-barcode uk-icon-justify uk-margin-small-right\" title=\"GLS pakket nummer\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.gls_parcel_number }}</span>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon-circle uk-icon-justify uk-margin-small-right\" title=\"Gewicht\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.parcel_weight }} kg</span>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon-clock-o uk-icon-justify uk-margin-small-right\" title=\"Datum\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.date_of_shipping | date }}</span>\n                    </dd>\n                </dl>\n            </td>\n            <td>\n                <dl>\n                    <dd>\n                        <i class=\"uk-icon-user uk-icon-justify uk-margin-small-right\" title=\"Ontvanger naam 1\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.receiver_name_1 }}</span>\n                    </dd>\n                    <dd v-if=\"shipment.receiver_name_2\">\n                        <i class=\"uk-icon- uk-icon-justify uk-margin-small-right\" title=\"Ontvanger naam 2\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.receiver_name_2 }}</span>\n                    </dd>\n                    <dd v-if=\"shipment.receiver_name_3\">\n                        <i class=\"uk-icon- uk-icon-justify uk-margin-small-right\" title=\"Ontvanger naam 3\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.receiver_name_3 }}</span>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon-building-o uk-icon-justify uk-margin-small-right\" title=\"Ontvanger adres\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.receiver_street }}</span>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon- uk-icon-justify uk-margin-small-right\" title=\"Ontvanger postcode\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.receiver_zip_code }}</span>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon- uk-icon-justify uk-margin-small-right\" title=\"Ontvanger plaats\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.receiver_place }}</span>\n                    </dd>\n                    <dd v-if=\"shipment.receiver_contact\">\n                        <i class=\"uk-icon-user uk-icon-justify uk-margin-small-right\" title=\"Contact ontvanger\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.receiver_contact }}</span>\n                    </dd>\n                    <dd v-if=\"shipment.receiver_phone\">\n                        <i class=\"uk-icon-phone uk-icon-justify uk-margin-small-right\" title=\"Telefoon ontvanger\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.receiver_phone }}</span>\n                    </dd>\n                </dl>\n            </td>\n            <td>\n                <dl>\n                    <dd>\n                        <i class=\"uk-icon-tag uk-icon-justify uk-margin-small-right\" title=\"Klantreferentie\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <strong>{{ shipment.customer_reference }}</strong>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon-user uk-icon-justify uk-margin-small-right\" title=\"Afzender naam 1\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.sender_name_1 }}</span>\n                    </dd>\n                    <dd v-if=\"shipment.sender_name_2\">\n                        <i class=\"uk-icon- uk-icon-justify uk-margin-small-right\" title=\"Afzender naam 2\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.sender_name_2 }}</span>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon-building-o uk-icon-justify uk-margin-small-right\" title=\"Afzender adres\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.sender_street }}</span>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon- uk-icon-justify uk-margin-small-right\" title=\"Afzender postcode\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.sender_zip }}</span>\n                    </dd>\n                    <dd>\n                        <i class=\"uk-icon- uk-icon-justify uk-margin-small-right\" title=\"Afzender plaats\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ shipment.sender_city }}</span>\n                    </dd>\n                </dl>\n            </td>\n            <td>\n                {{ shipment.state }}<br>\n                <ul class=\"uk-list\">\n                    <li>\n                        <a v-show=\"shipment.data.track_trace\" :href=\"shipment.data.track_trace\" target=\"_blank\">\n                            <i class=\"uk-icon-external-link uk-margin-small-right\"></i>\n                            Track &amp; Trace</a>\n                    </li>\n                    <li v-show=\"shipment.pdf_url\" class=\"uk-text-truncate\">\n                        <a :href=\"shipment.pdf_url\">\n                            <i class=\"uk-icon-file-pdf-o uk-margin-small-right\"></i>\n                            Etiket</a>\n                    </li>\n                </ul>\n\n                <button class=\"uk-button uk-button-small\" @click=\"editShipment(shipment.id)\">\n                    <i v-spinner=\"editloading[shipment.id]\" icon=\"edit\"></i>Bewerken</button><br>\n            </td>\n        </tr>\n        <tr v-show=\"!count\">\n            <td colspan=\"4\" class=\"uk-text-center\">\n                <div class=\"uk-alert\">Geen verzendingen gevonden.</div>\n            </td>\n        </tr>\n        </tbody>\n    </table>\n    <div v-else=\"\" class=\"uk-text-center\"><i class=\"uk-icon-circle-o-notch uk-icon-spin\"></i></div>\n\n\n    <v-modal v-ref:editshipmentmodal=\"\" :large=\"true\" :closed=\"cancelEdit\">\n        <div class=\"uk-modal-header\">\n            <div class=\"uk-flex\">\n                <h3 class=\"uk-flex-item-1\">GLS verzending</h3>\n                <div class=\"uk-flex uk-flex-middle uk-h5\">\n                    <div v-if=\"shipment.sender_id\" class=\"uk-margin-left\">\n                        <i class=\"uk-icon-user uk-margin-small-right\" title=\"Afzender\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ senders[shipment.sender_id].sender_name_1 }}</span>\n                    </div>\n                    <div class=\"uk-margin-left\">\n                        <i class=\"uk-icon-cubes uk-margin-small-right\" title=\"Product type\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ getValueLabel('product_short_description', shipment.product_short_description) }}</span>\n                    </div>\n                    <div class=\"uk-margin-left\">\n                        <i class=\"uk-icon-bolt uk-margin-small-right\" title=\"Express\" data-uk-tooltip=\"{delay: 200}\"></i>\n                        <span>{{ getValueLabel('express_flag', shipment.data.express_flag) }}</span>\n                        <i v-show=\"shipment.data.express_service_flag\" class=\"uk-icon-flag uk-text-danger uk-margin-small-left\" title=\"Express Service aan\" data-uk-tooltip=\"{delay: 200}\"></i>\n                    </div>\n                </div>\n            </div>\n        </div>\n\n        <form class=\"uk-form\" @submit.prevent=\"submitForm\">\n\n            <ul class=\"uk-tab\">\n                <li :class=\"{'uk-active': currentTab == 'pakket'}\"><a href=\"#\" @click.prevent=\"currentTab = 'pakket'\">\n                    <i class=\"uk-icon-cubes uk-margin-small-right\"></i>Pakket</a></li>\n                <li :class=\"{'uk-active': currentTab == 'versturen'}\"><a href=\"#\" @click.prevent=\"currentTab = 'versturen'\">\n                    <i class=\"uk-icon-paper-plane-o uk-margin-small-right\"></i>Versturen</a></li>\n                <li :class=\"{'uk-active': currentTab == 'instellingen'}\"><a href=\"#\" @click.prevent=\"currentTab = 'instellingen'\">\n                    <i class=\"uk-icon-cogs uk-margin-small-right\"></i>Instellingen</a></li>\n            </ul>\n\n            <div id=\"shipment-tabs\" class=\"uk-margin\">\n                <div v-show=\"currentTab == 'pakket'\">\n\n                    <div class=\"uk-grid uk-grid-width-medium-1-3 uk-grid-small uk-form-stacked uk-flex-center\">\n                        <div class=\"uk-text-center\">\n                            <label class=\"uk-form-label\" for=\"form-parcel_weight\">Gewicht pakket *</label>\n                            <div class=\"uk-form-controls\">\n                                <input v-model=\"shipment.parcel_weight\" id=\"form-parcel_weight\" type=\"number\" class=\"uk-form-width-medium uk-text-right\" min=\"0\" step=\"0.1\" required=\"\" number=\"\">\n                            </div>\n                        </div>\n                        <div class=\"uk-text-center\">\n                            <label class=\"uk-form-label\" for=\"form-parcel_sequence\">Pakket reeks</label>\n                            <div class=\"uk-form-controls\">\n                                <input v-model=\"shipment.parcel_sequence\" id=\"form-parcel_sequence\" type=\"number\" class=\"uk-form-width-medium uk-text-right\" min=\"0\" required=\"\" number=\"\">\n                            </div>\n                        </div>\n                        <div class=\"uk-text-center\">\n                            <label class=\"uk-form-label\" for=\"form-parcel_quantity\">Pakket aantal</label>\n                            <div class=\"uk-form-controls\">\n                                <input v-model=\"shipment.parcel_quantity\" id=\"form-parcel_quantity\" type=\"number\" class=\"uk-form-width-medium uk-text-right\" min=\"0\" required=\"\" number=\"\">\n                            </div>\n                        </div>\n                    </div>\n                    <hr>\n                    <div class=\"uk-grid uk-form-horizontal\">\n                        <div class=\"uk-width-medium-1-2\">\n                            <fields :config=\"$options.fields1\" :model.sync=\"shipment\" template=\"formrow\"></fields>\n                        </div>\n                        <div class=\"uk-width-medium-1-2\">\n                            <fields :config=\"$options.fields2\" :model.sync=\"shipment\" template=\"formrow\"></fields>\n\n                            <div v-show=\"shipment.gls_parcel_number == 0\" class=\"uk-margin-large uk-text-center\">\n                                <button class=\"uk-button uk-button-success uk-button-large\" @click=\"task = 'sendAndSave'\">\n                                    <i v-spinner=\"sending\" icon=\"paper-plane-o\"></i>\n                                    Opslaan en aanmelden\n                                </button>\n                            </div>\n\n                        </div>\n                    </div>\n\n                </div>\n                <div v-show=\"currentTab == 'versturen'\">\n\n                    <div class=\"uk-grid uk-grid-small uk-form-stacked\">\n                        <div class=\"uk-width-medium-1-2\">\n                            <dl class=\"uk-description-list-horizontal\">\n                                <fields :config=\"$options.fields3\" :model.sync=\"shipment\" template=\"descriptionlist\"></fields>\n                            </dl>\n                        </div>\n                        <div class=\"uk-width-medium-1-2 uk-flex uk-flex-column uk-flex-wrap-space-between\">\n\n                            <div>\n                                <div v-show=\"shipment.gls_parcel_number == 0\" class=\"uk-text-center\">\n                                    <button class=\"uk-button uk-button-success uk-button-large\" @click=\"task = 'sendAndSave'\">\n                                        <i v-spinner=\"sending\" icon=\"paper-plane-o\"></i>\n                                        Opslaan en aanmelden\n                                    </button>\n                                </div>\n                                <div v-else=\"\">\n                                    <dl>\n                                        <dt>Track &amp; Trace</dt>\n                                        <dd>\n                                            <a v-show=\"shipment.data.track_trace\" :href=\"shipment.data.track_trace\" target=\"_blank\" class=\"uk-display-block uk-text-truncate\">\n                                                <i class=\"uk-icon-external-link uk-margin-small-right\"></i>\n                                                {{ shipment.data.track_trace }}</a>\n                                        </dd>\n                                        <dt v-show=\"shipment.pdf_url\">Etiket</dt>\n                                        <dd v-show=\"shipment.pdf_url\">\n                                            <a :href=\"shipment.pdf_url\" class=\"uk-display-block uk-text-truncate\">\n                                                <i class=\"uk-icon-file-pdf-o uk-margin-small-right\"></i>\n                                                {{ shipment.pdf_url }}</a>\n                                        </dd>\n                                    </dl>\n                                </div>\n\n                                <div v-if=\"progresserror\" class=\"uk-alert uk-alert-danger\">{{ progresserror }}</div>\n\n                                <div v-if=\"progressmessage\" class=\"uk-alert\" :class=\"{'uk-alert-success': progress == 100}\">{{ progressmessage }}</div>\n\n                                <div v-if=\"sending\" class=\"uk-progress uk-progress-striped uk-active\">\n                                    <div class=\"uk-progress-bar\" :style=\"{'width': progress +'%'}\"></div>\n                                </div>\n\n                            </div>\n\n                        </div>\n                    </div>\n\n                </div>\n                <div v-show=\"currentTab == 'instellingen'\">\n\n                    <div class=\"uk-grid uk-grid-width-medium-1-2 uk-grid-small uk-form-horizontal\">\n                        <div>\n\n                            <div class=\"uk-form-row\">\n                                <label class=\"uk-form-label\" for=\"form-sender_id\">Afzender</label>\n                                <div class=\"uk-form-controls\">\n                                    <select v-model=\"shipment.sender_id\" id=\"form-sender_id\" class=\"uk-form-width-medium\">\n                                        <option v-for=\"sender in senders\" :value=\"sender.id\">{{ sender.sender_name_1 }}</option>\n                                    </select>\n                                </div>\n                            </div>\n\n                            <fields :config=\"$options.fields4\" :model.sync=\"shipment\" template=\"formrow\"></fields>\n                        </div>\n                        <div>\n                            <fields :config=\"$options.fields5\" :model.sync=\"shipment\" template=\"formrow\"></fields>\n                        </div>\n                    </div>\n\n                </div>\n            </div>\n            <div class=\"uk-modal-footer uk-text-right\">\n                <button type=\"button\" class=\"uk-button uk-margin-small-right uk-modal-close\">Sluiten</button>\n                <button type=\"submit\" class=\"uk-button uk-button-primary\" @click=\"task = 'saveShipment'\">\n                    <i v-spinner=\"saving[shipment.id]\" icon=\"save\"></i>Opslaan</button>\n            </div>\n\n\n        </form>\n    </v-modal>\n</div>";
	module.exports = {

	        props: ['config'],

	        data: function () {
	            return {
	                currentTab: 'pakket',
	                task: '',
	                error: '',
	                progress: 0,
	                progressmessage: '',
	                progresserror: '',
	                sending: false,
	                saving: {},
	                editloading: {},
	                shipment: {
	                    id: 0,
	                    sender_id: 0,
	                    data: {}
	                },
	                shipments: false,
	                senders: {},
	                filter: {
	                    search: '',
	                    order: 'created',
	                    dir: 'desc',
	                    limit: 10
	                },
	                count: 0,
	                total: 0,
	                pages: 1,
	                page: 0
	            }
	        },

	        ready: function () {
	            this.load(this.page);
	        },

	        filters: {
	            count: function (shipments) {
	                this.count = _.size(shipments);
	                return shipments;
	            }
	        },

	        computed: {
	            direction: function () {
	                return this.filter.dir == 'asc' ? 1 : -1;
	            }
	        },

	        methods: {
	            load: function (page) {
	                this.$set('shipments', false);
	                this.$http.get('/api/shipment', {filter: this.filter, page: this.page}).then(function (res) {
	                    if (res.data.shipments !== undefined) {
	                        this.$set('shipments', res.data.shipments);
	                        this.$set('total', res.data.total);
	                        this.$set('pages', res.data.pages);
	                        this.$set('page', Number(res.data.page));
	                    }
	                }, function (res) {
	                    this.$set('error', res.data.message || res.data);
	                    this.$set('shipments', []);
	                });
	            },
	            submitForm: function () {
	                if (this.task == 'saveShipment') {
	                    this.saveShipment();
	                }
	                if (this.task == 'sendAndSave') {
	                    this.sendAndSave();
	                }
	            },
	            saveShipment: function () {
	                Vue.set(this.saving, this.shipment.id, true);
	                this.$http.post('/api/shipment/save', {
	                    data: this.shipment
	                }).then(function (res) {
	                    if (res.data.shipment) {
	                        this.$set('shipment', res.data.shipment);
	                        Vue.set(this.shipments, this.shipment.id, res.data.shipment);
	                        this.$refs.editshipmentmodal.close();
	                    }
	                    this.saving = {};
	                }, function (res) {
	                    this.saving = {};
	                    this.$set('error', res.data.message || res.data);
	                });
	            },
	            sendAndSave: function () {
	                this.$set('currentTab', 'versturen');
	                this.$set('sending', true);
	                this.$set('progresserror', '');
	                this.$set('progress', 5);

	                this.$set('progressmessage', 'Zending opslaan');

	                this.$http.post('/api/shipment/save', {
	                    data: this.shipment
	                }).then(function (res) {
	                    if (res.data.shipment) {
	                        if (!this.shipments || this.shipments.length !== undefined) this.shipments = {};
	                        this.$set('shipment', res.data.shipment);
	                        Vue.set(this.shipments, this.shipment.id, res.data.shipment);
	                        this.$set('progressmessage', 'Zending aanmelden bij GLS');
	                        this.$set('progress', 30);
	                        this.sendShipment(this.shipment.id);
	                    }
	                }, function (res) {
	                    this.setError(res.data.message || res.data);
	                });

	            },
	            sendShipment: function (id) {
	                this.$http.post('/api/shipment/send/' + id).then(function (res) {
	                    if (res.data.error) {
	                        this.setError(res.data.error);
	                    }
	                    if (res.data.shipment) {
	                        this.$set('shipment', res.data.shipment);
	                        this.shipments[res.data.shipment.id] = res.data.shipment;
	                        this.$set('progressmessage', 'Label aanmaken');
	                        this.$set('progress', 70);
	                        this.getLabel(this.shipment.id);
	                    }
	                }, function (res) {
	                    this.setError(res.data.message || res.data);
	                });
	            },
	            getLabel: function (id) {
	                this.$http.post('/api/shipment/label/' + id).then(function (res) {
	                    if (res.data.error) {
	                        this.setError(res.data.error);
	                    }
	                    if (res.data.shipment) {
	                        this.$set('shipment', res.data.shipment);
	                        this.shipments[res.data.shipment.id] = res.data.shipment;
	                        this.$set('progressmessage', 'Aanmelden geslaagd');
	                        this.$set('progress', 100);
	                        setTimeout(function () {this.$set('sending', false);}.bind(this), 1500);
	                    }
	                }, function (res) {
	                    this.setError(res.data.message || res.data);
	                });
	            },
	            editShipment: function (id) {
	                var shipment = _.find(this.shipments, 'id', id),
	                        def = _.size(this.senders) ? _.find(this.senders, 'def', 1) || _.find(this.senders, 'state', 1) : {id: 0};
	                if (!shipment) {
	                    this.$set('shipment', {
	                        klantnummer: this.config.user.klantnummer,
	                        gls_customer_number: this.config.user.gls_customer_number,
	                        sender_id: 0,
	                        product_short_description: 'BP',
	                        parcel_weight: 0,
	                        parcel_sequence: 1,
	                        parcel_quantity: 1,
	                        gls_parcel_number: 0,
	                        state: 1,
	                        data: {
	                            track_trace: '',
	                            label_template: 'gls_default',
	                            express_flag: '',
	                            inbound_country_code: 'NL'
	                        },
	                        pdf_url: ''
	                    });
	                } else {
	                    this.$set('shipment', shipment);
	                }
	                if (_.size(this.senders) === 0) {
	                    Vue.set(this.editloading, id, true);
	                    this.$http.get('/api/sender').then(function (res) {
	                        var def = _.find(res.data.senders, 'def', 1) || _.find(res.data.senders, 'state', 1);
	                        this.$set('senders', res.data.senders);
	                        this.$set('shipment.sender_id', def.id);
	                        this.$refs.editshipmentmodal.open();
	                        this.editloading = {};
	                    }, function (res) {
	                        this.editloading = {};
	                        this.$set('error', res.data.message || res.data);
	                    });
	                } else {
	                    this.$set('shipment.sender_id', def.id);
	                    this.$refs.editshipmentmodal.open();
	                }
	            },
	            setError: function (message) {
	                this.$set('progressmessage', '');
	                this.$set('progresserror', message);
	                this.$set('progress', 0);
	                setTimeout(function () {this.$set('sending', false);}.bind(this), 1500);
	            },
	            cancelEdit: function () {
	                this.$set('currentTab', 'pakket');
	                this.$set('progresserror', '');
	                this.$set('progressmessage', '');
	                this.$set('progress', 0);
	                this.$set('shipment', {
	                    id: 0,
	                    sender_id: 0,
	                    data: {}
	                });
	            },
	            getValueLabel: function (key, value) {
	                var options = {}, label;
	                switch (key) {
	                case 'express_flag':
	                        options = this.$options.fields4['data.express_flag'].options;
	                    break;
	                case 'product_short_description':
	                        options = this.$options.fields4.product_short_description.options;
	                    break;
	                }
	                if (label = _.findKey(options, function (val) {
	                    return val === value;
	                })) {
	                    return label;
	                }
	                return value;
	            }

	        },

	        watch: {

	            'shipment.sender_id': function (id) {
	                var sender = this.senders[id];
	                if (sender) {
	                    ['sender_name_1', 'sender_name_2', 'sender_street', 'sender_zip', 'sender_city', 'sender_country'].forEach(function (key) {
	                        this.$set('shipment.' + key, sender[key]);
	                    }.bind(this));
	                }
	            },
	            'shipment.parcel_sequence': function (sequence) {
	                if (this.shipment.parcel_quantity < sequence) this.shipment.parcel_quantity = sequence;
	            },
	            'filter': {
	                handler: 'load',
	                deep: true
	            },
	            'page': function (page) {
	                this.load(page);
	            }
	        },

	        fields1: {
	            'receiver_name_1': {
	                type: 'text',
	                label: 'Ontvanger naam 1 *',
	                attrs: {'name': 'name', 'class': 'uk-width-1-1', 'required': true}
	            },
	            'receiver_zip_code': {
	                type: 'text',
	                label: 'Postcode ontvanger *',
	                attrs: {'name': 'zip_code', 'class': 'uk-width-1-1', 'required': true}
	            },
	            'receiver_street': {
	                type: 'text',
	                label: 'Ontvanger adres *',
	                attrs: {'name': 'address', 'class': 'uk-width-1-1', 'required': true}
	            },
	            'receiver_place': {
	                type: 'text',
	                label: 'Ontvanger plaats *',
	                attrs: {'name': 'city', 'class': 'uk-width-1-1', 'required': true}
	            },
	            'customer_reference': {
	                type: 'text',
	                label: 'Klantreferentie *',
	                attrs: {'name': 'referentie', 'class': 'uk-width-1-1', 'required': true}
	            },
	            'receiver_name_2': {
	                type: 'text',
	                label: 'Ontvanger naam 2',
	                attrs: {'name': 'tav', 'class': 'uk-width-1-1'}
	            },
	            'receiver_name_3': {
	                type: 'text',
	                label: 'Ontvanger naam 3',
	                attrs: {'name': 'name_3', 'class': 'uk-width-1-1'}
	            }
	        },
	        fields2: {
	            'receiver_contact': {
	                type: 'text',
	                label: 'Contact ontvanger',
	                attrs: {'name': 'contact', 'class': 'uk-width-1-1'}
	            },
	            'receiver_phone': {
	                type: 'text',
	                label: 'Telefoon ontvanger',
	                attrs: {'name': 'telefoon', 'class': 'uk-width-1-1'}
	            },
	            'additional_text_1': {
	                type: 'textarea',
	                label: 'Aanvullende tekst 1',
	                attrs: {'name': 'extra', 'class': 'uk-width-1-1'}
	            },
	            'additional_text_2': {
	                type: 'textarea',
	                label: 'Aanvullende tekst 2',
	                attrs: {'name': 'extra2', 'class': 'uk-width-1-1'}
	            }
	        },

	        fields3: {
	            'date_of_shipping': {
	                type: 'format',
	                label: 'Verzenddatum',
	                attrs: {'v-datetime': 'd-m-Y'}
	            },
	            'gls_parcel_number': {
	                type: 'format',
	                label: 'GLS pakket nummer',
	                attrs: {'class': ''}
	            },
	            'domestic_parcel_number_nl': {
	                type: 'format',
	                label: 'NL pakket nummer',
	                attrs: {'class': ''}
	            },
	            'sap_number': {
	                type: 'format',
	                label: 'SAP nummer',
	                attrs: {'class': ''}
	            }
	        },

	        fields4: {
	            'data.label_template': {
	                type: 'select',
	                label: 'Print template',
	                options: {
	                    'GLS standaard': 'gls_default'
	                },
	                attrs: {'class': 'uk-form-width-medium', 'required': true}
	            },
	            'product_short_description': {
	                type: 'select',
	                label: 'Product type *',
	                options: {
	                    'Business parcel': 'BP',
	                    'Express parcel': 'EP',
	                    'Euro business parcel': 'EBP'
	                },
	                attrs: {'class': 'uk-form-width-medium', 'required': true}
	            },
	            'data.inbound_country_code': {
	                type: 'select',
	                label: 'Land verzending *',
	                options: {
	                    'Nederland': 'NL'
	                },
	                attrs: {'class': 'uk-form-width-medium', 'required': true}
	            },
	            'data.express_flag': {
	                type: 'radio',
	                label: 'Express pakket',
	                options: {
	                    'Geen express': '',
	                    'Volgende dag 9.00 uur': 'T9',
	                    'Volgende dag 12.00 uur': 'T12'
	                },
	                attrs: {'class': 'uk-width-1-1'}
	            },
	            'data.express_service_flag': {
	                type: 'checkbox',
	                label: 'Express service',
	                optionlabel: 'Geef express service aan',
	                attrs: {}
	            },
	            'state': {
	                type: 'select',
	                label: 'Actief',
	                options: {
	                    'Ja': 1,
	                    'Nee': 0
	                },
	                attrs: {'class': 'uk-form-width-medium'}
	            }
	        },

	        fields5: {
	            'sender_name_1': {
	                type: 'text',
	                label: 'Afzender naam 1 *',
	                attrs: {'class': 'uk-form-width-medium', 'required': true}
	            },
	            'sender_name_2': {
	                type: 'text',
	                label: 'Afzender naam 2',
	                attrs: {'class': 'uk-form-width-medium'}
	            },
	            'sender_street': {
	                type: 'text',
	                label: 'Afzender adres *',
	                attrs: {'class': 'uk-form-width-medium', 'required': true}
	            },
	            'sender_zip': {
	                type: 'text',
	                label: 'Afzender postcode *',
	                attrs: {'class': 'uk-form-width-medium', 'required': true}
	            },
	            'sender_city': {
	                type: 'text',
	                label: 'Afzender plaats *',
	                attrs: {'class': 'uk-form-width-medium', 'required': true}
	            },
	            'sender_country': {
	                type: 'text',
	                label: 'Afzender land *',
	                attrs: {'class': 'uk-form-width-medium', 'required': true}
	            }
	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 3 */
/***/ function(module, exports) {

	var __vue_template__ = "<h1>Dashboard {{ config.user.bedrijfsnaam }}</h1>\n\n    <h3>{{ config.user.name }}</h3>\n    <h4>{{ config.user.klantnummer }}</h4>";
	module.exports = {

	        props: ['data', 'config']

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<h1>Verzendingen</h1>\n\n    <gls-shipment :config=\"config\"></gls-shipment>";
	module.exports = {

	        props: ['data', 'config'],

	        components: {
	            'gls-shipment': __webpack_require__(2)
	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<h1>Afzenders</h1>\n\n    <devos-senders :config=\"config\" :data=\"data\"></devos-senders>";
	module.exports = {

	        props: ['data', 'config'],

	        components: {
	            'devos-senders': __webpack_require__(6)
	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 6 */
/***/ function(module, exports) {

	var __vue_template__ = "<div>\n\n    <div v-if=\"error\" class=\"uk-alert uk-alert-danger\">{{ error }}</div>\n\n    <div class=\"uk-flex uk-flex-space-between\">\n        <div>\n            <div class=\"uk-search\" data-uk-search=\"\">\n                <input class=\"uk-search-field\" type=\"search\" v-model=\"search\">\n            </div>\n        </div>\n        <div>\n            <button class=\"uk-button\" @click=\"editSender(0)\"><i class=\"uk-icon-plus uk-margin-small-right\"></i>Nieuwe afzender</button>\n        </div>\n    </div>\n\n    <table class=\"uk-table\" v-show=\"senders\">\n        <thead>\n        <tr>\n            <th>#</th>\n            <th></th>\n            <th>Naam</th>\n            <th>Adres</th>\n            <th>Postcode</th>\n            <th>Plaats</th>\n            <th>Logo</th>\n            <th></th>\n        </tr>\n        </thead>\n        <tbody>\n        <tr v-for=\"sender in senders | filterBy search | count\">\n            <td class=\"uk-text-center\">{{ sender.id }}</td>\n            <td class=\"uk-text-center\">\n                <a :title=\"getStateName(sender.state)\" class=\"uk-icon-circle uk-margin-small-right\" :class=\"{\n                    'uk-text-danger': sender.state == 0,\n                    'uk-text-success': sender.state == 1\n                }\" @click=\"toggleState(sender)\" data-uk-tooltip=\"{delay: 200}\"></a>\n                <a :title=\"sender.def == 1 ? 'Standaard afzender' : ''\" v-spinner=\"settingdefault\" icon=\"star\" :class=\"{\n                    'uk-text-primary': sender.def == 1,\n                    'uk-text-muted': sender.def == 0\n                }\" @click=\"setDefault(sender)\" data-uk-tooltip=\"{delay: 200}\"></a>\n            </td>\n            <td>\n                <strong>{{ sender.sender_name_1 }}</strong>\n                <div v-if=\"sender.sender_name_2\">{{ sender.sender_name_2 }}</div>\n            </td>\n            <td>{{ sender.sender_street }}</td>\n            <td>{{ sender.sender_zip }}</td>\n            <td>{{ sender.sender_city }} ({{ sender.sender_country }})</td>\n            <td> logo</td>\n            <td>\n                <a @click=\"editSender(sender.id)\" v-spinner=\"saving[sender.id]\" icon=\"edit\" class=\"uk-icon-hover uk-margin-small-right\"></a>\n                <a v-spinner=\"deleting[sender.id]\" icon=\"trash-o\" class=\"uk-icon-hover\" v-confirm=\"'Wilt u deze afzender verwijderen?'\" @click=\"deleteSender(sender.id)\"></a>\n            </td>\n        </tr>\n        <tr v-show=\"!count\">\n            <td colspan=\"7\" class=\"uk-text-center\">\n                <div class=\"uk-alert\">Geen afzenders gevonden.</div>\n            </td>\n        </tr>\n        </tbody>\n    </table>\n    <div v-else=\"\" class=\"uk-text-center\"><i class=\"uk-icon-circle-o-notch uk-icon-spin\"></i></div>\n\n\n    <v-modal v-ref:editsendermodal=\"\" :large=\"true\">\n        <div class=\"uk-modal-header\"><h3>Afzender</h3></div>\n\n        <form class=\"uk-form\" @submit.prevent=\"saveSender\">\n\n            <div class=\"uk-grid\">\n                <div class=\"uk-width-medium-2-3 uk-form-horizontal\">\n                    <fields :config=\"$options.fields1\" :model.sync=\"sender\" template=\"formrow\"></fields>\n                </div>\n                <div class=\"uk-width-medium-1-3 uk-form-stacked\">\n                    image\n                    <fields :config=\"$options.fields2\" :model.sync=\"sender\" template=\"formrow\"></fields>\n                </div>\n            </div>\n\n            <div class=\"uk-modal-footer uk-text-right\">\n                <button type=\"button\" class=\"uk-button uk-margin-small-right uk-modal-close\">Sluiten</button>\n                <button type=\"submit\" class=\"uk-button uk-button-primary\"><i v-spinner=\"saving[sender.id]\" icon=\"save\"></i>Opslaan</button>\n            </div>\n\n\n        </form>\n    </v-modal>\n</div>";
	module.exports = {

	        props: ['config', 'data'],

	        data: function () {
	            return {
	                error: false,
	                deleting: {},
	                saving: {},
	                settingdefault: false,
	                editloading: false,
	                sender: {
	                    id: 0,
	                    data: {}
	                },
	                senders: false,
	                search: '',
	                count: 0
	            }
	        },

	        ready: function () {
	            this.load(this.page);
	        },

	        filters: {
	            count: function (senders) {
	                this.count = _.size(senders);
	                return senders;
	            }
	        },

	        methods: {
	            load: function (page) {
	                this.$set('error', '');
	                this.$http.get('/api/sender', {inactive: 1}).then(function (res) {
	                    this.$set('senders', res.data.senders);
	                }, function (res) {
	                    this.$set('error', res.data.message || res.data);
	                });
	            },
	            saveSender: function () {
	                this.$set('error', '');
	                Vue.set(this.saving, this.sender.id, true);
	                this.$http.post('/api/sender/save', {
	                    data: this.sender
	                }).then(function (res) {
	                    if (res.data.sender) {
	                        Vue.set(this.senders, res.data.sender.id, res.data.sender);
	                        this.$refs.editsendermodal.close();
	                    }
	                    this.saving = {};
	                }, function (res) {
	                    this.saving = {};
	                    this.$set('error', res.data.message || res.data);
	                });
	            },
	            editSender: function (id) {
	                var sender = _.find(this.senders, 'id', id);
	                this.$set('error', '');
	                if (!sender) {
	                    this.$set('sender', {
	                        user_id: this.config.user.id,
	                        sender_country: 'NL',
	                        state: 1,
	                        def: 0,
	                        data: {}
	                    });
	                } else {
	                    this.$set('sender', sender);
	                }
	                this.$refs.editsendermodal.open();
	            },
	            deleteSender: function (id) {
	                this.$set('error', '');
	                Vue.set(this.deleting, id, true);
	                this.$http.delete('/api/sender/' + id).then(function () {
	                    Vue.delete(this.senders, id);
	                    this.deleting = {};
	                }, function (res) {
	                    this.deleting = {};
	                    this.$set('error', res.data.message || res.data);
	                });
	            },
	            getStateName: function (state) {
	                return this.data.sender_states[state];
	            },
	            setDefault: function (sender) {
	                this.$set('error', '');
	                if (Number(sender.state) == 0) {
	                    this.$set('error', 'Standaard afzender moet gepubliceerd zijn');
	                    return;
	                }
	                this.settingdefault = true;
	                this.$http.post('/api/sender/setdefault', {
	                    id: sender.id
	                }).then(function (res) {
	                    this.$set('senders', res.data.senders);
	                    this.settingdefault = false;
	                }, function (res) {
	                    this.settingdefault = false;
	                    this.$set('error', res.data.message || res.data);
	                });
	            },
	            toggleState: function (sender) {
	                this.$set('error', '');
	                if (Number(sender.def) === 1 && Number(sender.state) === 1) {
	                    this.$set('error', 'Standaard afzender moet gepubliceerd zijn');
	                    return;
	                }
	                sender.state = Number(sender.state) === 1 ? 0 : 1;
	                this.sender = sender;
	                this.saveSender();
	            }

	        },

	        fields1: {
	            'sender_name_1': {
	                type: 'text',
	                label: 'Naam 1 *',
	                attrs: {'class': 'uk-width-1-1', 'required': true}
	            },
	            'sender_name_2': {
	                type: 'text',
	                label: 'Naam 2',
	                attrs: {'class': 'uk-width-1-1'}
	            },
	            'sender_street': {
	                type: 'text',
	                label: 'Adres *',
	                attrs: {'class': 'uk-width-1-1', 'required': true}
	            },
	            'sender_zip': {
	                type: 'text',
	                label: 'Postcode *',
	                attrs: {'class': 'uk-width-1-1', 'required': true}
	            },
	            'sender_city': {
	                type: 'text',
	                label: 'Stad *',
	                attrs: {'class': 'uk-width-1-1', 'required': true}
	            },
	            'sender_country': {
	                type: 'select',
	                label: 'Land *',
	                options: {
	                    'Nederland': 'NL',
	                    'België': 'BE',
	                    'Duitsland': 'DE'
	                },
	                attrs: {'class': 'uk-form-width-large', 'required': true}
	            }
	        },

	        fields2: {
	            'state': {
	                type: 'radio',
	                label: 'Actief',
	                options: {
	                    'Inactief': 0,
	                    'Actief': 1
	                },
	                attrs: {}
	            }
	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);