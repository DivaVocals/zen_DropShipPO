DROP TABLE IF EXISTS `subcontractors`;
DROP TABLE IF EXISTS `subcontractors_to_customers`;
DROP TABLE IF EXISTS `subcontractors_shipping`;

ALTER TABLE `orders_products`
DROP `po_sent`,
DROP `po_number`,
DROP `po_sent_to_subcontractor`,
DROP `po_date`,
DROP `item_shipped`;

ALTER TABLE `products`
DROP `default_subcontractor`;

DELETE FROM admin_pages WHERE page_key = 'dropshipsendpos' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'dropshipsendposnc' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'dropshipconftrack' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'dropshipeditsubs' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'dropshipsetsubs' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'configDropShip' LIMIT 1;

SELECT @poid:=configuration_group_id
FROM configuration_group
WHERE configuration_group_title= 'DropShip Purchase Orders';
DELETE FROM configuration WHERE configuration_group_id = @poid AND configuration_group_id != 0; 
DELETE FROM configuration_group WHERE configuration_group_id = @poid AND configuration_group_id != 0; 
