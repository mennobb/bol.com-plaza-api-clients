bol.com-plaza-api-clients
=========================

A suite of client classes for the bol.com Plaza API in PHP and C#


Bol.com Plaza API Features
==========================
The bol.com Plaza API is one of the API's that bol.com has to offer. It allows 3rd party sellers with professional bol.com seller accounts to link their order administration to that of bol.com.

Currently the only features disclosed through the API are:
- Receiving new orders
- Marking orders as shipped or cancelled
- Getting payment information

For more information see the bol.com [developer portal](http://developers.bol.com/ "Developer Portal") (Only available in the Dutch language although the documentation is in English).

Target Audience
===============
Software developers of e-commerce solutions / administrative systems

What the code does
==================
The code in this project offers an abstract implementation of the bol.com Plaza API.

These classes take care of the communication with the Plaza API which means they:
- Communicate with the Plaza API over HTTPS
- Perform the signing of requests
- Compose the XML input that the Plaza API requires
- Parse the XML that the Plaza API produces
- Capture errors and allow the developer to handle those.

The developer knows how to code for his own systems and wishes to connect those to bol.com without having to re-invent the wheel.

Credits
=======
- PHP library was developed by [Menno Bieringa](http://www.appwards.nl/ "Appwards - Apps for web and mobile")
- C# library was developed by [Carolina Bauque](http://www.carobauque.com/)


Eat your vegetables...
