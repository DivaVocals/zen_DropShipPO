*****************************
*** PURCHASE ORDERS V3.20 ***
*****************************
* Version 3.20 updated by   *
*               Numinix.com *
*                           *
* Version 2.0, 3.0-3.12 by  *
*              Scott Turner *
*          birdingdepot.com *
*                           *
* Commisioned by            *
*                Jack Mikol *
*         www.bumpernut.com *
*                           *
* Designed by               *
*         Maciej Szleminski *
*      www.redlinegoods.com *
*                           *
* With a lot of help from   *
*                  Prattski *
* (ZenCart forums username) *
*****************************
DISCLAIMER
Provided AS-IS. No warranty either expressed or implied. Use at your own risk. :) Check the Zen Cart forum for help with version 3.0.  A previous author added: When in doubt, email mszleminski@gazeta.pl with your questions. They will be answered on first-come first served basis depending on my free time.

If you feel like expressing some gratitude for our efforts, please take a kind look at our stores to see what we offer. And buy some, if you find the products interesting. :)  I'd also really appreciate a link on your web site.

To upgrade to version 3, SQL changes are necessary.  See the installation instructions below.

I like to give a special thanks to everyone who worked on the following.  Without them this contribution would not be possible:
Everyone who has created or worked on this before me!
FPDF
Xavier Nicolay - Created an invoice script for FPDF that the packing lists in this contribution are based on.
Anyone who worked on the Edit Orders Mod including - Kathleen, Igor Couto, Scot http://www.ecdiscounts.com, Josh Beauregard http://www.sanguisdevelopment.com, Numinix Technology http://www.numinix.com - Code was adapted from this mod for send_pos_nc.php for adding products.

*****************************
V3.20 CHANGELOG
****************************
1.) Modified admin/send_pos.php to remove manufactures lookup function
2.) Modified admin/send_pos_nc.php to remove manufactures lookup function
3.) Modified dbscript.sql to Register Admin Pages
4.) Added Purchase order number to bottom of comments of PDF Packing List


*****************************
V3.12 CHANGELOG
*****************************
1) HTML emails fixed.
2) Added {customers_comments} field for email header.
3) Added a list of fields to readme file.

*****************************
V3.11 CHANGELOG
*****************************
This is a bug fix release.  If you are using 3.0 or 3.1, I highly recommend you upgrade.
1) Fixed problem with incorrect attributes on send PO for unknown customer page.

Please read the instructions for upgrading carefully!

*****************************
V3.1 CHANGELOG
*****************************
This is a bug fix release.  If you are using 3.0, I highly recommend you upgrade.
1) Fixed problems with adding products on send PO for unknown customer page.
2) Fixed heading problems that caused issues with other headings in Zen Cart.
3) Renamed send PO for unknown customer page to be more consistent with mod.
4) Consolidated files to be more consistent.

Please read the instructions for upgrading carefully!

*****************************
V3.0 CHANGELOG
*****************************
1) Created a way to send purchase orders to customers that are not in your database.  This is great for phone orders, ebay orders, etc...
2) Added the ability to review a purchase order before sending it.  This allows you to make changes that are specific to one subcontractor...or just simply change stuff on a P.O. any time before sending it.
3) Added several options to the database to give this mod more flexibility and decrease the effort needed to make this mod do what you want.  Here are some notable additions to the database:
- Change a shipping option automatically.  For example, you can make a shipping option read "Cheapest" on the P.O.
- Omit things from the P.O. when sent to a customer who is not on Zen Cart.
- Edit messages to customers when PO is sent and when packages ship.
- Edit messages that are placed on packing lists.
- Added the ability to choose whether or not packing lists are sent.
- Choose the packing list filename.
4) Got rid of database entry for Own Stock -- This was redundant.  Just use the "Own Stock" subcontractor entry!
5) Moved English language stuff in admin directory to the appropriate language files.  This still needs to be done for confirm_track_sub.php.  Maybe someday!
6) Automatically retrieves store information for packing list, which makes installation easier -- one less thing to modify.
7) Added the ability to change the sort order of the confirm tracking page.
8) Changed layout of send PO page.  Much more info is included for each order and this info is much easier to use and understand.
9) Added the ability to put shipping option in the email header section as well as the email footer section.
10) Added fpdf files -- It turns out there is no license on fpdf, which means it can be included.  This makes installation easier!

This version is much easier to install and has many more options that are easily configurable.  Enjoy!

*****************************
V2.0 CHANGELOG
*****************************
1) Implemented PDF packing lists that automatically attach to the email purchase orders. These have customer comments, and only include the products that ship with each purchase order. If the purchase order is a partial shipment, this is indicated on the PDF packing list.
2) Reworked confirm_track.php.  This was badly broken.  I fixed a lot of bugs and cut a lot of code. I also added who the purchase order was sent to. It seems to work fine now.
3) Added a ton of extra information to the send PO page.
4) Emails to customers now include all products shipped regardless of whether or not the shipment was a partial shipment or not. The old one only listed the products if it was a partial shipment
5) Simplified the PO # and Order # system. Now, PO #'s are what used to be Order #-PO #. So for example, if the PO # is 5 and the order # is 10 the new PO# would be 10-5. This way, there is only one number that your subcontractor needs to know, but you have all the information you might need in this number.
6) Changed the tracking input page so that all products are checked by default. The most common scenario is that all products shipped at once, so this is the easiest way to handle this. If not, you can still easily uncheck products.
7) Fixed many problems with the PO resend page.  Basically, I just reused the code for PO sending with a few modifications.  It seems to have fixed all the bugs.
8) Got rid of duplicate Own stock entry in drop-down menus.
9) Added search capability for Order Numbers in PO Resend page.
10) Fixed problems with incorrect shipping when sending multiple POs at once.
11) Fixed problems with 2nd line of address and company not showing up in addresses.
12) Added the ability to add comments to packing lists and POs.
13) Changed the default behavior so that orders are not listed on the Send PO or Resend PO page if they have been marked "Delivered" in Zen Cart.
14) Added the ability to reinsert "Delivered" products on the Resend PO pages with a checkbox.
15) Added the ability to remove customer comments from packing lists.
16) Changed the default behavior so that when an order is sent to subcontractor, the order is changed from "Pending" to "Processing."  The customer IS NOT notified when this happens, but they can see this in their account if they log on.  After the first part of the order is sent to a subcontractor, it will no longer do this for future POs.
17) Changed the wording and date format to reflect the changes made and the U.S. date system of MM-DD-YYYY.  The exception was on the Resend PO page, where for logistical reasons, changing the date format would have been more difficult to do.
18) Added a warning about refreshing the page.
19) Changed default number of POs to show on one page to 100.
20) If you split an order in two between two suppliers/drop shippers only the first tracking information was sent to the customer.  This has been fixed.
21) Change mailing addresses to reflect the format used in the United States.
22) Added a way for you to convert one type of shipping to "Cheapest" on the PO.
23) I've probably forgot something!  But, this seems like everything I can remember doing.   :)

*****************************
V1.3B CHANGELOG
*****************************
1) Incorporated updates and fixes made by Prattski to make this ZenCart v1.3.x compliant. TESTED ON 1.3.8.
2) General code maintenance, moved all necessary information to configuration variables
3) Updated list of available mail template tagnames both in subject and in contents of sent messages
4) Added debug mode for sending POs (admin/includes/extra_configures/purchaseorders DEBUGMODE set to Yes if needed)



*****************************
INSTALLATION & CONFIGURATION
*****************************
UPGRADES TO VERSION 3.12 FROM VERSION 1 OR 2 SERIES -- Copy the entire directory structure overwriting all files when necessary.  Install the SQL script in upgradeto3.sql

UPGRADES TO VERSION 3.12 FROM VERSION 3 SERIES -- Copy the entire directory structure overwriting all files when necessary.  Delete this file if it exists: admin/includes/boxes/extra_boxes/send_pos_nc_customers_dhtml.php.  No SQL changes are necessary.

1) Install Ty_Package_Tracker and configure it properly before you continue. The definition of carriers is the most important one, as our module will plug into it for consistency. If you are not interested in the tracking functionalities of this module, skip this step.  PLEASE NOTE!  I have never tested version 2.0 or 3.0 of this modification without Ty_Package_Tracker!!  Install at your own risk if you don't use Ty_Package_Tracker!!

2) Run dbscript.sql on your database. Via Tools->Install SQL patches (need to copy the entire file into clipboard and paste it into the screen).  (Upgrades from ANY previous version should install upgradeto30.sql instead.)

3) If you are not interested in the tracking functionalities, you need to remove links to that functionality so that your admin menu doesn't show them. In the contribution files, find /admin/includes/boxes/extra_boxes/send_po_customers_dhtml and add two slashes (//) in front of this line:

$za_contents[] = array('text' => BOX_CUSTOMERS_CONFIRM_TRACKING, 'link' => zen_href_link(FILENAME_CONFIRM_TRACKING, '', 'NONSSL'));

4) Copy the entire directory structure (email to email and admin to whatever is the name of your admin directory) to your store. If you have relocated admin to another directory, make sure to check confirm_track_sub.php and edit it accordingly (line numbers are given on the beginning of the file)

5) Customize the email templates if you want. There are three text files in /admin/email starting with email_. These contain the templates for the PO description (header and footer) and the PO fields (the product data rows). The tags enclosed in {} brackets are replaced with actual values when POs are being sent. I`m sure the rest is self-explanatory. :) For a complete list of tags you can use, see the next section of this file.

5-1) Also please note that the ONLY valid tags for the subject of the PO are: po_number, contact_person, full_name, short_name, and order_number.

If you are removing tracking functionality, make sure to remove the {tracking_link} tag so that the emails do not contain a link for your subcontractors to enter tracking information.

6) Configure some variables in Admin->Configuration->Purchase Orders. 7) Test. I would suggest adding a fake supplier with your email address in the respective field, submitting a test order to your store, setting yourself as the default PO and sending out a PO to yourself, then entering the tracking either via admin or, better yet, through the link provided in the email.

8) Backup. If you have customized your email templates in any way, make sure to copy them to admin/email - updated distributions of this contribution will come with standard email templates and you probably wouldn't want your customizations to get overwritten.

ENJOY!

*****************************
TAGS FOR EMAIL FILES
*****************************
email_header.txt
----------------
{customers_name} - Name of customer
{order_number} - Order Number
{customers_adres} - Customer's Address
{customers_phone} - Customer's Phone Number
{customers_email} - Customer's Email Address
{delivery_name} - Name of Person for Shipping
{po_comments} - Comments you enter on Send PO page for the PO
{customers_comments} - The customer's order comments
{delivery_company} - Name of Company for Shipping
{delivery_adress} - Shipping Address
{billing_company} - Name of Company for Billing
{billing_name} - Name of Person for Billing
{billing_address} - Billing Address
{payment_method} - Payment Method
{date_purchased} - Date Purchased
{po_number} - Purchase Order Number

email_products.txt
------------------
{manufacturers_name} - Manufacturer of Product
{products_name} - Name of Product
{products_model} - Model of Product
{final_price} - Price of Product
{products_quantity} - Quantity of Product
{products_attributes} - Product Attributes
{po_number} - Purchase Order Number

email_footer.txt
----------------
{tracking_link} - Link for Subcontractor to Enter Tracking Number

any of the three email files
----------------------------
{shipping_method} - Shipping Method
{contact_person} - Subcontractor Contact
{full_name} - Full Name of Subcontractor
{short_name} - Short Name of Subcontractor
{subcontractors_id} - Subcontractor's ID Number
{street} - Subcontractor's Street Address
{city} - Subcontractor's City
{state} - Subcontractor's State
{zip} - Subcontractor's Zip Code
{telephone} - Subcontractor's Telephone Number
{email_address} - Subcontractor's Email Address

email title - change in admin-configuration-purchase orders
-----------------------------------------------------------
{po_number} - Purchase Order Number
{contact_person} - Subcontractor Contact
{full_name} - Full Name of Subcontractor
{short_name} - Short Name of Subcontractor
{order_number} - Order Number



*****************************
USER'S MANUAL (CRUDE FOR NOW)
*****************************
All of the options are available in the admin, of course.

EXTRAS/EDIT SUBCONTRACTORS
1) You can enter a list of subcontractors/dropshippers (SCs from now on) you work with (all their contact info, emails, phone, etc.)

EXTRAS/SET SUBCONTRACTORS
2) You can set a default SC for each product

CUSTOMERS/SEND POS
3) Using admin you can send POs to SCs

3.1) For each order in Processing you will see a list of all ordered products along with customer info in a simple table. For any 1+ -> all of such order items, you can send POs to their default SCs, or select another SC from a drop-down list (separate for each ordered item and customer). One of the SCs is called "Own stock" which means you commit to ship the item from your stock. Useful if you happen to do that. :)  Don't ever delete Own stock!


3.2) For each product item selected, they will be grouped by customer into respective POs and emails will be sent to those SCs. Each PO would be dated and numbered. All the info for drop-shipping is in the email: what was ordered and where does it have to be shipped to.

3.3) If you send a P.O. for several items from one order they will all be grouped into the same PO.

3.4) You can add comments to the PO and the Packing List.

3.5) You can preview and edit emails, but ONLY when sending one P.O. at a time.  You can have several products on the P.O., but they must all be from one order and for one subcontractor (i.e. the conditions that create a new P.O.!)

CUSTOMERS/RESEND POS

4) You will be able to re-send POs. You`d be able to search past POs by number, orders by number, date range and recipient.  If your supplier can't ship an item for some reason, you can send this to another supplier.  If there is nothing left on the previous PO, it will be deleted.  Otherwise, it will just modify the old PO by removing the old items.  ONLY THE NEW PO WILL BE SENT, but I assume if your old subcontractor is out of something that you will be communicating with them anyway!

CUSTOMERS/SEND POS FOR UNKNOWN CUSTOMER

5) This will allow you to easily send a P.O. for phone orders, ebay orders, etc...  Just fill in the customer's information, add products, and send the P.O. There is no option to review the entire email, but all customizable options can be easily edited on the screen before sending the orders, so this probably won't be necessary.

5.1) Because many options don't make sense or aren't available for unknown customers, you can omit three things from unknown customer's POs.  This can be done from the Configuration/Purchase Orders menu.

5.2) These POs are not tracked.  No emails are sent to customers.  This option is simply here to make it easy to send a PO for unknown customers with a packing list to your subcontractor.

5.3) There is a known bug -- any changes will be lost if you make them while adding a product.  So, just leave the other fields alone until you are done adding a product.  

IF SUPPLIER ENTERS TRACKING INFORMATION

6) For items sent to your own stock, you`d be able to enter tracking numbers. THIS WOULD WORK THANKS TO THE TY_PACKAGE_TRACKER Contribution. YOU NEED TO HAVE IT INSTALLED IN ORDER FOR TRACKING TO WORK!

6.1) Each PO email will have a link to a simple form that would allow the recipient to choose which items were shipped and enter tracking info (carrier and number) for each (or all if he did it right and bundled them in a package). Thus you, the store owner, know all tracking info for each package that was shipped for that order.

6.2) The comments for the order would be updated with tracking info (with a link to the carrier's tracking page already prefilled with the tracking number) and a designation which item(s) were shipped in each package.

IF YOU ENTER TRACKING INFORMATION
CUSTOMERS/POs Enter Tracking #
7) Many suppliers are unwilling to enter tracking information directly into your system, but they will send you automatic emails with this information.  You can enter this information directly under Customers-POs Enter Tracking #.

*****************************
Example:

- John ordered 5 items. 2 of them from SC#1, 2 of them from SC#2, the last one normally sent from your stock
- Mary ordered 3 items. 1 from SC#1, 1 from SC#2, 1 from your stock

Say you have a returned item identical to one of John`s normally sent to SC#1 on your own stock and you want to move it.

Hit the Send POs screen. You`ll see 8 rows in the table. One for each ordered product. Each product would have a check box (send PO yes/no) and a drop-down menu (choosing the recipient of the PO, already prefilled upon default product SC).

You`d flip all the Send PO checkboxes to yes. You would change the John`s product you happen to have from SC#1 to `Own stock`. You`d hit SEND POs.

SC#1 will get 2 emails. One for each order and with one product on each one.
SC#2 will get 2 emails. One for each order, with two products on John's order and one product on Mary's order.
You will get 2 emails. One for each order, first with 2 products (John) and the other with 1 product (Mary).

Say SC#2 cannot process Mary`s order, but SC#1 can (although he wouldn`t be the default recipient). Look up that old PO by number, change the recipient to SC#1 and hit SEND. It`s renumbered and re-dated and emailed to SC#1. 
*****************************


