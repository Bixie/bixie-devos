<?php
/**
 * @var Bixie\Gls\Data\Label $label
 * @var string $primary_code
 * @var string $secondary_code
 * @var string $gls_parcel_number
 * @var string $domestic_parcel_number_nl
 */

$label_version = 'E2.00.1';
$imagepath = 'components/com_bix_devos/vendor/bixie/gls/templates/';


?>
<style><?=file_get_contents(__DIR__.'/style/default.css')?></style>
<table class="border-2" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="25%" align="left" class="pad-10"><h1><?=$label['outbound_sorting_flag']?></h1></td>
		<td width="15%" align="center" class="bg-black pad-vert-10"><h1><?=$label['inbound_sorting_flag']?></h1></td>
		<td width="30%" align="right" class="pad-10"><h1><?=$label['inbound_country_code']?></h1></td>
		<td width="30%" align="right" class="bg-black pad-10"><h1><?=$label['final_location']?></h1></td>
	</tr>
</table>
<table class="border-2-bot" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="20%" align="left" class="pad-10"><h1><?=$label['tour_number']?></h1></td>
		<td width="30%" valign="top" class="">
			<h6><?=$label['zip_code_label']?></h6>
			<h2><?=$label['receiver_zip_code']?></h2>
		</td>
		<td width="30%" valign="top" class="">
			<h6><?=$label['track_id_label']?></h6>
			<h2><?=$label['track_id']?></h2>
		</td>
		<td width="20%" align="right" class="pad-10">
			<h1>
				<?=$label['express_service_flag']?>
				<?=$label['express_service_flag_sat']?>
				<?=$label['handling_information_2']?>
				<?=$label['express_flag']?>
				<?=$label['cash_flag']?>
				<?=$label['handling_information_5']?>
			</h1>
		</td>
	</tr>
</table>
<table class="border-2-bot" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="25%" align="left" class="pad-10">
			<img src="<?=$primary_code?>" alt="primary_code"/>
		</td>
		<td width="50%" align="center" class="">
			<?php if ($gls_parcel_number) : ?>
				<img src="<?=$gls_parcel_number?>" alt="gls_parcel_number"/>
			<?php else: ?>
				<br>
			<?php endif; ?>
		</td>
		<td width="25%" align="right" class="pad-10">
			<img src="<?=$secondary_code?>" alt="secondary_code"/>
		</td>
	</tr>
</table>
<table class="border-1-bot" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="10%" align="right"><h5><?=$label['pickup_location']?></h5></td>
		<td width="5%" align="center"><h5><?=$label['station_id']?></h5></td>
		<td width="15%" align="right"><h6><?=$label['date']?></h6></td>
		<td width="10%"><h6>&nbsp;<?=$label['time']?></h6></td>
		<td width="10%" align="right"><h5><?=$label['parcel_weight']?> kg</h5></td>
		<td width="20%" align="center"><h6><?=$label['parcel_sequence']?>/<?=$label['parcel_quantity']?></h6></td>
		<td width="20%"><h6>RTG <?=$label['routing_date']?></h6></td>
		<td width="10%"><h6><?=$label_version?></h6></td>
	</tr>
</table>
<table class="" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2" width="60%" class="border-1-bot pad-10">
			<h6><?=$label['service_type_1_text']?:'&nbsp;'?></h6>
			<h3><?=$label['service_type_1_value']?:'&nbsp;'?></h3>
			<h3><?=$label['text_label_and_phone']?:'&nbsp;'?></h3>
			<h3><?php //missing 753 ?>&nbsp;</h3>
		</td>
		<td width="40%" class="border-1-left pad-15">
			<div class="rotate-90">
				<h5>Afzender:</h5>
				<h4><?=$label['sender_name_1']?></h4>
				<h4><?=$label['sender_name_2']?:'&nbsp;'?></h4>
				<h4><?=$label['sender_name_street']?></h4>
				<h4><?=$label['sender_name_country']?> <?=$label['sender_zip']?> <?=$label['sender_city']?></h4>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="border-1-bot pad-5">
			<h3><?=$label['receiver_name_1']?></h3>
			<h4><?=$label['receiver_name_2']?:'&nbsp;'?></h4>
			<h4><?=$label['receiver_name_3']?:'&nbsp;'?></h4>
			<h3><?=$label['receiver_street']?></h3>
			<h4><?=$label['receiver_zip_code']?> <?=$label['receiver_place']?></h4>
		</td>
		<td class="border-1-left pad-10">
			<div class="rotate-90">
				<h5><?=$label['customer_id_label']?></h5>
				<h4 class="mar-5-top"><?=$label['sap_number']?></h4>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center" class="border-1-bot pad-10">
			<?php if ($domestic_parcel_number_nl) : ?>
				<img src="<?=$domestic_parcel_number_nl?>" alt="domestic_parcel_number_nl"/><br>
				<?=$label['domestic_parcel_number_nl']?>
			<?php else: ?>
				<br><br>
			<?php endif; ?>
		</td>
		<td class="border-1-left pad-10">
			<div class="rotate-90">
				<h5 class="mar-5-top"><?=$label['contact_id_label']?></h5>
				<h4 class="mar-5-top"><?=$label['contact_id']?></h4>
			</div>
		</td>
	</tr>
	<tr>
		<td width="10%" class="border-1-bot pad-5-15">
			<p><?=$label['contact_label']?></p>
			<p><?=$label['phone_label']?></p>
			<p><?=$label['note_label']?></p>
			<p><?=$label['note_label']?></p>
			<p><?=$label['ref_no_label']?></p>
		</td>
		<td width="50%" class="border-1-bot pad-5-10">
			<p><?=$label['receiver_contact']?></p>
			<p><?=$label['receiver_phone']?></p>
			<p><?=$label['additional_text_1']?></p>
			<p><?=$label['additional_text_2']?></p>
			<p><?=$label['customer_reference']?></p>
		</td>
		<td class="border-1-left pad-10 border-1-bot">
			<div class="rotate-90">
				<img src="<?=$imagepath?>images/gls_logo.jpg" alt="logo">
			</div>
		</td>
	</tr>
</table>
