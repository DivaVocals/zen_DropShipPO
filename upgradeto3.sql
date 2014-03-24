SET @poid=9999;
SELECT (@poid:=configuration_group_id) as poid
FROM configuration_group
WHERE configuration_group_title= 'Purchase Orders';

DELETE FROM configuration WHERE configuration_key='PO_OWN_STOCK_EMAIL';

INSERT INTO configuration VALUES 	('','PO - send packing lists', 'PO_SEND_PACKING_LISTS', '1', '0 - never, 1 - always, 2 - sometimes (default yes), 3 - sometimes (default no)', @poid, 101, now(), now(), NULL, NULL),
					('','PO - sent comments', 'PO_SENT_COMMENTS', 'Order Submitted to Shipping Department for Fulfillment', 'Comments added to the account when submitted to subcontractor', @poid, 106, now(), now(), NULL, NULL),
					('','PO - full ship comments', 'PO_FULLSHIP_COMMENTS', 'Thanks for your order!', 'Comments added to the account when the order has shipped in full', @poid, 107, now(), now(), NULL, NULL),
					('','PO - partial ship comments', 'PO_PARTIALSHIP_COMMENTS', 'Part of your order has shipped!  The rest of your order will ship soon. You will be notified by email when your order is complete.', 'Comments added to the account when part of the order has shipped', @poid, 108, now(), now(), NULL, NULL),
					('','PO - full ship packinglist', 'PO_FULLSHIP_PACKINGLIST', 'Thanks for your order!', 'Comments added to the packing list when the order has shipped in full', @poid, 109, now(), now(), NULL, NULL),					('','PO - partial ship packinglist', 'PO_PARTIALSHIP_PACKINGLIST', 'This is a partial shipment.  The rest of your order has shipped or will ship separately.', 'Comments added to the packing list when part of the order has shipped', @poid, 110, now(), now(), NULL, NULL),
					('','PO - packinglist filename', 'PO_PACKINGLIST_FILENAME', 'packinglist.pdf', 'packing list filename', @poid, 111, now(), now(), NULL, NULL),
					('','PO - omit from unknown email 1', 'PO_UNKNOWN_OMIT1', '\nIf you would prefer to enter tracking information for this order\ndirectly, please visit:\n', 'Text to omit from emails sent for unknown customers 1 of 3', @poid, 112, now(), now(), NULL, NULL),
					('','PO - omit from unknown email 2', 'PO_UNKNOWN_OMIT2', '{delivery_name}\n', 'Text to omit from emails sent for unknown customers 2 of 3', @poid, 113, now(), now(), NULL, NULL),
					('','PO - omit from unknown email 3', 'PO_UNKNOWN_OMIT3', '', 'Text to omit from emails sent for unknown customers 3 of 3', @poid, 114, now(), now(), NULL, NULL),
					('','PO - change shipping from', 'PO_CHANGE_SHIPPING_FROM', '', 'Change this shipping option to something else on POs and Packing Lists', @poid, 115, now(), now(), NULL, NULL),					('','PO - change shipping to', 'PO_CHANGE_SHIPPING_TO', 'Cheapest', 'Value to change shipping option to on POs and Packing Lists', @poid, 116, now(), now(), NULL, NULL);
