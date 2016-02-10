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
/******/ ({

/***/ 0:
/***/ function(module, exports, __webpack_require__) {

	
	window.Dashboard = module.exports = {

	    data: function () {
	        return {
	            saving: false,
	            sections: [],
	            data: window.$data,
	            config: window.$config
	        };
	    },

	    created: function () {
	        //sections
	        var sections = [];
	        _.forIn(this.$options.components, function (component, name) {
	            var options = component.options || {};
	            if (options.section) {
	                sections.push(_.extend({name: name, priority: 0}, options.section));
	            }
	        });
	        this.$set('sections', _.sortBy(sections, 'priority'));


	    },

	    ready: function () {

	    },

	    methods: {
	        load: function () {
	            this.$http.get('&p=/drukkerij/edit', {
	                drukkerijID: this.data.drukkerijID
	            }, function (data) {
	                this.$set('drukkerij', data.drukkerij);
	                this.$set('afwerkprijzen', _.groupBy(_.sortByOrder(data.afwerkprijzen, 'afwerksoort'), 'afwerksoort'));
	                this.$nextTick(function () {
	                    UIkit.switcher(this.$$.machineTabs).show(UIkit.$(this.$$.machineTabs).find('li:first'));
	                    UIkit.switcher(this.$$.papierprijzenTabs).show(UIkit.$(this.$$.papierprijzenTabs).find('li:first'));
	                    UIkit.switcher(this.$$.afwerkprijzenTabs).show(UIkit.$(this.$$.afwerkprijzenTabs).find('li:first'));
	                    UIkit.accordion('.uk-accordion', {showfirst: false, toggle: '.bps-accordion-toggle', containers: '.uk-panel'});
	                }.bind(this));
	            }, function (data) {
	                console.warn(data);
	            });
	        },

	        saveDrukkerij: function () {
	            this.saving = true;
	            this.$http.post('&p=/drukkerij/save', {
	                drukkerijData: this.drukkerij
	            }, function (data) {
	                UIkit.notify(this.$trans('Drukkerij opgeslagen'), 'success');
	                this.$set('drukkerij', data.drukkerij);
	                this.saving = false;
	            }, function (data) {
	                console.warn(data);
	                this.saving = false;
	            });
	        },

	    },
	    components: {
	        'dv-settings': __webpack_require__(26)
	    }

	};

	UIkit.$(function () {

	    new Vue(module.exports).$mount('#devos-dashboard');

	});


/***/ },

/***/ 26:
/***/ function(module, exports) {

	var __vue_template__ = "<div>\n\n    <div v-if=\"error\" class=\"uk-alert uk-alert-danger\">{{ error }}</div>\n\n    <div v-if=\"message\" class=\"uk-alert uk-alert-success\">{{ message }}</div>\n\n    <button class=\"uk-button\" @click=\"editSettings\"><i v-spinner=\"loading\" icon=\"cogs\"></i>Instellingen</button>\n\n\n    <v-modal v-ref:editsettingsmodal=\"\" :large=\"true\">\n        <div class=\"uk-modal-header\"><h3>Instellingen</h3></div>\n\n        <form class=\"uk-form\" @submit.prevent=\"saveSettings\">\n\n            <div class=\"uk-grid\">\n                <div class=\"uk-width-medium-1-2 uk-form-horizontal\">\n                    <fields :config=\"$options.fields1\" :model.sync=\"settings\" template=\"formrow\"></fields>\n                </div>\n                <div class=\"uk-width-medium-1-2 uk-form-horizontal\">\n                    <fields :config=\"$options.fields2\" :model.sync=\"settings\" template=\"formrow\"></fields>\n                </div>\n            </div>\n\n            <div class=\"uk-modal-footer uk-text-right\">\n                <button type=\"button\" class=\"uk-button uk-margin-small-right uk-modal-close\">Sluiten</button>\n                <button type=\"submit\" class=\"uk-button uk-button-primary\"><i v-spinner=\"saving\" icon=\"save\"></i>Opslaan</button>\n            </div>\n\n\n        </form>\n    </v-modal>\n</div>";
	var defaultSettings = {
	        'gls_customer_numbers': [],
	        'glsserver': 'unibox.gls-netherlands.com',
	        'glsport_live': 3033,
	        'glsport_test': 3032,
	        'gls_test': 0,
	        'gls_tracking' : {
	            encryptionCode: 123
	        }
	    };

	    module.exports = {

	        props: ['config', 'data'],

	        data: function () {
	            return {
	                error: false,
	                message: false,
	                loading: false,
	                saving: false,
	                settings: {}
	            }
	        },

	        methods: {
	            saveSettings: function () {
	                this.$set('error', '');
	                this.saving = true;
	                this.$http.post('/api/config', {
	                    data: this.settings
	                }).then(function (res) {
	                    if (res.data) {
	                        this.$set('settings', res.data);
	                        this.$set('message', 'Instellingen opgeslagen');
	                        this.$refs.editsettingsmodal.close();
	                    }
	                    this.saving = false;
	                }, function (res) {
	                    this.saving = false;
	                    this.$set('error', res.data.message || res.data);
	                });
	            },
	            editSettings: function () {
	                this.loading = true;
	                this.$http.get('/api/config').then(function (res) {
	                    if (res.data) {
	                        console.log(_.defaults(res.data, defaultSettings));
	                        this.$set('settings', _.defaults(res.data, defaultSettings));
	                        this.$refs.editsettingsmodal.open();
	                    }
	                    this.loading = false;
	                }, function (res) {
	                    this.loading = false;
	                    this.$set('error', res.data.message || res.data);
	                });
	            }

	        },

	        fields1: {
	            'glsUnibox': {
	                type: 'title',
	                title: 'GLS Unibox',
	                attrs: {'class': 'uk-margin-remove'}
	            },
	            'gls_customer_numbers': {
	                type: 'tags',
	                label: 'GLS Klantnummers',
	                style: 'list',
	                attrs: {'class': 'uk-width-1-1'}
	            },
	            'gls_customer_number': {
	                type: 'text',
	                label: 'Standaard GLS Klantnummer *',
	                attrs: {'class': 'uk-width-1-1', 'required': true}
	            },
	            'sap_number': {
	                type: 'text',
	                label: 'SAP nummer *',
	                attrs: {'class': 'uk-width-1-1', 'required': true}
	            },
	            'glsserver': {
	                type: 'text',
	                label: 'GLS Server *',
	                attrs: {'class': 'uk-width-1-1', 'required': true}
	            },
	            'glsport_live': {
	                type: 'number',
	                label: 'GLS port live',
	                attrs: {'class': 'uk-width-1-1', 'required': true}
	            },
	            'glsport_test': {
	                type: 'number',
	                label: 'GLS port test',
	                attrs: {'class': 'uk-width-1-1', 'required': true}
	            },
	            'gls_test': {
	                type: 'select',
	                label: 'Testmodus',
	                options: {
	                    'Ja': 1,
	                    'Nee': 0
	                },
	                attrs: {'class': 'uk-form-width-medium'}
	            }
	        },

	        fields2: {
	            'glsTrackTrace': {
	                type: 'title',
	                title: 'GLS Track & Trace',
	                attrs: {'class': 'uk-margin-remove'}
	            },
	            'gls_tracking.encryptionCode': {
	                type: 'number',
	                label: 'Encryptie Code *',
	                attrs: {'class': 'uk-width-1-1', 'min': 100, 'max': 999, 'required': true}
	            }

	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }

/******/ });