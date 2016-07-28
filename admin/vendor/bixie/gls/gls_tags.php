<?php
use Bixie\Gls\Data\Tag;
return [
	'printer_template' => new Tag(
		'021',
		'printer_template',
		'Printer Template: ZPL/IPL/DPL',
		'zebrazpl',
		'C',
		false,
		25
	),
	'software_name' => new Tag(
		'050',
		'software_name',
		'Software Name',
		'Shippingsoftware',
		'C',
		false,
		20
	),
	'software_version' => new Tag(
		'051',
		'software_version',
		'Version Of The Shipping Software',
		'V1.14',
		'C',
		false,
		20
	),
	'mode' => new Tag(
		'090',
		'mode',
		'Parameter According To Function NOPRINT,NOSAVE,UPDATE',
		'NOPRINT',
		'C',
		false,
		20
	),
	'inbound_country_code' => new Tag(
		'100',
		'inbound_country_code',
		'Inbound Country Code',
		'GB',
		'C',
		true,
		3
	),
	'express_service_flag' => new Tag(
		'200',
		'express_service_flag',
		'Express Service Flag (see table express)',
		'T9',
		'C',
		false,
		3
	),
	'express_service_flag_sat' => new Tag(
		'201',
		'express_service_flag_sat',
		'Express Service Flag (only Saturday delivery)',
		'SCB',
		'C',
		false,
		3
	),
	'cash_flag' => new Tag(
		'203',
		'',
		'Always Fixed As “C” For CASH shipments',
		'C',
		'C',
		false,
		1
	),
	'express_flag' => new Tag(
		'204',
		'express_flag',
		'Always Fixed As : “T” For Express',
		'T',
		'C',
		false,
		1
	),
	'product_short_description' => new Tag(
		'206',
		'product_short_description',
		'Product Short Description. BP = Business Parcel, EP = Express Parcel, EBP = Euro Business Parcel',
		'EP',
		'C',
		true,
		3
	),
	'barcode_article_flag' => new Tag(
		'207',
		'barcode_article_flag',
		'Barcode Article Flag Express Services',
		'T9',
		'C',
		false,
		2
	),
	'receiver_zip_code' => new Tag(
		'330',
		'receiver_zip_code',
		'Receiver Zip Code',
		'CV379HY',
		'C',
		true,
		10
	),
	'gls_parcel_number' => new Tag(
		'400',
		'gls_parcel_number',
		'GLS-Parcel Number 11 Digit + 1 Check Digit',
		'201110001218',
		'N',
		false,
		12
	),
	'parcel_weight' => new Tag(
		'530',
		'parcel_weight',
		'Parcel Weight, Decimal Divider „,“ (Comma)',
		'31,5',
		'N',
		false,
		'2,1'
	),
	'date_of_shipping' => new Tag(
		'545',
		'date_of_shipping',
		'Date Of Shipping, Format DD.MM.YYYY',
		'21.01.2009',
		'C',
		false,
		10
	),
	'domestic_parcel_number_nl' => new Tag(
		'620',
		'domestic_parcel_number_nl',
		'Domestic Parcel Number NL',
		'12345678000927',
		'C',
		false,
		14
	),
	'service_type_1_text' => new Tag(
		'750',
		'service_type_1_text',
		'Service Type 1 Text',
		'EXPRESS-Service',
		'C',
		false,
		50
	),
	'service_type_1_value' => new Tag(
		'751',
		'service_type_1_value',
		'Service Type 1 Value',
		'Volgende werkdag voor 09:00 uur',
		'C',
		false,
		50
	),
	'text_label_and_phone' => new Tag(
		'752',
		'text_label_and_phone',
		'Text on label + phone number receiver',
		'Tel. nr. geadreseerde: +31 30 2417800',
		'C',
		false,
		50
	),
	'receiver_phone' => new Tag(
		'758',
		'receiver_phone',
		'Receiver Phone No.',
		'+31 30 2417800',
		'C',
		false,
		50
	),
	'receiver_contact' => new Tag(
		'759',
		'receiver_contact',
		'Receiver Contact Person',
		'John Doe',
		'C',
		false,
		50
	),
	'receiver_email' => new Tag(
		'1229',
		'receiver_email',
		'Receiver Email',
		'john@example.com',
		'C',
		false,
		70
	),
	'sender_email' => new Tag(
		'1235',
		'sender_email',
		'Sender Email',
		'john@company.com',
		'C',
		false,
		70
	),
	'send_email' => new Tag(
		'859',
		'send_email',
		'Send Email',
		'1',
		'N',
		false,
		1
	),
	'gls_customer_number' => new Tag(
		'805',
		'gls_customer_number',
		'Customer Number GLS',
		'12345678',
		'N',
		true,
		8
	),
	'sender_name_1' => new Tag(
		'810',
		'sender_name_1',
		'Sender Name 1',
		'General Logistics Systems',
		'C',
		true,
		30
	),
	'sender_name_2' => new Tag(
		'811',
		'sender_name_2',
		'Sender Name 2',
		'Mr.Paul',
		'C',
		false,
		50
	),
	'sender_street' => new Tag(
		'820',
		'sender_street',
		'Sender Street',
		'Traunuferstraße 42',
		'C',
		true,
		50
	),
	'sender_country' => new Tag(
		'821',
		'sender_country',
		'Sender –Country Indicator',
		'AT',
		'C',
		true,
		2
	),
	'sender_zip' => new Tag(
		'822',
		'sender_zip',
		'Sender –Zip Code',
		'4052',
		'C',
		true,
		10
	),
	'sender_city' => new Tag(
		'823',
		'sender_city',
		'Sender City',
		'ANSFELDEN',
		'C',
		true,
		30
	),
	'sender_contact' => new Tag(
		'825',
		'sender_contact',
		'Sender Contact name',
		'Sjaak',
		'C',
		true,
		30
	),
	'sender_phone' => new Tag(
		'826',
		'sender_phone',
		'Sender Phone',
		'085-1548743',
		'C',
		true,
		15
	),
	'label_id_number' => new Tag(
		'853',
		'label_id_number',
		'Labelling Of The ID-Number',
		'Ref no:',
		'C',
		true,
		25
	),
	'customer_reference' => new Tag(
		'854',
		'customer_reference',
		'Customer Reference',
		'12345678AB',
		'C',
		true,
		10
	),
	'receiver_address_type' => new Tag(
		'859',
		'receiver_address_type',
		'Receiver Email Address Type (P/B)',
		'P',
		'C',
		true,
		1
	),
	'receiver_name_1' => new Tag(
		'860',
		'receiver_name_1',
		'Receiver Name1',
		'Parcelforce International',
		'C',
		true,
		30
	),
	'receiver_name_2' => new Tag(
		'861',
		'receiver_name_2',
		'Receiver Name2',
		'Mr.PeterMay',
		'C',
		false,
		30
	),
	'receiver_name_3' => new Tag(
		'862',
		'receiver_name_3',
		'Receiver Name3',
		'1st Floor',
		'C',
		false,
		30
	),
	'receiver_street' => new Tag(
		'863',
		'receiver_street',
		'Receiver Street',
		'Middlemarch Business Parc 14',
		'C',
		true,
		30
	),
	'receiver_place' => new Tag(
		'864',
		'receiver_place',
		'Receiver Place',
		'COVENTRY',
		'C',
		true,
		30
	),
	'additional_text_1' => new Tag(
		'922',
		'additional_text_1',
		'Additional Text 1',
		'Infotext',
		'C',
		false,
		50
	),
	'additional_text_2' => new Tag(
		'922',
		'additional_text_2',
		'Additional Text 2',
		'Infotext',
		'C',
		false,
		50
	),
	'message_subject' => new Tag(
		'1044',
		'message_subject',
		'Message subject',
		'Uw bestelling',
		'C',
		false,
		50
	),
	'inbound_location_code' => new Tag(
		'8700',
		'inbound_location_code',
		'Location Code Of The Inbound GLS Depot',
		'MultiLocation(NL3500)',
		'C',
		true,
		6
	),
	'consolidation_reference' => new Tag(
		'8701',
		'consolidation_reference',
		'Consolidation reference',
		'11001234',
		'C',
		false,
		20
	),
	'parcel_sequence' => new Tag(
		'8904',
		'parcel_sequence',
		'Parcel Sequence',
		'001',
		'N',
		false,
		3
	),
	'parcel_quantity' => new Tag(
		'8905',
		'parcel_quantity',
		'Parcel Quantity',
		'002',
		'N',
		false,
		3
	),
	'contact_id' => new Tag(
		'8914',
		'contact_id',
		'ContactID. Fixed value “5280000000” for the time being.',
		'5280000000',
		'C',
		true,
		10
	),
	'sap_number' => new Tag(
		'8915',
		'sap_number',
		'SAP number',
		'5281234567',
		'C',
		true,
		10
	),
	//return tags
	'outbound_sorting_flag' => new Tag(
		'110',
		'outbound_sorting_flag',
		'Outbound sorting flag',
		'CTC'
	),
	'inbound_sorting_flag' => new Tag(
		'310',
		'inbound_sorting_flag',
		'Inbound sorting flag',
		'0'
	),
	'final_location' => new Tag(
		'101',
		'final_location',
		'Final location',
		'7301'
	),
	'tour_number' => new Tag(
		'320',
		'tour_number',
		'Tour number',
		'1410'
	),
	'zip_code_label' => new Tag(
		'8951',
		'zip_code_label',
		'Zip code (text)',
		'Zip code'
	),
	'track_id_label' => new Tag(
		'8952',
		'track_id_label',
		'Your GLS Track ID (text)',
		'Your GLS Track ID'
	),
	'ref_no_label' => new Tag(
		'8953',
		'ref_no_label',
		'Refenrence no. (text)',
		'Refenrence no.'
	),
	'customer_id_label' => new Tag(
		'8957',
		'customer_id_label',
		'Customer ID (text)',
		'Customer ID'
	),
	'contact_label' => new Tag(
		'8958',
		'contact_label',
		'Contact (text)',
		'Contact'
	),
	'phone_label' => new Tag(
		'8959',
		'phone_label',
		'Phone (text)',
		'Phone'
	),
	'note_label' => new Tag(
		'8960',
		'note_label',
		'Note (text)',
		'Note'
	),
	'contact_id_label' => new Tag(
		'8965',
		'contact_id_label',
		'Contact ID (text)',
		'Contact ID'
	),
	'track_id' => new Tag(
		'8913',
		'track_id',
		'Track ID',
		'ZVF9NYFH'
	),
	'handling_information_2' => new Tag(
		'202',
		'handling_information_2',
		'Handling information 2',
		'S'
	),
	'handling_information_5' => new Tag(
		'205',
		'handling_information_5',
		'Handling information 5',
		'S'
	),
	'primary_code' => new Tag(
		'8902',
		'primary_code',
		'Primary Code (black borders must be added)',
		'A|Name|'
	),
	'secondary_code' => new Tag(
		'8903',
		'secondary_code',
		'Secondary Code',
		'A|Name|'
	),
	'pickup_location' => new Tag(
		'500',
		'pickup_location',
		'Pickup location',
		'NL0100'
	),
	'station_id' => new Tag(
		'510',
		'station_id',
		'Station ID',
		'cd'
	),
	'date' => new Tag(
		'540',
		'date',
		'Date',
		'03.06.2010'
	),
	'time' => new Tag(
		'541',
		'time',
		'Time',
		'14:45'
	),
	'routing_date' => new Tag(
		'520',
		'routing_date',
		'Routing date',
		'01062010'
	)
];