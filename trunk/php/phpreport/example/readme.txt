Examples
--------

files
-----
	readme.txt	- this file
	test.sql	- test database content. Load this file into database named 'test'

1) hungarian.account
--------------------

This example shows how to use the pdf report generator. It generates an invoice document w/o replaced word entities. The 'kiszla.xml' 
defines the pdf and 'szlagen.php' generates the output. 'szlagen.php' generates 2 pages with different content. The example show 
how to be able to merge more report into one output pdf. It is useful in web application. Imagine you have to generate 200 invoice in
one step...

files
-----
	szlagen.php				- generator
	kiszla.xml				- report definition
	hungarian.account.pdf	- generated report

2) simplequery
--------------

Example shows how to get variables' values from database. The simple query example lists address table's content. The little code shows
how to use Database php object and how to implement ReportData interface. These classes are useful to generate report. Windows styled 
configuration files is used, which is readable and comfortable to set application initial data.

files
-----
	simplequery.php		- generator
	addresses.xml		- report definition
	class.MySQLRD.php	- ReportData interface implementation
	config.ini			- application variables (db connection definition, etc...)
	simplequery.pdf		- generated report


3) simplegroup
--------------

This group of example contains 4 report samples. All of them use the same implemented ReportData class and configuration file. The
examples show the group possiblities of the report generation class. The examples demonstate the capabilities from the simplest to the
more complicated.

files
-----
	class.MySQLRD.php	- ReportData interface implementation
	config.ini			- application variables (db connection definition, etc...)

3/1)

It counts, how many citizen there are in the address table. It uses the COUNT function and shows how to define group variable.

files
-----
	simplecount.php	- generator
	addresses.xml	- report definition
	simplecount.pdf	- generated report

3/2)

This example defines 2 groups. It demonstrates, how to define local groups inside the one and only one global one. We count how many citizen
lives the seperate cities. Report definition contains 2 embedded group definitions.

files
-----
	simple2count.php	- generator
	addresses1.xml		- report definition
	simple2count.pdf	- generated report

3/3)

The example groups the order items and summarize the prices of the orders, using 2 group definitions.

files
-----
	simplesum.php	- generator
	documents.xml	- report definition
	simplesum.pdf	- generated report

3/4)

This demonstrates the GROUP function. With this, you can define group function result, which can be use at the end of groups. See
the example, which summarize items.

files
-----
	groupfunction.php	- generator
	documents1.xml		- report definition
	groupfunction.pdf	- generated report


