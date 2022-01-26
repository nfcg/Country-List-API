# Description
A JSON API to retrieve countries list.


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


# Configuration

Edit paramteres in the countries.php file.
- The database location
- IP Access: allow, deny or disabled (ips list in database)


# Options

- continent: AF, AN, AS, EU, NA, OC, SA (default all Continents)
- flag: 16, 24, 32, 48, 64, 128 (default all Continents)
- language: pt (default English)
- iso: alpha_2, alpha_3, numeric (default alpha_2)
All fields can be excluded using "hide" (eg. calling_code=hide continent_code=hide)

# Examples

- Using DataTables and Bootstrap-Select examples in the examples.html file.
- `https://example.com/countries.php?language=pt`
- `https://example.com/countries.php?continent=eu`
- `https://example.com/countries.php?currency_code=hide0&cc_tld=hide&capital_en=hide&continent_name_en=hide&continent_code=hide&currency_name_en=hide&continent`
