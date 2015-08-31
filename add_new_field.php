<?php
session_start();
extract($_REQUEST);
include_once("classes/commonfunctions.php");
include_once("form_generator.class.php");
if(isset($_REQUEST['AddNewFieldFormFlag']) && $_REQUEST['AddNewFieldFormFlag']=='true')
{
	$FormGeneratorClass = new FormGenerator();
	
	$HTML_Name = str_replace(" ", "", $FieldName);
	$db->query("select * from form_elements where HTML_Name='$HTML_Name' and FormID='$FormID'");
	if($db->num_rows()>0)
	{
		showmessage("Field Name already exists. Try again");
		redirect("add_new_field.php?TableID=".$FormID, 0);
	}
	else
	{
		if(isset($_REQUEST["Required"]))
		{ $IsRequired=1; }
		else
		{ $IsRequired=0; }
		
		if(isset($_REQUEST["NumericOnly"]))
		{ $IsNumeric=1; }
		else
		{ $IsNumeric=0; }
		
		if(isset($_REQUEST["ValidateEmail"]))
		{ $IsEmail=1; }
		else
		{ $IsEmail=0; }
		
		if(isset($_REQUEST["ShowInListing"]))
		{ $InListing=1; }
		else
		{ $InListing=0; }
			
		$db->query("insert into form_elements set 
		FormID='$FormID', 
		label='$FieldName', 
		HTML_Name='$HTML_Name', 
		Type='$InputType', 
		IsRequired='$IsRequired', 
		IsNumeric='$IsNumeric', 
		IsEmail='$IsEmail', 
		ShowInListing='$InListing'");
		$ElementID = mysql_insert_id();
		if(in_array($InputType, $OptionsIDs))
		{
			$OptionValues = explode("\r\n", $Options);
			foreach($OptionValues as $OptionValue)
			{
				$db->query("insert into element_options set 
				ElementID='$ElementID', 
				Value='$OptionValue'");
			}
		}
		
		if($InputType==6)
		{
			if(isset($_REQUEST['Extensions']))
			{
				$SelectedExtensions = array();
				$SelectedExtensions = $_REQUEST['Extensions'];
			
				foreach($SelectedExtensions as $Extension)
				{
					$db->query("insert into element_options set 
					ElementID='$ElementID', 
					Value='$Extension'");
				}
			}
		}
		
		$FormDetails = FetchRecordByID($FormID, "TableID", "forms");
		
		$DirectoryName = $FormDetails['TableName'];
		
		//delete_directory("forms/$DirectoryName");
		
		$db->query("ALTER TABLE $DirectoryName ADD COLUMN $HTML_Name VARCHAR(255) NULL");
		
		$FormGeneratorClass->setTableName($DirectoryName);
		
		$FormGeneratorClass->setFormID($FormID);
		
		$FormGeneratorClass->setFormName($FormDetails["FormName"]);
		
		$FormInfo = $FormGeneratorClass->SetValuesSession();
		
		$FormInfo["SaveInDB"]=1;
		
		$FormGeneratorClass->UpdateForm($FormInfo);
		
		showmessage("Form field added successfully");
		
		redirect("view_fields.php?TableID=".$FormID, 0);
	}
}
if(isset($_REQUEST['TableID']) && $_REQUEST['TableID']!='')
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Dynamic Form Builder</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script language="javascript" type="text/javascript" src="js/client.js"></script>
</head>
<body>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<tr>
    	<td width="90%" align="left"><h1>Create Dynamic Forms with javascript validatation and php script</h1></td>
        <td width="10%" align="right"><a href="create_form.php">Create Form</a></td>
    </tr>
</table>
<hr />
<?php
	$db->query("select * from forms where TableID='".$_REQUEST['TableID']."'");
	if($db->num_rows()==0)
	{
		echo '<div align="center">No form found</div>';
	}
	else
	{
?>
<table width="100%" cellpadding="5" cellspacing="0" border="1" bordercolor="#CCCCCC" style="border-collapse:collapse;">
	<tr>
    	<td align="center" width="5%">S.No</td>
        <td align="left" width="40%">Form Name</td>
        <td align="left" width="30%">Email To Send</td>
        <td align="center" width="15%">Created Date</td>
        <td align="center" width="5%">Fields</td>
        <td align="center" width="5%">Delete</td>
    </tr>
    <?php
		$Sno=0;
		while($db->next_Record())
		{
			$Sno++;
	?>
    <tr>
    	<td align="center"><?=$Sno?></td>
        <td align="left"><?= $db->f('FormName') ?><br /><a target="_blank" href="forms/<?= $db->f('TableName') ?>/<?= $db->f('TableName') ?>.html">View Form</a> - <a target="_blank" href="forms/<?= $db->f('TableName') ?>/<?= $db->f('TableName') ?>_listing.php">View Listing</a></td>
        <td align="left"><?= $db->f('EmailToSend') ?></td>
        <td align="center"><?= formatdate($db->f('CreatedDate'), 'M d, Y') ?></td>
        <td align="center"><a href="view_fields.php?TableID=<?= $db->f('TableID') ?>">View</a></td>
        <td align="center"><a href="delete_form.php?TableID=<?= $db->f('TableID') ?>">Delete</a></td>
    </tr>
    <?php
		}
	?>
</table>
<div style="height:25px;">&nbsp;</div>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<tr>
    	<td width="50%" align="left"><h1>Add New Field</h1></td>
        <td width="50%" align="right"><a href="view_fields.php?TableID=<?= $_REQUEST['TableID'] ?>">View Fields</a></td>
    </tr>
</table>
<hr />
<form name="AddNewFieldForm" id="AddNewFieldForm" method="post" action="" onsubmit="return ValidateAddNewField();">
	<input type="hidden" name="AddNewFieldFormFlag" id="AddNewFieldFormFlag" />
    <input type="hidden" name="FormID" id="FormID" value="<?= $_REQUEST['TableID'] ?>" />
<table width="100%" cellpadding="5" cellspacing="0" border="1" bordercolor="#cccccc" style="border-collapse:collapse;">
	<tr>
	  <td align="left">Field Name</td>
	  <td align="left">Type</td>
	  <td align="center">Required</td>
	  <td align="center">Numeric Only</td>
	  <td align="center">Validate Email</td>
	  <td align="center">Show In Listing</td>
    <tr>
        <td width="25%" align="left"><input type="text" name="FieldName" id="FieldName" /></td>
        <td width="15%" align="left">
        <select name="InputType" id="InputType" onchange="LoadFileOrOptions(this);">
        <?php
            foreach($InputTypes as $Key => $Type)
            {
        ?>
            <option value="<?= $Key ?>"><?= $Type ?></option>
        <?php
            }
        ?>
        </select>
        </td>
        <td width="15%" align="center"><input type="checkbox" name="Required" id="Required" /></td>
        <td width="15%" align="center"><input type="checkbox" name="NumericOnly" id="NumericOnly" /></td>
        <td width="15%" align="center"><input type="checkbox" name="ValidateEmail" id="ValidateEmail" /></td>
        <td width="15%" align="center"><input type="checkbox" name="ShowInListing" id="ShowInListing" /></td>
    <tr>
    <tr id="TrOptions" style="display:none;">
    	<td colspan="6">Enter one value per line<br /><textarea name="Options" id="Options" style="width:99%; height:50px;"></textarea></td>
    </tr>
    <tr id="TrExtensions" style="display:none;">
    	<td colspan="6" align="left">
        	file format must be:
            <table cellpadding="0" cellspacing="0" border="0">
                    	<?php
							$ExtensionsTr=0;
							foreach($FilesFormat as $Format)
							{
								
								if($ExtensionsTr==1)
								{
									echo '<tr>';
								}
						?>
                        <td>
                        	<table cellpadding="0" cellspacing="0" border="0">
                            	<tr>
                                	<td><input type="checkbox" name="Extensions[]" value="<?= $Format ?>" /></td>
                                    <td><?= $Format ?></td>
                                    <td style="width:25px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                        <?php
								if($ExtensionsTr>8)
								{
									echo '</tr>';
								}
							}
                        ?>
                        <tr>
                        </tr>
                    </table>
        </td>
    </tr>
</table>
<div align="right" style="margin-top:10px;"><input type="submit" name="SubmitButton" id="SubmitButton" value="Submit" /></div>
</form>
</body>
</html>
<?php
	}
}
else
{
	redirect("index.php", 0);
}
?>