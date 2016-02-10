<?php
$app['scripts']->add('devos-admin-dashboard', 'assets/js/admin-dashboard.js', array('vue'));
?>

<div id="devos-dashboard" class="uk-noconflict">

	<div class="uk-grid">
		<div class="uk-width-medium-1-5">
			<ul class="uk-nav uk-nav-side">
				<li class="uk-active"><a href="<?= JRoute::_('index.php?option=com_bix_devos')?>">Dashboard</a></li>
				<li><a href="<?= JRoute::_('index.php?option=com_bix_devos&p=/shipments')?>">Verzendingen</a></li>
			</ul>

		</div>
		<div class="uk-width-medium-4-5">
				<dv-settings :config="config" :data="data"></dv-settings>

		</div>
	</div>

</div>
