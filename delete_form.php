<?php
session_start();
extract($_REQUEST);
include_once("classes/commonfunctions.php");
include_once("form_generator.class.php");

if(isset($_REQUEST['TableID']) && $_REQUEST['TableID']!='')
{
	$db->query("select * from forms where TableID='".$_REQUEST['TableID']."'");
	if($db->num_rows()>0)
	{
		$db->next_Record();
		$DirectoryName = $db->f('TableName');
		delete_directory("forms/$DirectoryName");
		$db->query("delete from forms where TableID='".$_REQUEST['TableID']."'");
		$db->query("drop table ".$DirectoryName);
	}
	showmessage("Form all related items deleted successfully");
}
redirect("index.php", 0);
?>