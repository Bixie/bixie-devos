<template>
<div>

    <div v-if="error" class="uk-alert uk-alert-danger">{{ error }}</div>

    <div class="uk-flex uk-flex-space-between uk-flex-middle">
        <div>
            <div class="uk-form-icon">
                <i class="uk-icon-search"></i>
                <input class="uk-margin-remove uk-form-width-medium" type="search" v-model="filter.search" debounce="500">
            </div>
        </div>
        <div>
            <small>{{ total }} verzending<span v-show="total != 1">en</span></small>
        </div>
        <div>
            <button class="uk-button" @click="newDefault">
                <i v-spinner="editloading[-1]" icon="plus"></i>Standaard</button>
            <div class="uk-button-group">
                <button class="uk-button" @click="newExpress('')">
                    <i v-spinner="editloading[-2]" icon="bolt"></i>Express 17 uur</button>
                <button class="uk-button" @click="newExpress('T12')">
                    <i v-spinner="editloading['T12']" icon="bolt"></i>12 uur</button>
                <button class="uk-button" @click="newExpress('T9')">
                    <i v-spinner="editloading['T9']" icon="bolt"></i>9 uur</button>
            </div>
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
                    <dd v-if="shipment.data.express_service_flag">
                        <i class="uk-icon-bolt uk-icon-justify uk-margin-small-right" title="Express" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ getValueLabel('express_flag', shipment.data.express_flag) }}</span>
                        <i v-show="shipment.data.express_service_flag" class="uk-icon-flag uk-text-danger uk-margin-small-left" title="Express Service aan" data-uk-tooltip="{delay: 200}"></i>
                        <i v-show="shipment.data.express_service_flag_sat" class="uk-icon-calendar-plus-o uk-text-danger uk-margin-small-left" title="Saturday Service aan" data-uk-tooltip="{delay: 200}"></i>
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
                            <i class="uk-icon-download uk-margin-small-right"></i>
                            Download PDF</a>
                    </li>
                    <li v-show="printEnabled && pdfPrinter && shipment.domestic_parcel_number_nl" class="uk-text-truncate">
                        <a @click="printPdf(shipment.domestic_parcel_number_nl)">
                            <i class="uk-icon-file-pdf-o uk-margin-small-right"></i>
                            Print PDF</a>
                    </li>
                    <li v-show="printEnabled && zplPrinter && shipment.zpl_template" class="uk-text-truncate">
                        <a @click="printZpl(shipment.zpl_template)">
                            <i class="uk-icon-barcode uk-margin-small-right"></i>
                            Print etiket</a>
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
    <div v-else class="uk-margin uk-text-center"><i class="uk-icon-circle-o-notch uk-icon-spin"></i></div>

    <div v-if="printEnabled" class="uk-grid uk-grid-width-medium-1-3 uk-form uk-flex-middle" data-uk-grid-margin>
        <div>
            <i class="uk-icon-print uk-text-success uk-margin-small-right"></i>
            <label class="uk-form-label uk-margin-right">Printen ingeschakeld</label>
        </div>
        <div>
            <i class="uk-icon-barcode uk-margin-small-right"></i>
            <input type="text" v-model="zplPrinter" class="uk-form-width-medium uk-form-blank"/>
        </div>
        <div>
            <i class="uk-icon-file-pdf-o uk-margin-small-right"></i>
            <input type="text" v-model="pdfPrinter" class="uk-form-width-medium uk-form-blank"/>
        </div>
    </div>


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
                    <div class="uk-margin-left" v-show="shipment.data.express_service_flag">
                        <i class="uk-icon-bolt uk-margin-small-right" title="Express" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ getValueLabel('express_flag', shipment.data.express_flag) }}</span>
                        <i class="uk-icon-flag uk-text-danger uk-margin-small-left" title="Express Service" data-uk-tooltip="{delay: 200}"></i>
                        <a :class="{'uk-text-danger': shipment.data.express_service_flag_sat}"
                           @click="shipment.data.express_service_flag_sat = !shipment.data.express_service_flag_sat"
                           class="uk-icon-calendar-plus-o uk-margin-small-left" title="Saturday Service" data-uk-tooltip="{delay: 200}"></a>
                    </div>
                </div>
            </div>
        </div>

        <partial name="gls-form"></partial>

    </v-modal>
</div>
</template>

<script>

    module.exports = {

        props: ['config'],

        data: function () {
            return {
                zplPrinter: '',
                pdfPrinter: '',
                printEnabled: false,
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

        created: function () {
            this.ninjaPrint = this.$ninjaPrint(this);
            this.zplPrinter = this.config.user.zpl_printer;
            this.pdfPrinter = this.config.user.pdf_printer;
        },

        ready: function () {
            this.load(this.page);
        },

        events: {
            'ninjaprint.enabled': function () {
                this.printEnabled = true;
            },
            'ninjaprint.result': function (result) {
                UIkit.notify(result.message, result.success ? 'success' : 'danger');
            }
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
            newDefault: function () {
                this.editShipment(-1, {
                    product_short_description: 'BP',
                    data: {
                        express_flag: '',
                        express_service_flag: false,
                        inbound_country_code: 'NL'
                    }
                });
            },
            newExpress: function (flag) {
                this.editShipment((flag === '' ? -2 : flag), {
                    product_short_description: 'EP',
                    data: {
                        express_flag: flag,
                        express_service_flag: true,
                        inbound_country_code: 'NL'
                    }
                });
            },
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
            createShipment: function (data) {
                return _.merge({
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
                        express_service_flag: false,
                        express_service_flag_sat: false,
                        inbound_country_code: 'NL'
                    },
                    pdf_url: ''
                }, (data || {}));
            },
            editShipment: function (id, data) {
                var shipment = _.find(this.shipments, 'id', id) || this.createShipment(data),
                        def = _.size(this.senders) ? _.find(this.senders, 'def', 1) || _.find(this.senders, 'state', 1) : {id: 0};
                this.shipment = shipment;
                if (_.size(this.senders) === 0) {
                    Vue.set(this.editloading, id, true);
                    this.$http.get('/api/sender').then(function (res) {
                        var def = _.find(res.data.senders, 'def', 1) || _.find(res.data.senders, 'state', 1);
                        if (def.id) {
                            this.$set('senders', res.data.senders);
                            this.$set('shipment.sender_id', def.id);
                            this.$refs.editshipmentmodal.open();
                        } else {
                            this.$set('error', 'Geen afzender gevonden');
                        }
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
            printZpl: function (zpl_template) {
                this.ninjaPrint.zpl(this.zplPrinter, zpl_template);
            },
            printPdf: function (domestic_parcel_number_nl) {
                this.$http.get('/api/shipment/pdf/' + domestic_parcel_number_nl, {'string': 1}).then(function (res) {
                    this.ninjaPrint.pdf(this.pdfPrinter, res.data);
                }, function (res) {
                    UIkit.notify(res.data.message || res.data, 'danger');
                });
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
                        options = {
                            '': 'Volgende dag 17.00 uur',
                            'T9': 'Volgende dag 9.00 uur',
                            'T12': 'Volgende dag 12.00 uur'
                        };
                    break;
                case 'product_short_description':
                        options = {
                            'BP': 'Business parcel',
                            'EBP': 'Euro business parcel',
                            'EP': 'Express parcel'
                        };
                    break;
                }
                if (options[value]) {
                    return options[value];
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
            },
            'shipment.data.inbound_country_code': function (value) {
                if (value !== 'NL') {
                    this.shipment.product_short_description =  'EBP';
                } else if (this.shipment.product_short_description === 'EBP') {
                    this.shipment.product_short_description =  'BP';
                }
            },
            'shipment.product_short_description': function (value) {
                this.shipment.data.express_service_flag = value === 'EP';
                if (value !== 'EP') {
                    this.shipment.data.express_service_flag_sat = false;
                    this.shipment.data.express_flag = false;
                }
            }
        },

        partials: {
            'gls-form': require('../../templates/gls-form.html')
        },

        fields1: {
            'receiver_name_1': {
                type: 'text',
                label: 'Ontvanger naam 1 *',
                attrs: {'name': 'name', 'class': 'uk-width-1-1', 'required': true}
            },
            'data.inbound_country_code': {
                type: 'select',
                label: 'Land verzending *',
                options: {
                    'Nederland': 'NL',
                    'Duitsland': 'DE',
                    'Groot Brittanië': 'GB',
                    'België': 'BE'
                },
                attrs: {'class': 'uk-width-1-1', 'required': true}
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
                attrs: {'name': 'referentie', 'class': 'uk-width-1-1', maxLength: 10, 'required': true}
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