SELECT @poid:=configuration_group_id
FROM configuration_group
WHERE configuration_group_title= 'Purchase Orders';
DELETE FROM configuration WHERE configuration_group_id = @poid AND configuration_group_id != 0; 
DELETE FROM configuration_group WHERE configuration_group_id = @poid AND configuration_group_id != 0; 

SELECT @poid:=configuration_group_id
FROM configuration_group
WHERE configuration_group_title= 'DropShip Purchase Orders';
DELETE FROM configuration WHERE configuration_group_id = @poid AND configuration_group_id != 0; 
DELETE FROM configuration_group WHERE configuration_group_id = @poid AND configuration_group_id != 0; 

INSERT INTO configuration_group VALUES ('', 'DropShip Purchase Orders', 'DropShip Purchase Orders Settings', '1', '1');
UPDATE configuration_group SET sort_order = last_insert_id() WHERE configuration_group_id = last_insert_id();

SELECT @poid:=configuration_group_id 
FROM configuration_group
WHERE configuration_group_title= 'DropShip Purchase Orders';

INSERT INTO configuration (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function) VALUES 	
	(NULL,  'PO - send packing lists', 'PO_SEND_PACKING_LISTS', '1', '0 - never, 1 - always, 2 - sometimes (default yes), 3 - sometimes (default no)', @poid, 101, now(), NULL, NULL),
	(NULL,  'PO - notify customer', 'PO_NOTIFY', '1', '0 - no customer notification of PO updates, 1 - notify customer', @poid, 102, now(), NULL, NULL),
	(NULL,  'PO - subject', 'PO_SUBJECT', '{contact_person}: New order (#{po_number}) for {full_name}', 'Subject of PO emails, {po_number} will be replaced with the actual number', @poid, 103, now(), NULL, NULL),
	(NULL,  'PO - from email name', 'PO_FROM_EMAIL_NAME', 'PurchaseOrderManager', 'The FROM email NAME for sent Purchase Orders', @poid, 104, now(), NULL, NULL),
	(NULL,  'PO - from email address', 'PO_FROM_EMAIL_ADDRESS', 'po_email@here.com', 'The FROM email ADDRESS for sent Purchase Orders', @poid, 105, now(), NULL, NULL),
	(NULL,  'PO - sent comments', 'PO_SENT_COMMENTS', 'Order Submitted to Shipping Department for Fulfillment', 'Comments added to the account when submitted to subcontractor', @poid, 106, now(), NULL, NULL),
	(NULL,  'PO - full ship comments', 'PO_FULLSHIP_COMMENTS', 'Thanks for your order!', 'Comments added to the account when the order has shipped in full', @poid, 107, now(), NULL, NULL),
	(NULL,  'PO - partial ship comments', 'PO_PARTIALSHIP_COMMENTS', 'Part of your order has shipped!  The rest of your order will ship soon. You will be notified by email when your order is complete.', 'Comments added to the account when part of the order has shipped', @poid, 108, now(), NULL, NULL),
	(NULL,  'PO - full ship packinglist', 'PO_FULLSHIP_PACKINGLIST', 'Thanks for your order!', 'Comments added to the packing list when the order has shipped in full', @poid, 109, now(), NULL, NULL),
	(NULL,  'PO - partial ship packinglist', 'PO_PARTIALSHIP_PACKINGLIST', 'This is a partial shipment.  The rest of your order has shipped or will ship separately.', 'Comments added to the packing list when part of the order has shipped', @poid, 110, now(), NULL, NULL),
	(NULL,  'PO - packinglist filename', 'PO_PACKINGLIST_FILENAME', 'packinglist.pdf', 'packing list filename', @poid, 111, now(), NULL, NULL),
	(NULL,  'PO - omit from unknown email 1', 'PO_UNKNOWN_OMIT1', '\nIf you would prefer to enter tracking information for this order\ndirectly, please visit:\n', 'Text to omit from emails sent for unknown customers 1 of 3', @poid, 112, now(), NULL, NULL),
	(NULL,  'PO - omit from unknown email 2', 'PO_UNKNOWN_OMIT2', '{delivery_name}\n', 'Text to omit from emails sent for unknown customers 2 of 3', @poid, 113, now(), NULL, NULL),
	(NULL,  'PO - omit from unknown email 3', 'PO_UNKNOWN_OMIT3', '', 'Text to omit from emails sent for unknown customers 3 of 3', @poid, 114, now(), NULL, NULL),
	(NULL,  'PO - change shipping from', 'PO_CHANGE_SHIPPING_FROM', '', 'Change this shipping option to something else on POs and Packing Lists', @poid, 115, now(), NULL, NULL),
	(NULL,  'PO - change shipping to', 'PO_CHANGE_SHIPPING_TO', 'Cheapest', 'Value to change shipping option to on POs and Packing Lists', @poid, 116, now(), NULL, NULL),
	(NULL,  'DropShip Purchase Orders Version', 'DSPO_TVERSION ', '3.21', 'Version number (DO NOT MODIFY THIS VALUE)', @poid, 0,  now(), NULL, 'zen_cfg_select_option(array(''3.21''),');

DELETE FROM admin_pages WHERE page_key = 'dropshipsendpos' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'dropshipsendposnc' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'dropshipconftrack' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'dropshipeditsubs' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'dropshipsetsubs' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'configDropShip' LIMIT 1;

INSERT IGNORE INTO admin_pages (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order) VALUES
	('dropshipsendpos','BOX_CUSTOMERS_SEND_POS','FILENAME_SEND_POS','', 'customers', 'Y', @poid),
	('dropshipsendposnc','BOX_CUSTOMERS_SEND_POS_NC','FILENAME_SEND_POS_NC','', 'customers', 'Y', @poid),
	('dropshipconftrack','BOX_CUSTOMERS_CONFIRM_TRACKING','FILENAME_CONFIRM_TRACKING','', 'customers', 'Y', @poid),
	('dropshipconftracksub','BOX_CUSTOMERS_CONFIRM_TRACKING_SUB','FILENAME_CONFIRM_TRACKING_SUB','', 'customers', 'N', @poid),
	('dropshipeditsubs','BOX_TOOLS_EDIT_SUBCONTRACTORS','FILENAME_SUBCONTRACTORS','', 'tools', 'Y', @poid),
	('dropshipsetsubs','BOX_TOOLS_SET_SUBCONTRACTORS','FILENAME_SET_SUBCONTRACTORS','', 'tools', 'Y', @poid),
	('configDropShip','BOX_CONFIGURATION_DROPSHIP','FILENAME_CONFIGURATION',CONCAT('gID=',@poid),'configuration','Y',@poid);


CREATE TABLE IF NOT EXISTS `subcontractors_to_admins` (
  `s2cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subcontractors_id` int(10) unsigned NOT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`s2cid`),
  KEY s2c_sub (subcontractors_id)
);
