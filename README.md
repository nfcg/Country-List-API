# Description
A API to retrieve countries list.


## Country fields available:
- Name
- Capital
- Calling Code
- Currency Name and Code
- Continents Name and Code
- Top-Level Domain Code (ccTLD)
- ISO-3166-1 Alpha-2, Alpha-3 and Numeric Codes
- Flags


# Installation

Clone the contents of the repository into your server directory.

- Apache server setings.
```
<Directory /Your-API-Folder>
   Header set Access-Control-Allow-Origin "*"
</Directory>
```

- Nginx server setings.
```
location /Your-API-Folder) {		
   add_header 'Access-Control-Allow-Origin' '*';
}
```

# Configuration

Edit the countries.php file.
- The database location
- IP Access: allow, deny or disabled (ips list in database)


# Options

- continent: AF, AN, AS, EU, NA, OC, SA (default all Continents)
- flag: 16, 24, 32, 48, 64, 128 (default size 16x16)
- language: pt (default English)
- iso: alpha_2, alpha_3, numeric (default alpha_2)

All fields can be excluded using "hide" (eg. calling_code=hide continent_code=hide)

# Examples

 Examples using DataTables and Bootstrap-Select in the examples.html file.
-  https://nunofcguerreiro.com/examples.html
-  https://nunofcguerreiro.com/countries.php?language=pt
-  https://nunofcguerreiro.com/countries.php?continent=eu
-  https://nunofcguerreiro.com/countries.php?currency_code=hide0&cc_tld=hide&capital_en=hide&continent_name_en=hide&continent_code=hide&currency_name_en=hide&continent
