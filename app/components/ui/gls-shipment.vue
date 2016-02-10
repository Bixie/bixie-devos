<template>
<div>

    <div v-if="error" class="uk-alert uk-alert-danger">{{ error }}</div>

    <div class="uk-flex uk-flex-space-between uk-flex-middle">
        <div>
            <div class="uk-form-icon">
                <i class="uk-icon-search"></i>
                <input class="uk-margin-remove uk-form-width-small" type="search" v-model="filter.search" debounce="500">
            </div>
        </div>
        <div>
            <small>{{ total }} verzending<span v-show="total != 1">en</span></small>
        </div>
        <div>
            <button class="uk-button" @click="editShipment(0)">
                <i v-spinner="editloading[0]" icon="plus"></i>Nieuwe verzending</button>
        </div>
    </div>


    <table class="uk-table uk-table-hover" v-show="shipments">
        <thead>
        <tr>
            <th>Pakket</th>
            <th class="uk-width-1-4">Ontvanger</th>
            <th class="uk-width-1-4">Afzender</th>
            <th class="uk-width-1-5">Status</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="4">
                <v-pagination :page.sync="page" :pages="pages" v-show="pages > 1"></v-pagination>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <tr v-for="shipment in shipments | orderBy filter.order direction | count">
            <td>
                <dl>
                    <dd>
                        <i class="uk-icon-cubes uk-icon-justify uk-margin-small-right" title="Product type" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ getValueLabel('product_short_description', shipment.product_short_description) }}</span>
                    </dd>
                    <dd>
                        <i class="uk-icon-bolt uk-icon-justify uk-margin-small-right" title="Express" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ getValueLabel('express_flag', shipment.data.express_flag) }}</span>
                        <i v-show="shipment.data.express_service_flag" class="uk-icon-flag uk-text-danger uk-margin-small-left" title="Express Service aan" data-uk-tooltip="{delay: 200}"></i>
                    </dd>
                    <dd>
                        <i class="uk-icon-ticket uk-icon-justify uk-margin-small-right" title="NL pakket nummer" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.domestic_parcel_number_nl }}</span>
                    </dd>
                    <dd>
                        <i class="uk-icon-barcode uk-icon-justify uk-margin-small-right" title="GLS pakket nummer" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.gls_parcel_number }}</span>
                    </dd>
                    <dd>
                        <i class="uk-icon-circle uk-icon-justify uk-margin-small-right" title="Gewicht" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.parcel_weight }} kg</span>
                    </dd>
                    <dd>
                        <i class="uk-icon-clock-o uk-icon-justify uk-margin-small-right" title="Datum" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.date_of_shipping | date }}</span>
                    </dd>
                </dl>
            </td>
            <td>
                <dl>
                    <dd>
                        <i class="uk-icon-user uk-icon-justify uk-margin-small-right" title="Ontvanger naam 1" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.receiver_name_1 }}</span>
                    </dd>
                    <dd v-if="shipment.receiver_name_2">
                        <i class="uk-icon- uk-icon-justify uk-margin-small-right" title="Ontvanger naam 2" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.receiver_name_2 }}</span>
                    </dd>
                    <dd v-if="shipment.receiver_name_3">
                        <i class="uk-icon- uk-icon-justify uk-margin-small-right" title="Ontvanger naam 3" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.receiver_name_3 }}</span>
                    </dd>
                    <dd>
                        <i class="uk-icon-building-o uk-icon-justify uk-margin-small-right" title="Ontvanger adres" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.receiver_street }}</span>
                    </dd>
                    <dd>
                        <i class="uk-icon- uk-icon-justify uk-margin-small-right" title="Ontvanger postcode" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.receiver_zip_code }}</span>
                    </dd>
                    <dd>
                        <i class="uk-icon- uk-icon-justify uk-margin-small-right" title="Ontvanger plaats" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.receiver_place }}</span>
                    </dd>
                    <dd v-if="shipment.receiver_contact">
                        <i class="uk-icon-user uk-icon-justify uk-margin-small-right" title="Contact ontvanger" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.receiver_contact }}</span>
                    </dd>
                    <dd v-if="shipment.receiver_phone">
                        <i class="uk-icon-phone uk-icon-justify uk-margin-small-right" title="Telefoon ontvanger" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.receiver_phone }}</span>
                    </dd>
                </dl>
            </td>
            <td>
                <dl>
                    <dd>
                        <i class="uk-icon-tag uk-icon-justify uk-margin-small-right" title="Klantreferentie" data-uk-tooltip="{delay: 200}"></i>
                        <strong>{{ shipment.customer_reference }}</strong>
                    </dd>
                    <dd>
                        <i class="uk-icon-user uk-icon-justify uk-margin-small-right" title="Afzender naam 1" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.sender_name_1 }}</span>
                    </dd>
                    <dd v-if="shipment.sender_name_2">
                        <i class="uk-icon- uk-icon-justify uk-margin-small-right" title="Afzender naam 2" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.sender_name_2 }}</span>
                    </dd>
                    <dd>
                        <i class="uk-icon-building-o uk-icon-justify uk-margin-small-right" title="Afzender adres" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.sender_street }}</span>
                    </dd>
                    <dd>
                        <i class="uk-icon- uk-icon-justify uk-margin-small-right" title="Afzender postcode" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.sender_zip }}</span>
                    </dd>
                    <dd>
                        <i class="uk-icon- uk-icon-justify uk-margin-small-right" title="Afzender plaats" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.sender_city }}</span>
                    </dd>
                </dl>
            </td>
            <td>
                {{ shipment.state }}<br>
                <ul class="uk-list">
                    <li>
                        <a v-show="shipment.data.track_trace" :href="shipment.data.track_trace"
                           target="_blank">
                            <i class="uk-icon-external-link uk-margin-small-right"></i>
                            Track & Trace</a>
                    </li>
                    <li v-show="shipment.pdf_url" class="uk-text-truncate">
                        <a :href="shipment.pdf_url">
                            <i class="uk-icon-file-pdf-o uk-margin-small-right"></i>
                            Etiket</a>
                    </li>
                </ul>

                <button class="uk-button uk-button-small" @click="editShipment(shipment.id)">
                    <i v-spinner="editloading[shipment.id]" icon="edit"></i>Bewerken</button><br>
            </td>
        </tr>
        <tr v-show="!count">
            <td colspan="4" class="uk-text-center">
                <div class="uk-alert">Geen verzendingen gevonden.</div>
            </td>
        </tr>
        </tbody>
    </table>
    <div v-else class="uk-text-center"><i class="uk-icon-circle-o-notch uk-icon-spin"></i></div>


    <v-modal v-ref:editshipmentmodal :large="true" :closed="cancelEdit">
        <div class="uk-modal-header">
            <div class="uk-flex">
                <h3 class="uk-flex-item-1">GLS verzending</h3>
                <div class="uk-flex uk-flex-middle uk-h5">
                    <div v-if="shipment.sender_id" class="uk-margin-left">
                        <i class="uk-icon-user uk-margin-small-right" title="Afzender" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ senders[shipment.sender_id].sender_name_1 }}</span>
                    </div>
                    <div class="uk-margin-left">
                        <i class="uk-icon-cubes uk-margin-small-right" title="Product type" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ getValueLabel('product_short_description', shipment.product_short_description) }}</span>
                    </div>
                    <div class="uk-margin-left">
                        <i class="uk-icon-bolt uk-margin-small-right" title="Express" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ getValueLabel('express_flag', shipment.data.express_flag) }}</span>
                        <i v-show="shipment.data.express_service_flag" class="uk-icon-flag uk-text-danger uk-margin-small-left" title="Express Service aan" data-uk-tooltip="{delay: 200}"></i>
                    </div>
                </div>
            </div>
        </div>

        <form class="uk-form" @submit.prevent="submitForm">

            <ul class="uk-tab">
                <li :class="{'uk-active': currentTab == 'pakket'}"><a href="#" @click.prevent="currentTab = 'pakket'">
                    <i class="uk-icon-cubes uk-margin-small-right"></i>Pakket</a></li>
                <li :class="{'uk-active': currentTab == 'versturen'}"><a href="#" @click.prevent="currentTab = 'versturen'">
                    <i class="uk-icon-paper-plane-o uk-margin-small-right"></i>Versturen</a></li>
                <li :class="{'uk-active': currentTab == 'instellingen'}"><a href="#" @click.prevent="currentTab = 'instellingen'">
                    <i class="uk-icon-cogs uk-margin-small-right"></i>Instellingen</a></li>
            </ul>

            <div id="shipment-tabs" class="uk-margin">
                <div v-show="currentTab == 'pakket'">

                    <div class="uk-grid uk-grid-width-medium-1-3 uk-grid-small uk-form-stacked uk-flex-center">
                        <div class="uk-text-center">
                            <label class="uk-form-label" for="form-parcel_weight">Gewicht pakket *</label>
                            <div class="uk-form-controls">
                                <input v-model="shipment.parcel_weight" id="form-parcel_weight" type="number"
                                       class="uk-form-width-medium uk-text-right" min="0" step="0.1" required="" number>
                            </div>
                        </div>
                        <div class="uk-text-center">
                            <label class="uk-form-label" for="form-parcel_sequence">Pakket reeks</label>
                            <div class="uk-form-controls">
                                <input v-model="shipment.parcel_sequence" id="form-parcel_sequence" type="number"
                                       class="uk-form-width-medium uk-text-right" min="0" required="" number>
                            </div>
                        </div>
                        <div class="uk-text-center">
                            <label class="uk-form-label" for="form-parcel_quantity">Pakket aantal</label>
                            <div class="uk-form-controls">
                                <input v-model="shipment.parcel_quantity" id="form-parcel_quantity" type="number"
                                       class="uk-form-width-medium uk-text-right" min="0" required="" number>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="uk-grid uk-form-horizontal">
                        <div class="uk-width-medium-1-2">
                            <fields :config="$options.fields1" :model.sync="shipment" template="formrow"></fields>
                        </div>
                        <div class="uk-width-medium-1-2">
                            <fields :config="$options.fields2" :model.sync="shipment" template="formrow"></fields>

                            <div v-show="shipment.gls_parcel_number == 0" class="uk-margin-large uk-text-center">
                                <button class="uk-button uk-button-success uk-button-large"
                                        @click="task = 'sendAndSave'">
                                    <i v-spinner="sending" icon="paper-plane-o"></i>
                                    Opslaan en aanmelden
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
                <div v-show="currentTab == 'versturen'">

                    <div class="uk-grid uk-grid-small uk-form-stacked">
                        <div class="uk-width-medium-1-2">
                            <dl class="uk-description-list-horizontal">
                                <fields :config="$options.fields3" :model.sync="shipment" template="descriptionlist"></fields>
                            </dl>
                        </div>
                        <div class="uk-width-medium-1-2 uk-flex uk-flex-column uk-flex-wrap-space-between">

                            <div>
                                <div v-show="shipment.gls_parcel_number == 0" class="uk-text-center">
                                    <button class="uk-button uk-button-success uk-button-large"
                                            @click="task = 'sendAndSave'">
                                        <i v-spinner="sending" icon="paper-plane-o"></i>
                                        Opslaan en aanmelden
                                    </button>
                                </div>
                                <div v-else>
                                    <dl>
                                        <dt>Track & Trace</dt>
                                        <dd>
                                            <a v-show="shipment.data.track_trace" :href="shipment.data.track_trace"
                                               target="_blank" class="uk-display-block uk-text-truncate">
                                                <i class="uk-icon-external-link uk-margin-small-right"></i>
                                                {{ shipment.data.track_trace }}</a>
                                        </dd>
                                        <dt v-show="shipment.pdf_url">Etiket</dt>
                                        <dd v-show="shipment.pdf_url">
                                            <a :href="shipment.pdf_url" class="uk-display-block uk-text-truncate">
                                                <i class="uk-icon-file-pdf-o uk-margin-small-right"></i>
                                                {{ shipment.pdf_url }}</a>
                                        </dd>
                                    </dl>
                                </div>

                                <div v-if="progresserror" class="uk-alert uk-alert-danger">{{ progresserror }}</div>

                                <div v-if="progressmessage" class="uk-alert"
                                     :class="{'uk-alert-success': progress == 100}">{{ progressmessage }}</div>

                                <div v-if="sending" class="uk-progress uk-progress-striped uk-active">
                                    <div class="uk-progress-bar" :style="{'width': progress +'%'}"></div>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>
                <div v-show="currentTab == 'instellingen'">

                    <div class="uk-grid uk-grid-width-medium-1-2 uk-grid-small uk-form-horizontal">
                        <div>

                            <div class="uk-form-row">
                                <label class="uk-form-label" for="form-sender_id">Afzender</label>
                                <div class="uk-form-controls">
                                    <select v-model="shipment.sender_id" id="form-sender_id" class="uk-form-width-medium">
                                        <option v-for="sender in senders" :value="sender.id">{{ sender.sender_name_1 }}</option>
                                    </select>
                                </div>
                            </div>

                            <fields :config="$options.fields4" :model.sync="shipment" template="formrow"></fields>
                        </div>
                        <div>
                            <fields :config="$options.fields5" :model.sync="shipment" template="formrow"></fields>
                        </div>
                    </div>

                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button type="button" class="uk-button uk-margin-small-right uk-modal-close">Sluiten</button>
                <button type="submit" class="uk-button uk-button-primary" @click="task = 'saveShipment'">
                    <i v-spinner="saving[shipment.id]" icon="save"></i>Opslaan</button>
            </div>


        </form>
    </v-modal>
</div>
</template>

<script>

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


</script>