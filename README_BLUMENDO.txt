# # # # # # # # # # # # # # # # # # # # 

LAST EDIT: 12.11.2015
Sascha Dobschal
mail@saschadobschal.de

# # # # # # # # # # # # # # # # # # # # 

WEBSERVICE BUILT WITH JOOMLA 3.*
	
MODULES

* mod_paypal
	payment process
* mod_paysuccess
	javascriptInterface to Android
	adding success to database
* mod_orderview
	admin page to show orders in list

# # # # # # # # # # # # # # # # # # # # 

PARAMETERS

via POST request
firstName 	VARCHAR
lastName 	VARCHAR
postCode 	INT
street		VARCHAR
houseNumber	INT
city		VARCHAR
amount		INT
productId	INT
email		VARCHAR

# # # # # # # # # # # # # # # # # # # # 

ADDRESS FOR WEBVIEW

http:// [URL] /index.php/payment