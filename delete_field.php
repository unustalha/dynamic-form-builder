<?php
session_start();
extract($_REQUEST);
include_once("classes/commonfunctions.php");
include_once("form_generator.class.php");

$FormGeneratorClass = new FormGenerator();

if(isset($_REQUEST['TableID']) && $_REQUEST['TableID']!='')
{
	$db->query("select * from form_elements where TableID='".$_REQUEST['TableID']."'");
	if($db->num_rows()>0)
	{
		$db->next_Record();
		$FieldName = $db->f('HTML_Name');
		$FormID = $db->f('FormID');
		
		$FormDetails = FetchRecordByID($FormID, "TableID", "forms");
		
		$DirectoryName = $FormDetails['TableName'];
		
		//delete_directory("forms/$DirectoryName");
		
		$db->query("delete from form_elements where TableID='".$_REQUEST['TableID']."'");
		
		$db->query("ALTER TABLE $DirectoryName DROP COLUMN $FieldName");
		
		$FormGeneratorClass->setTableName($DirectoryName);
		
		$FormGeneratorClass->setFormID($FormID);
		
		$FormInfo = $FormGeneratorClass->SetValuesSession();
		
		$FormInfo["SaveInDB"]=1;
		
		$FormGeneratorClass->UpdateForm($FormInfo);
	}
	showmessage("Form all related items deleted successfully");
}
redirect("index.php", 0);
?>