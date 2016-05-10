
window.Dashboard = module.exports = {

    el: '#devos-shipments',

    data: function () {
        return {
            saving: false,
            sections: [],
            data: window.$data,
            config: window.$config
        };
    },


    ready: function () {

    },

    methods: {

    },
    components: {
        'gls-shipment': require('../../components/ui/gls-shipment.vue')
    }

};

UIkit.$(function () {

    new Vue(module.exports);

});
