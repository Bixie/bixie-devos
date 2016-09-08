<?php
/**
 */
$app['scripts']->add('devos-admin-gls-tracking', 'assets/js/admin-gls-tracking.js', ['vue', 'uikit-notify', 'uikit-pagination']);
?>
<div id="devos-gls-tracking" class="uk-noconflict">


	<div class="uk-grid">
		<div class="uk-width-medium-1-6">
			<ul class="uk-nav uk-nav-side">
				<li><a href="<?= JRoute::_('index.php?option=com_bix_devos')?>">Dashboard</a></li>
				<li><a href="<?= JRoute::_('index.php?option=com_bix_devos&p=/shipments')?>">Verzendingen GLS</a></li>
				<li><a href="<?= JRoute::_('index.php?option=com_bix_devos&p=/sendcloud')?>">Verzendingen PostNL</a></li>
				<li class="uk-active"><a href="<?= JRoute::_('index.php?option=com_bix_devos&p=/gls-tracking')?>">GLS Tracking</a></li>
			</ul>

		</div>
		<div class="uk-width-medium-5-6">

			<div class="uk-panel uk-panel-box">

				<div class="uk-flex uk-flex-top">

					<button @click="syncGls" class="uk-button uk-button-success">
						<i class="uk-icon-refresh uk-margin-small-right" :class="{'uk-icon-spin': syncing}"></i>
						Synchroniseer nu met GLS</button>

					<div v-show="trackings.length" class="uk-margin-left">
						<a @click="trackings = []" class="uk-close uk-float-right"></a>
						<div v-for="tracking in trackings" class="uk-alert"
							 :class="{'uk-alert-success': !tracking.error, 'uk-alert-danger': tracking.error}">
							{{ tracking.filename }}
							<strong v-if="tracking.error" class="uk-display-block">{{ tracking.error }}</strong>
						</div>
					</div>

				</div>

				<table class="uk-table" v-show="glstrackings">
					<thead>
					<tr>
						<th>Bestand</th>
						<th>Klantnummer</th>
						<th>Datum import</th>
						<th>Datum bereik</th>
						<th>Parcels</th>
						<th>Events</th>
						<th>Status</th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="7">
							<v-pagination :page.sync="page" :pages="pages" v-show="pages > 1"></v-pagination>
						</td>
					</tr>
					</tfoot>
					<tbody>
					<tr v-for="glstracking in glstrackings">
						<td>
							{{ glstracking.filename }}
						</td>
						<td>
							{{ glstracking.gls_number }}
						</td>
						<td>
							{{ glstracking.created | date }}
						</td>
						<td>
							{{ glstracking.date_from | date }}<br>
							{{ glstracking.date_to | date }}
						</td>
						<td class="uk-text-center">
							<template v-if="glstracking.parcels.counter">
								<div class="uk-badge uk-badge-notification uk-badge-success">{{
									glstracking.parcels.counter.parcels || 0 }}
								</div>
								<log-modal :messages="glstracking.parcels.errors" label="Fouten"
										   cls="uk-badge-danger"></log-modal>
								<log-modal :messages="glstracking.parcels.warnings" label="Waarschuwingen"
										   cls="uk-badge-warning"></log-modal>
								<log-modal :messages="glstracking.parcels.messages" label="Berichten"></log-modal>
							</template>
						</td>
						<td class="uk-text-center">
							<template v-if="glstracking.events.counter">
								<div class="uk-badge uk-badge-notification uk-badge-success">{{
									glstracking.events.counter.events || 0 }}
								</div>
								<log-modal :messages="glstracking.events.errors" label="Fouten"
										   cls="uk-badge-danger"></log-modal>
								<log-modal :messages="glstracking.events.warnings" label="Waarschuwingen"
										   cls="uk-badge-warning"></log-modal>
								<log-modal :messages="glstracking.events.messages" label="Berichten"></log-modal>
							</template>

						</td>
						<td>
							{{ getStateName(glstracking.state) }}
						</td>
					</tr>
					<tr v-show="!total">
						<td colspan="7" class="uk-text-center">
							<div class="uk-alert">Geen trackings gevonden.</div>
						</td>
					</tr>
					</tbody>
				</table>
				<div v-else class="uk-margin uk-text-center"><i class="uk-icon-circle-o-notch uk-icon-spin"></i></div>


			</div>

		</div>
	</div>


</div>
