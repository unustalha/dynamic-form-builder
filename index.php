<?php
session_start();
extract($_REQUEST);
include_once("classes/commonfunctions.php");
include_once("form_generator.class.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Dynamic Form Builder</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
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
	$db->query("select * from forms");
	if($db->num_rows()==0)
	{
		echo '<div align="center">No forms found</div>';
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
<?php
	}
?>
</body>
</html>