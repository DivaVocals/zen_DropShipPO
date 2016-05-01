INSERT INTO subcontractors_shipping ( alias, name, street1, city,
state, zip, email_address, telephone, contact_person) 
SELECT short_name, full_name, street1, city,
state, zip, email_address, telephone, contact_person
FROM subcontractors; 

DROP TABLE subcontractors; 
