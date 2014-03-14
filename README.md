Wholegoods Database for Machinery Equipment
===========================================

A Wholegoods Database portal that allows members of a physically-diverse sales team to actively add, edit and view wholegoods stock across all branches.

Built for a company I used to work for back in 2010. Coded from scratch (hence its messiness)

Requirements:
-------------
Apache Server (v2+)
PHP v5.0+
SQL-based database (MySQL used primarily, database class allows for easy transition)
Windows-based machine (for PDF generation, using pdftk found here: http://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/)

Flow:
-----
**Add New Trade**
User --> index.php --> sees all trades, can limit by type, show only brand new, or show/hide sold equipment
--> Add new trade(*) --> trade_form.php --> add trade information as relevant --> Save/Create PDF
--> processpdf.php --> creates PDF via fdf and pdf-form, using pdftk.exe (located in webroot) -->
saves data in database --> servepdf.php --> view/save pdf --> Stream PDF to user --> Return to main page --> delete pdf and
temporary data --> index.php

(*) - Edit Existing Trade works same after Add new trade.

**Search**
user --> index.php --> enter search term --> search.php --> shows all results that include search term. Search instructions
are located at the top of this screen. --> index.php
