<?php
$app['scripts']->add('devos-admin-dashboard', 'assets/js/admin-dashboard.js', ['vue', 'uikit-pagination']);
?>

<div id="devos-dashboard" class="uk-noconflict">

	<div class="uk-grid">
		<div class="uk-width-medium-1-5">
			<ul class="uk-nav uk-nav-side">
				<li class="uk-active"><a href="<?= JRoute::_('index.php?option=com_bix_devos')?>">Dashboard</a></li>
				<li><a href="<?= JRoute::_('index.php?option=com_bix_devos&p=/shipments')?>">Verzendingen GLS</a></li>
				<li><a href="<?= JRoute::_('index.php?option=com_bix_devos&p=/sendcloud')?>">Verzendingen PostNL</a></li>
				<li><a href="<?= JRoute::_('index.php?option=com_bix_devos&p=/gls-tracking')?>">GLS Tracking</a></li>
			</ul>

		</div>
		<div class="uk-width-medium-4-5">
			<div class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
			    <div class="uk-width-medium-1-3">
					<div class="uk-panel uk-panel-space">

						<h3 class="uk-panel-title">Applicatie instellingen</h3>

						<dv-settings :config="config" :data="data"></dv-settings>

					</div>
				</div>
			    <div class="uk-width-medium-2-3">
					<?php if ($sc_user) : ?>

						<div class="uk-panel uk-panel-box">
							<h3 class="uk-panel-title">Sendcloud gebruiker</h3>

							<dl class="uk-description-list-horizontal">
								<dt>Gebruikersnaam</dt>
								<dd><?= $sc_user->username; ?></dd>
								<dt>Bedrijfsnaam</dt>
								<dd><?= $sc_user->company_name; ?></dd>
								<dt>Telefoon</dt>
								<dd><?= $sc_user->telephone; ?></dd>
								<dt>Adres</dt>
								<dd><?= $sc_user->address; ?></dd>
								<dt>Postcode</dt>
								<dd><?= $sc_user->postal_code; ?></dd>
								<dt>Plaats</dt>
								<dd><?= $sc_user->city; ?></dd>
								<dt>Email</dt>
								<dd><?= $sc_user->email; ?></dd>
								<dt>Logo</dt>
								<dd><?= $sc_user->company_logo; ?></dd>
								<dt>Registratiedatum</dt>
								<dd><?= $sc_user->registered; ?></dd>
							</dl>

							<div class="uk-grid">
							    <div class="uk-width-1-3">
									<h3 class="uk-panel-title">Modules</h3>
									<ul class="uk-list uk-list-line">
										<?php foreach ($sc_user->modules as $module) : ?>
											<li class="uk-panel">
												<?php if ($module['activated']) : ?>
													<div class="uk-panel-badge uk-badge-success"><i class="uk-icon-check uk-text-contrast uk-margin-small-left uk-margin-small-right uk-icon-justify"></i></div>
												<?php else: ?>
													<div class="uk-panel-badge uk-badge-danger"><i class="uk-icon-ban uk-text-contrast uk-margin-small-left uk-margin-small-right uk-icon-justify"></i></div>
												<?php endif; ?>
												<h3><?= $module['name']; ?></h3>
											</li>
										<?php endforeach; ?>
									</ul>

								</div>
							    <div class="uk-width-2-3">
									<h3 class="uk-panel-title">Facturen</h3>
									<ul class="uk-list uk-list-line">
										<?php foreach ($sc_user->invoices as $invoice) : ?>
											<li class="uk-panel">
												<?php if ($invoice['isPayed']) : ?>
													<div class="uk-panel-badge uk-badge-success"><i class="uk-icon-check uk-text-contrast uk-margin-small-left uk-margin-small-right uk-icon-justify"></i></div>
												<?php else: ?>
													<div class="uk-panel-badge uk-badge-danger"><i class="uk-icon-ban uk-text-contrast uk-margin-small-left uk-margin-small-right uk-icon-justify"></i></div>
												<?php endif; ?>
												<h3 class="uk-margin-bottom-remove">Factuur <?= $invoice['ref']; ?></h3>
												<small><?= $invoice['date']; ?></small>
												<ul class="uk-subnav uk-subnav-line">
													<li><span>Excl: € <?= number_format($invoice['price_excl'], 2, ',', '.'); ?></span></li>
													<li><span>Incl: € <?= number_format($invoice['price_incl'], 2, ',', '.'); ?></span></li>
													<li>
														<a href="<?= $invoice['items']; ?>" target="_blank">
															<i class="uk-icon-file-pdf-o uk-margin-small-right"></i>
																Download</a>
													</li>
												</ul>
											</li>
										<?php endforeach; ?>
									</ul>

								</div>
							</div>
						</div>

					<?php endif; ?>
				</div>
			</div>

		</div>
	</div>

</div>
