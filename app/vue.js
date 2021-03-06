var Vue = require('vue');

/**
 * Libraries
 */

require('vue-resource')(Vue);

/**
 * Configuration
 */

function install (Vue) {

    var config = window.$config;

    Vue.component('multiselect', require('./components/multiselect.vue'));
    Vue.component('input-tags', require('./components/input-tags.vue'));
    Vue.component('v-modal', require('./components/modal.vue'));
    Vue.component('v-pagination', require('./components/pagination'));

    Vue.directive('confirm', require('./directives/confirm'));
    Vue.directive('spinner', require('./directives/spinner'));

    Vue.http.options.emulateHTTP = true;
    Vue.http.headers.custom = {'X-XSRF-TOKEN': config.csrf};
    Vue.http.options.beforeSend = function (request) {
        request.params.p = request.url;
        request.params.option = 'com_bix_devos';
        request.params.api = 1;
        request.url = config.url;
    };
}

jQuery(function () {
    Vue.use(install);
});

window.Vue = Vue;
window._ = require('lodash');

//needs window.Vue
require('vue-form');
require('./lib/ninja-print')(Vue);
require('./lib/filters')(Vue);

Vue.field.templates.formrow = require('./templates/formrow.html');
Vue.field.templates.raw = require('./templates/raw.html');
Vue.field.templates.descriptionlist = require('./templates/descriptionlist.html');

Vue.field.types.text = '<input type="text" v-bind="attrs" v-model="value">';
Vue.field.types.textarea = '<textarea v-bind="attrs" v-model="value"></textarea>';
Vue.field.types.select = '<select v-bind="attrs" v-model="value"><option v-for="option in options" :value="option">{{ $key }}</option></select>';
Vue.field.types.radio = '<p class="uk-form-controls-condensed">\n    <label class="uk-margin-small-right" v-for="option in options" v-bind="attrs"><input type="radio" :value="option" v-model="value"> {{ $key }}</label>\n</p>';
Vue.field.types.checkbox = '<p class="uk-form-controls-condensed"><label><input type="checkbox" v-bind="attrs" v-model="value" v-bind:true-value="1" v-bind:false-value="0" number> {{ optionlabel }}</label></p>';

Vue.field.types.email = '<input type="email" v-bind="attrs" v-model="value">';
Vue.field.types.number = '<input type="number" v-bind="attrs" v-model="value" number>';
Vue.field.types.title = '<h3 v-bind="attrs">{{ title }}</h3>';
Vue.field.types.price = '<i class="uk-icon-euro uk-margin-small-right"></i><input type="number" v-bind="attrs" v-model="value" number>';
Vue.field.types.multiselect = '<multiselect :values.sync="value" :options="options"></multiselect>';

Vue.field.types.tags = '<input-tags v-bind="attrs" :tags.sync="value" :existing="options" :style="style"></input-tags>';

Vue.field.types.format = '<span v-bind="attrs">{{ value }}</span>';
