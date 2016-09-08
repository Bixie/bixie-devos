<?php
$app['scripts']->add('devos-admin-shipments-sendcloud', 'assets/js/admin-shipments-sendcloud.js', ['vue', 'uikit-pagination']);
?>
<div id="devos-shipments-sendcloud" class="uk-noconflict">

	<div class="uk-grid">
		<div class="uk-width-medium-1-6">
			<ul class="uk-nav uk-nav-side">
				<li><a href="<?= JRoute::_('index.php?option=com_bix_devos')?>">Dashboard</a></li>
				<li><a href="<?= JRoute::_('index.php?option=com_bix_devos&p=/shipments')?>">Verzendingen GLS</a></li>
				<li class="uk-active"><a href="<?= JRoute::_('index.php?option=com_bix_devos&p=/sendcloud')?>">Verzendingen PostNL</a></li>
				<li><a href="<?= JRoute::_('index.php?option=com_bix_devos&p=/gls-tracking')?>">GLS Tracking</a></li>
			</ul>

		</div>
		<div class="uk-width-medium-5-6">

			<div class="uk-panel uk-panel-box">
				<sendcloud-shipment :config="config" :is-admin="true"></sendcloud-shipment>
			</div>

		</div>
	</div>


</div>
