<?php
session_start();
extract($_REQUEST);
include_once("classes/commonfunctions.php");
include_once("form_generator.class.php");
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
    	<td width="50%" align="left"><h1>Form Fields</h1></td>
        <td width="50%" align="right"><a href="add_new_field.php?TableID=<?= $_REQUEST['TableID'] ?>">Add New Field</a></td>
    </tr>
</table>
<hr />
<?php
	$db->query("select * from form_elements where FormID='".$_REQUEST['TableID']."'");
?>
<form name="formobject" id="formobject" method="post" action="update_sequence.php" onsubmit="return UpdateSequence();">
<input type="text" name="FormID" value="<?= $_REQUEST['TableID'] ?>" />
<input type="hidden" name="ValidateFormFieldsFormFlag" id="ValidateFormFieldsFormFlag" />
<table width="100%" cellpadding="5" cellspacing="0" border="1" bordercolor="#CCCCCC" style="border-collapse:collapse;">
	<tr>
    	<td width="5%" align="center">S.No</td>
        <td width="25%" align="left">Field Name</td>
        <td width="15%" align="left">Type</td>
        <td width="10%" align="center">Required</td>
        <td width="10%" align="center">Numeric Only</td>
        <td width="10%" align="center">Validate Email</td>
        <td width="10%" align="center">Sequence</td>
        <td width="10%" align="center">Show In Listing</td>
        <td width="5%" align="center">Delete</td>
    </tr>
    <?php
		$Sno=0;
		while($db->next_Record())
		{
			$Sno++;
	?>
    <tr>
    	<td align="center"><?= $Sno ?></td>
        <td align="left"><?= $db->f('Label') ?></td>
        <td align="left"><?= $InputTypes[$db->f('Type')] ?></td>
        <td align="center"><?= $YesNo[$db->f('IsRequired')] ?></td>
        <td align="center"><?= $YesNo[$db->f('IsNumeric')] ?></td>
        <td align="center"><?= $YesNo[$db->f('IsEmail')] ?></td>
        <td align="center"><input type="text" name="Sequence[]" value="<?= $db->f('Sequence') ?>" maxlength="2" size="2" onKeyPress="return numbersonly(event, false)" /><input type="hidden" name="FormElementID[]" value="<?= $db->f('TableID') ?>" /></td>
        <td align="center"><?= $YesNo[$db->f('ShowInListing')] ?></td>
        <td align="center"><a href="delete_field.php?TableID=<?= $db->f('TableID') ?>">Delete</a></td>
    </tr>
    <?php
			$db2->query("select * from element_options where ElementID='".$db->f('TableID')."'");
			if($db2->num_rows()>0)
			{
    ?>
    <tr>
    	<td colspan="9" align="left">
        <?php
			if($db->f('Type')==6)
			{
				echo 'Accepted File Formats:';
			}
			else if($db->f('Type')==3)
			{
				echo "Radio Button Values";
			}
			else if($db->f('Type')==4)
			{
				echo "Checkbox Values";
			}
			else if($db->f('Type')==5)
			{
				echo "Select Menu Values";
			}
		?>
        <ul>
        <?php
			while($db2->next_Record())
			{
				echo '<li>'.$db2->f('Value').'</li>';
			}
		?>
        </ul>
        </td>
    </tr>
    <?php
			}
		}
	?>
</table>
<div align="right">
<input type="submit" name="SubmitBtn" id="SubmitBtn" value="Update Sequence" />
</div>
</form>
<?php	
	}
?>
</body>
</html>
<?php
}
else
{
	redirect("index.php", 0);
}
?>