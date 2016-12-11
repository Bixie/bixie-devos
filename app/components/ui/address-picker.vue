<template>
    <div class="uk-form-row" id="address-base">
        <label class="uk-form-label">Uit adresboek</label>
        <div class="uk-form-controls">

            <div class="uk-form-icon uk-width-1-1">
                <i :class="{'uk-icon-search': !loading, 'uk-icon-circle-o-notch uk-icon-spin': loading}"></i>
                <input class="uk-margin-remove uk-width-1-1" type="search" v-model="search" debounce="500">
            </div>

            <div v-el:dropdown class="uk-position-relative">
                <div class="uk-dropdown">
                    <ul class="uk-list uk-list-line">
                        <li v-for="address in addresses" @click="pick(address)" class="uk-dropdown-close">
                            <div class="uk-grid uk-grid-small" data-uk-grid-margin style="cursor: pointer;">
                                <div class="uk-width-medium-1-2">
                                    <strong>{{ address.name_1 }}</strong>
                                    <div v-if="address.address_name_2">{{ address.name_2 }}</div>
                                </div>
                                <div class="uk-width-medium-1-2">
                                    {{ address.street }}<br/>
                                    {{ address.zip }}, {{ address.city }} ({{ address.country }})
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div v-if="!loading && total === 0" class="uk-alert">Geen adressen gevonden.</div>
                    <v-pagination :page.sync="page" :pages="pages" v-show="pages > 1"></v-pagination>

                </div>
            </div>



        </div>
    </div>
</template>

<script>

    module.exports = {

        props: ['config', 'onPick'],

        data: function () {
            return {
                addresses: [],
                loading: false,
                total: 0,
                pages: 0,
                page: 0,
                error: '',
                search: '',
            }
        },

        ready: function () {
            this.dropdown = UIkit.dropdown(this.$els.dropdown,  {pos:'bottom-left', justify: '#address-base', mode: 'click'});
            this.$watch('page + search', function () {
                this.load();
            })
        },

        filters: {
            count: function (addresses) {
                this.count = _.size(addresses);
                return addresses;
            }
        },

        methods: {
            load: function (page) {
                this.loading = true;
                this.$http.get('/api/address', {filter: {inactive: 1, search: this.search, limit: 5}, page: this.page}).then(function (res) {
                    this.addresses = res.data.addresses;
                    this.total = res.data.total;
                    this.pages = res.data.pages;
                    this.page = Number(res.data.page);
                    this.dropdown.show();
                    this.loading = false;
                }, function (res) {
                    this.loading = false;
                    this.error = res.data.message || res.data;
                });
            },
            pick: function (address) {
                this.onPick(address);
            },
        },



    };


</script>