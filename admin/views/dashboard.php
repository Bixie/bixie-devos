<?php
$app['scripts']->add('devos-dashboard', 'assets/js/dashboard.js', ['vue', 'uikit-pagination']);
?>

<div id="devos-dashboard" class="uk-noconflict">

	<div class="uk-grid">
		<div class="uk-width-medium-1-5">
			<ul class="uk-tab uk-tab-left">
				<li :class="{'uk-active': currentView == 'dashboard'}"><a href="#" @click.prevent="setView('dashboard')">Dashboard</a></li>
				<li :class="{'uk-active': currentView == 'verzendingen'}"><a href="#" @click.prevent="setView('verzendingen')">Verzendingen</a></li>
				<li :class="{'uk-active': currentView == 'afzenders'}"><a href="#" @click.prevent="setView('afzenders')">Afzenders</a></li>
			</ul>

		</div>
		<div class="uk-width-medium-4-5">

			<component :is="currentView" :config="config" :data="data"></component>

		</div>
	</div>

</div>
