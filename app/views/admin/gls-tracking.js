
window.Dashboard = module.exports = {

    el: '#devos-gls-tracking',

    data: function () {
        return {
            syncing: false,
            glstrackings: false,
            trackings: [],
            filter: {
                search: '',
                order: 'created',
                dir: 'desc',
                limit: 10
            },
            total: 0,
            pages: 1,
            page: 0,
            data: _.merge({}, window.$data),
            config: _.merge({}, window.$config)
        };
    },

    watch: {
        'page': function (page) {
            this.load(page);
        }
        
    },

    ready: function () {
        this.load(0);
    },

    methods: {
        load: function (page) {
            this.$set('glstrackings', false);
            this.$http.get('/api/glstracking', {filter: this.filter, page: this.page}).then(function (res) {
                if (res.data.glstrackings !== undefined) {
                    this.$set('glstrackings', res.data.glstrackings);
                    this.$set('total', res.data.total);
                    this.$set('pages', res.data.pages);
                    this.$set('page', Number(res.data.page));
                }
            }, function (res) {
                UIkit.notify(res.data.message || res.data, 'danger');
                this.$set('glstrackings', []);
            });
        },
        
        getStateName: function (state) {
            return this.data.glstracking_states[state] || state;
        },

        syncGls: function () {
            this.syncing = true;
            this.$set('trackings', []);
            this.$http.get('/api/glstracking/sync', {}).then(function (res) {

                this.$set('trackings', res.data.trackings);
                this.load(0);
                this.syncing = false;
            }, function (res) {
                UIkit.notify(res.data.message || res.data, 'danger');
                this.syncing = false;
            });
        }


    },
    components: {
        'log-modal': require('../../components/log-modal.vue')
    }

};

UIkit.$(function () {

    new Vue(module.exports);

});
