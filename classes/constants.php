<?php
error_reporting(0);

define("DATABASE_HOST","localhost");
define("DATABASE_NAME","formbuilder");
define("DATABASE_USER","root");
define("DATABASE_PASSWORD","");
define("DOMAINNAME","http://localhost/formbuilder/");
/*
define("DATABASE_HOST","localhost");
define("DATABASE_NAME","rsidbs_formbuilder");
define("DATABASE_USER","rsidbs_formbuild");
define("DATABASE_PASSWORD","formbuilder123$");
define("DOMAINNAME","http://www.rsidemos.com/development/formbuilder/");
*/
$InputTypes[]='Text Field';
$InputTypes[]='Text Area';
$InputTypes[]='Password';
$InputTypes[]='Radio Button';
$InputTypes[]='Check Box';
$InputTypes[]='Select Menu';
$InputTypes[]='File';
$InputTypes[]='Date Calendar';

$YesNo[]='No';
$YesNo[]='Yes';

$OptionsIDs = array(3, 4, 5);

define("SELECT_ALERT", "Please select ");
define("ENTER_ALERT", "Please enter ");

define("BR", "");

$FilesFormat[] = 'jpg';
$FilesFormat[] = 'gif';
$FilesFormat[] = 'png';
$FilesFormat[] = 'doc';
$FilesFormat[] = 'docx';
$FilesFormat[] = 'xls';
$FilesFormat[] = 'xlsx';
$FilesFormat[] = 'pdf';
?>