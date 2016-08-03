
window.Dashboard = module.exports = {

    el: '#devos-shipments-sendcloud',

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
        'sendcloud-shipment': require('../../components/ui/sendcloud-shipment.vue')
    }

};

UIkit.$(function () {

    new Vue(module.exports);

});
