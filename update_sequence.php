<?php

session_start();
extract($_REQUEST);
include_once("classes/commonfunctions.php");
include_once("form_generator.class.php");

if(isset($_REQUEST['ValidateFormFieldsFormFlag']) && $_REQUEST['ValidateFormFieldsFormFlag']=='true')
{
	$FormGeneratorClass = new FormGenerator();
	
	$Count=0;
	foreach($_REQUEST['FormElementID'] as $ElementID)
	{
		$Sequence = $_REQUEST['Sequence'][$Count];
		$db->query("update form_elements set Sequence=$Sequence where TableID=$ElementID");
		$Count++;
	}
	$FormDetails = FetchRecordByID($_REQUEST['FormID'], "TableID", "forms");
	$DirectoryName = $FormDetails['TableName'];
	//delete_directory("forms/$DirectoryName");
	
	$FormGeneratorClass->setTableName($DirectoryName);
		
	$FormGeneratorClass->setFormID($FormID);
	
	$FormGeneratorClass->setFormName($FormDetails["FormName"]);
	
	$FormInfo = $FormGeneratorClass->SetValuesSession();
	
	$FormInfo["SaveInDB"]=1;
	
	$FormGeneratorClass->UpdateForm($FormInfo);
	
	showmessage("Form field added successfully");
	
	redirect("view_fields.php?TableID=".$FormID, 0);
}