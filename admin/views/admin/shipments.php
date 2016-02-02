<?php
$app['scripts']->add('devos-admin-shipments', 'assets/js/admin-shipments.js', array('vue'));
?>
<div id="devos-shipments" class="uk-noconflict">

	<div class="uk-grid">
		<div class="uk-width-medium-1-6">
			<ul class="uk-nav uk-nav-side">
				<li><a href="<?= JRoute::_('index.php?option=com_bix_devos')?>">Dashboard</a></li>
				<li class="uk-active"><a href="<?= JRoute::_('index.php?option=com_bix_devos&p=/shipments')?>">Verzendingen</a></li>
			</ul>

		</div>
		<div class="uk-width-medium-5-6">

			<div class="uk-panel uk-panel-box">
				<gls-shipment :config="config"></gls-shipment>
			</div>

		</div>
	</div>


</div>
