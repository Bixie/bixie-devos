<template>
<div>

    <div v-if="error" class="uk-alert uk-alert-danger">{{ error }}</div>

    <div class="uk-flex uk-flex-space-between uk-flex-middle uk-flex-wrap" data-uk-margin="">
        <div>
            <div class="uk-form-icon">
                <i class="uk-icon-search"></i>
                <input class="uk-margin-remove uk-form-width-medium" placeholder="Pakketnummer, referentie, adres, email..."
                       type="search" v-model="filter.search" debounce="500">
            </div>
        </div>
        <div v-if="isAdmin">
            <div class="uk-form-icon">
                <i class="uk-icon-search"></i>
                <input class="uk-margin-remove uk-form-width-small" placeholder="De Vos klantnummer"
                       type="search" v-model="filter.klantnummer" debounce="500">
            </div>
        </div>
        <div v-if="isAdmin">
            <div class="uk-form-icon">
                <i class="uk-icon-search"></i>
                <input class="uk-margin-remove uk-form-width-small" placeholder="GLS klantnummer"
                       type="search" v-model="filter.gls_customer_number" debounce="500">
            </div>
        </div>
        <div>
            <button class="uk-button uk-button-mini" @click="exportShipments"><i class="uk-icon-download"></i></button>
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
                    <dd v-if="shipment.data.express_flag">
                        <i class="uk-icon-bolt uk-icon-justify uk-margin-small-right" title="Express" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ getValueLabel('express_service_flag', shipment.data.express_service_flag) }}</span>
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
                        <span>{{ shipment.date_of_shipping | datetime }}</span>
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
                    <dd v-if="shipment.receiver_email">
                        <i class="uk-icon-envelope-o uk-icon-justify uk-margin-small-right" title="Email ontvanger" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.receiver_email }}</span>
                    </dd>
                </dl>
            </td>
            <td>
                <dl>
                    <dd>
                        <i class="uk-icon-hashtag uk-icon-justify uk-margin-small-right" title="Klantnummer" data-uk-tooltip="{delay: 200}"></i>
                        <strong>{{ shipment.klantnummer }}</strong>
                    </dd>
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
                    <dd>
                        <i class="uk-icon-comment-o uk-icon-justify uk-margin-small-right" title="Afzender contact" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.sender_contact }}</span>
                    </dd>
                    <dd>
                        <i class="uk-icon-phone uk-icon-justify uk-margin-small-right" title="Afzender telefoon" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.sender_phone | nrquotes }}</span>
                    </dd>
                    <dd>
                        <i class="uk-icon-envelope-o uk-icon-justify uk-margin-small-right" title="Afzender email" data-uk-tooltip="{delay: 200}"></i>
                        <span>{{ shipment.sender_email }}</span>
                    </dd>
                </dl>
            </td>
            <td>
                <em v-if=" shipment.data.gls_status">{{ shipment.data.gls_status }}</em><br>
                {{ shipment.statusname }}
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
                    <li v-show="shipment.png_url">
                        <a :href="shipment.png_url" data-uk-lightbox>
                            <i class="uk-icon-file-image-o uk-margin-small-right"></i>
                            Bekijk etiket</a>
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
                    <div v-if="senders" class="uk-margin-left uk-flex uk-flex-middle">
                        <i class="uk-icon-user uk-margin-small-right" title="Afzender" data-uk-tooltip="delay: 200, pos: 'bottom'"></i>
                        <select v-model="shipment.sender_id" id="form-sender_id_top" class="uk-form-small uk-form-width-small uk-margin-remove">
                            <option v-for="sender in senders" :value="sender.id">{{ sender.sender_name_1 }}</option>
                        </select>
                    </div>
                    <div class="uk-margin-small-left" v-show="shipment.receiver_email && shipment.data.send_email">
                        <i class="uk-icon-envelope-o uk-text-success" title="Email wordt naar ontvanger verstuurd" data-uk-tooltip="delay: 200, pos: 'bottom'"></i>
                    </div>
                    <div class="uk-margin-left">
                        <i class="uk-icon-cubes uk-margin-small-right" title="Product type" data-uk-tooltip="delay: 200, pos: 'bottom'"></i>
                        <span>{{ getValueLabel('product_short_description', shipment.product_short_description) }}</span>
                    </div>
                    <div class="uk-margin-left" v-show="shipment.data.express_service_flag">
                        <i class="uk-icon-bolt uk-margin-small-right" title="Express" data-uk-tooltip="delay: 200, pos: 'bottom'"></i>
                        <span>{{ getValueLabel('express_service_flag', shipment.data.express_service_flag) }}</span>
                        <i class="uk-icon-flag uk-text-danger uk-margin-small-left" title="Express Service" data-uk-tooltip="delay: 200, pos: 'bottom'"></i>
                        <a :class="{'uk-text-danger': shipment.data.express_service_flag_sat}"
                           @click="shipment.data.express_service_flag_sat = !shipment.data.express_service_flag_sat"
                           class="uk-icon-calendar-plus-o uk-margin-small-left" title="Saturday Service" data-uk-tooltip="delay: 200, pos: 'bottom'"></a>
                    </div>
                </div>
            </div>
        </div>

        <partial name="gls-form"></partial>

    </v-modal>

    <v-modal v-ref:downloadcsvmodal>
        <div class="uk-modal-header">
            <h3 class="uk-flex-item-1">Download als CSV</h3>
        </div>
        <div class="uk-form">
            <div class="uk-grid uk-grid-width-medium-1-3" data-uk-grid-margin>
                <div>
                    <label for="created_from">Datum vanaf</label>
                    <input type="date" id="created_from" class="uk-width-1-1" v-model="exportFilter.created_from"/>
                </div>
                <div>
                    <label for="created_to">Datum tot</label>
                    <input type="date" id="created_to" class="uk-width-1-1" v-model="exportFilter.created_to"/>
                </div>
                <div>
                    <label for="state">Status</label>
                    <select v-model="exportFilter.state" class="uk-width-1-1" id="state" size="4">
                        <option value="">Alle</option>
                        <option value="0">Verwijderd</option>
                        <option value="1">Aangemaakt</option>
                        <option value="2">Gescand</option>
                    </select>
                </div>
            </div>

        </div>
        <div class="uk-modal-footer uk-text-right">
            <button type="button" class="uk-button uk-margin-small-right uk-modal-close">Sluiten</button>
            <button type="submit" class="uk-button uk-button-primary" @click="downloadCSV">
                <i v-spinner="exporting" icon="download"></i>Downloaden</button>

        </div>
    </v-modal>

</div>
</template>

<script>

    module.exports = {

        props: ['config', 'isAdmin'],

        data: function () {
            return _.merge({
                address_id: '',
                zplPrinter: '',
                pdfPrinter: '',
                printEnabled: false,
                currentTab: 'pakket',
                postcode: '',
                huisnr: '',
                toev: '',
                task: '',
                error: '',
                progress: 0,
                progressmessage: '',
                progresserror: '',
                lookup: false,
                sending: false,
                exporting: false,
                saving: {},
                editloading: {},
                shipment: {
                    id: 0,
                    sender_id: 0,
                    parcel: {},
                    data: {}
                },
                shipments: false,
                countries: {},
                senders: {},
                exportFilter: {
                    created_from: '',
                    created_to: '',
                    state: ''
                },
                filter: {
                    search: '',
                    klantnummer: '',
                    gls_customer_number: '',
                    order: 'created',
                    dir: 'desc',
                    limit: 10
                },
                count: 0,
                total: 0,
                pages: 1,
                page: 0,
                form: {}
            }, window.$data)
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
                this.postcode = '';
                this.huisnr = '';
                this.toev = '';
                this.editShipment(-1, {
                    product_short_description: 'BP',
                    data: {
                        express_service_flag: '',
                        express_flag: false,
                        inbound_country_code: 'NL'
                    }
                });
            },
            newExpress: function (flag) {
                this.postcode = '';
                this.huisnr = '';
                this.toev = '';
                this.editShipment((flag === '' ? -2 : flag), {
                    product_short_description: 'EP',
                    data: {
                        express_service_flag: flag,
                        express_flag: true,
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
                    parcel: {},
                    data: {
                        track_trace: '',
                        label_template: 'gls_default',
                        express_service_flag: '',
                        send_email: false,
                        send_email_type: 'P',
                        express_flag: false,
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
                            if (!this.shipment.sender_id) this.$set('shipment.sender_id', def.id);
                        } else {
                            this.$set('error', 'Geen afzender gevonden');
                            this.editloading = {};
                        }
                    }, function (res) {
                         this.$set('error', res.data.message || res.data);
                         this.editloading = {};
                    }).then(function () {
                        this.$refs.editshipmentmodal.open();
                        this.editloading = {};

                    });
                 } else {
                    if (!this.shipment.sender_id) this.$set('shipment.sender_id', def.id);
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
            exportShipments: function () {
                this.$refs.downloadcsvmodal.open();
            },
            downloadCSV: function () {
                var filter = {filter: _.merge({}, this.filter, this.exportFilter)};
                window.location = 'index.php?p=%2Fapi%2Fshipment%2Fcsv&option=com_bix_devos&api=1&' + jQuery.param(filter);
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
                case 'express_service_flag':
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
            },

            postcodeLookup: function () {
                var data = {
                    postcode: this.postcode.replace(/\s+/, '').toUpperCase(),
                    huisnr: Number(this.huisnr),
                    toev: this.toev
                };

                if (data.postcode.length != 6 || !data.huisnr) return;
                this.lookup = true;

                this.$http.post('/api/shipment/postcode', data).then(function (res) {
                    if (res.data.result) {
                        console.log(res.data);
                        this.$set('shipment.receiver_zip_code', res.data.result.postcode);
                        this.$set('shipment.receiver_street', res.data.result.street + ' '
                                + res.data.result.houseNumber
                                + (res.data.result.houseNumberAddition ? ' ' + res.data.result.houseNumberAddition : ''));
                        this.$set('shipment.receiver_place', res.data.result.city);
                    }
                    this.lookup = false;
                }, function (res) {
                    console.log(res.data);
                    this.lookup = false;
                    UIkit.notify(res.data.message || res.data, 'danger');
                });


            },
            pickAddress: function (address) {
                var xref = {
                    zip: 'zip_code',
                    city: 'place'
                };
                if (address) {
                    address.phone = address.phone.replace(new RegExp('"', 'g'), '');
                    ['name_1', 'name_2', 'street', 'zip', 'city', 'email', 'contact', 'phone'].forEach(function (key) {
                        var fkey = xref[key] || key;
                        this.$set('shipment.receiver_' + fkey, address[key] || '');
                    }.bind(this));
                    this.$set('shipment.additional_text_1', address.additional_text || '');
                    this.$set('shipment.data.inbound_country_code', address.country || 'NL');
                }
            },
            getAdresType: function (adresType) {
                return {
                            'DELADR' : 'Afleveradres',
                            'REQADR' : 'Afzenderadres'
                        }[adresType] || adresType;
            }

        },

        watch: {

            'shipment.sender_id': function (id) {
                var sender = this.senders[id];
                if (sender) {
                    sender.sender_phone = sender.sender_phone.replace(new RegExp('"', 'g'), '');
                    ['sender_name_1', 'sender_name_2', 'sender_street', 'sender_zip', 'sender_city', 'sender_country', 'sender_email'].forEach(function (key) {
                        this.$set('shipment.' + key, sender[key] || '');
                    }.bind(this));
                    ['sender_contact', 'sender_phone', 'message_subject'].forEach(function (key) {
                        if (key !== 'message_subject' || !this.shipment.data[key]) { //prevent subject from override
                            this.$set('shipment.data.' + key, sender[key] || '');
                        }
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
                this.shipment.data.express_flag = (value === 'EP');
                if (value !== 'EP') {
                    this.shipment.data.express_service_flag_sat = false;
                    this.shipment.data.express_flag = false;
                }
            },
            'postcode + huisnr + toev': function () {
                this.postcodeLookup();
            }
        },

        partials: {
            'gls-form': require('../../templates/gls-form.html')
        },

        components: {
            'address-picker': require('./address-picker.vue')
        },

        fields1: {
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
            'receiver_email': {
                type: 'email',
                label: 'Email ontvanger',
                attrs: {'name': 'email', 'class': 'uk-width-1-1'}
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

        fields2: {
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

        fields3: {
            'data.send_email': {
                type: 'checkbox',
                label: 'Stuur mail',
                optionLabel: 'Stuur e-mail naar gebruiker via GLS',
                attrs: {}
            },
            'data.send_email_type': {
                type: 'select',
                label: 'E-mail type',
                options: {
                    'Prive mail': 'P',
                    'Zakelijke mail': 'B'
                },
                attrs: {'class': 'uk-form-width-medium'}
            },
            'data.message_subject': {
                type: 'text',
                label: 'Onderwerp email',
                attrs: {'class': 'uk-width-1-1'}
            },
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

        fields4: {
            'sender_name_1': {
                type: 'text',
                label: 'Afzender naam 1 *',
                attrs: {'class': 'uk-form-width-medium', 'required': true, 'maxlength': 30}
            },
            'sender_name_2': {
                type: 'text',
                label: 'Afzender naam 2',
                attrs: {'class': 'uk-form-width-medium', 'maxlength': 50}
            },
            'sender_street': {
                type: 'text',
                label: 'Afzender adres *',
                attrs: {'class': 'uk-form-width-medium', 'required': true, 'maxlength': 50}
            },
            'sender_zip': {
                type: 'text',
                label: 'Afzender postcode *',
                attrs: {'class': 'uk-form-width-medium', 'required': true, 'maxlength': 10}
            },
            'sender_city': {
                type: 'text',
                label: 'Afzender plaats *',
                attrs: {'class': 'uk-form-width-medium', 'required': true, 'maxlength': 30}
            },
            'sender_country': {
                type: 'text',
                label: 'Afzender land *',
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'sender_email': {
                type: 'email',
                label: 'Email',
                attrs: {'name': 'sender_email', 'class': 'uk-width-1-1', 'maxlength': 70}
            },
            'data.sender_contact': {
                type: 'text',
                label: 'Contactpersoon *',
                attrs: {'name': 'sender_contact', 'class': 'uk-width-1-1', 'maxlength': 30, 'required': true}
            },
            'data.sender_phone': {
                type: 'text',
                label: 'Telefoon *',
                attrs: {'name': 'sender_phone', 'class': 'uk-width-1-1', 'maxlength': 15, 'required': true}
            }
        }

    };


</script>