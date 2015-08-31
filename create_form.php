<?php
session_start();
extract($_REQUEST);
include_once("classes/commonfunctions.php");
include_once("form_generator.class.php");

$FormGeneratorClass = new FormGenerator();
unset($_SESSION['tmpField']);
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
<div align="left">
<h1>Create Dynamic Forms with javascript validatation and php script</h1>
<br /><br />
</div>
<?php
	if(isset($_REQUEST['ValidateCreateFormFlag']) && $_REQUEST['ValidateCreateFormFlag']=='true')
	{
		// Save basic form in db - Start
		$TableName = TableName($FormTitle);
		$FormGeneratorClass->setTableName($TableName);
		$FormGeneratorClass->setFormName($FormTitle);
		
		$SaveInDB=0;
		if(isset($_REQUEST['SaveInDB']))
		{
			$SaveInDB=1;
		}
		
		$db->query("insert into forms set 
		FormName='$FormTitle', 
		TableName='$TableName', 
		ReturnURL='$ReturnURL', 
		ThankYouMessage='$ThankYouMessage', 
		EmailToSend='$EmailToSend', 
		SaveInDB='$SaveInDB', 
		CreatedDate='".date("Y-m-d H:i:s")."'");
		
		$FormID = mysql_insert_id();
		$FormGeneratorClass->setFormID($FormID);
		$FormGeneratorClass->SaveForm();
		// Save basic form in db - End
		
		// Create Form Table - Start
		$FormGeneratorClass->CreateTable();
		// Create Form Table - End
		
		// Crate form files - Start
		$FormGeneratorClass->CreateFormHTMLFile($_REQUEST);
		// Crate form files - End
		
		// Create Form Php Insert Query - Start
		$FormGeneratorClass->CreatePhpInsertCode($_REQUEST);
		// Create Form Php Insert Query - End
		
		// Create Listing File - Start
		$FormGeneratorClass->CreateListingCode($_REQUEST);
		// Create Listing File - End
		
		// Create View Recird File - Start
		$FormGeneratorClass->CreateViewRecordCode($_REQUEST);
		// Create View Recird File - End
		
		showmessage("Form Created Successfully");
		redirect("index.php", 0);
	}
	else if(isset($_REQUEST['FormFieldsMoreOptionsFlag']) && $_REQUEST['FormFieldsMoreOptionsFlag']=='true')
	{
		$Count=0;
		foreach($ArrayKeys as $ArrayKey)
		{
			$Values = $Options[$Count];
			$OptionValues = explode("\r\n", $Values);
			
			foreach($OptionValues as $OptionValue)
			{
				if($OptionValue!='')
				{
					if(in_array(trim($OptionValue), $_SESSION['FormArray'][$ArrayKey]["OptionValues"]))
					{
						showmessage("You can't add same value options. Please try again");
						echo '<script>
							history.back();
						</script>';
						die();
					}
					$_SESSION['FormArray'][$ArrayKey]["OptionValues"][]=trim($OptionValue);
				}
			}
			$Count++;
		}
		
		foreach($FileKeys as $FileKey)
		{
			$ExtensionsArray  = RemoveSpecialCharacters($_SESSION['FormArray'][$FileKey]["FieldName"]);
			$ExtensionsArray .= "_Extensions";
			if(isset($_REQUEST[$ExtensionsArray]))
			{
				foreach($_REQUEST[$ExtensionsArray] as $Extension)
				{
					$_SESSION['FormArray'][$FileKey]["FileType"][]=$Extension;
				}
			}
		}
?>
	<form name="CreateForm" id="CreateForm" method="post" action="" onsubmit="return ValidateCreateForm();">
       	<input type="hidden" name="ValidateCreateFormFlag" id="ValidateCreateFormFlag" value="" />
        <div align="left">
        <table width="100%" cellpadding="5" cellspacing="0" border="0">
        	<tr>
            	<td align="left"><strong>Form Title:</strong></td>
            </tr>
            <tr>
            	<td align="left"><input type="text" name="FormTitle" id="FormTitle" maxlength="25" class="BigTxtField" /></td>
            </tr>
            <tr>
            	<td align="left"><strong>Return URL</strong></td>
            </tr>
            <tr>
            	<td align="left"><input type="text" name="ReturnURL" id="ReturnURL" class="BigTxtField" /></td>
            </tr>
            <tr>
            	<td align="left"><strong>Send This Info To This Email</strong></td>
            </tr>
            <tr>
            	<td align="left"><input type="text" name="EmailToSend" id="EmailToSend" class="BigTxtField" /></td>
            </tr>
            <tr>
            	<td align="left"><strong>Thank You Message</strong></td>
            </tr>
            <tr>
            	<td align="left"><input type="text" name="ThankYouMessage" id="ThankYouMessage" class="BigTxtField" /></td>
            </tr>
        </table>
        </div>
        <!--<table width="100%" cellpadding="5" cellspacing="0" border="1" bordercolor="#cccccc" style="border-collapse:collapse;">
        	<?php
				foreach($_SESSION['FormArray'] as $Key => $Elements)
				{
			?>
            <tr>
            	<td width="20%" align="left"><strong><?= $Elements["FieldName"]?></strong></td>
                <td width="20%" align="left"><?= $Elements["Type"][0] ?></td>
                <td width="20%" align="left">Required = <?= $YesNo[$Elements["Required"]]?></td>
                <td width="20%" align="left">Numeric Only = <?= $YesNo[$Elements["NumericOnly"]]?></td>
                <td width="20%" align="left">Validate Email = <?= $YesNo[$Elements["ValidateEmail"]]?></td>
            </tr>
            <?php
					if(in_array($Elements["Type"][1], $OptionsIDs))
					{
			?>
            <tr>
            	<td colspan="5" align="left">
                	<?= $Elements["FieldName"]?> <?= $Elements["Type"][0] ?> Values<br />
                    <?php
						echo '<ul>';
						foreach($Elements["OptionValues"] as $Values)
						{
							echo "<li>$Values</li>";
						}
						echo '</ul>';
                    ?>
                </td>
            </tr>
            <?php
					}
					else if($Elements["Type"][1]==6)
					{
			?>
            <tr>
            	<td colspan="5" align="left">
                <?php
					echo '<ul>';
					foreach($Elements["FileType"] as $Ext)
					{
						echo '<li>'.$Ext.'</li>';
					}
					echo '</ul>';
				?>
                </td>
            </tr>
            <?php
					}
				}
			?>
        </table>-->
        <div align="right" style="margin-top:10px;">
        <input type="hidden" name="SaveInDB" id="SaveInDB" value="1" /><input type="submit" name="GenerateFormButton" id="GenerateFormButton" value="Create Form" />
        </div>
    </form>
<?php
	}
	
	else if(isset($_REQUEST['ValidateFormFieldsTypeFlag']) && $_REQUEST['ValidateFormFieldsTypeFlag']=='true')
	{
		$Count=0;
		foreach($FieldName as $Field)
		{
			$FormFieldsArray[$Count]["FieldName"]=$Field;
			
			$Type = $_REQUEST['InputType'][$Count];
			$FormFieldsArray[$Count]["Type"]=array($InputTypes[$Type], $Type);
			
			$RequiredFieldName = 'Required'.$Count;
			$NumericOnlyFieldName = 'NumericOnly'.$Count;
			$ValidateEmailFieldName = 'ValidateEmail'.$Count;
			$ShowInListing = 'ShowInListing'.$Count;
			
			if(isset($_REQUEST[$RequiredFieldName]))
			{ $FormFieldsArray[$Count]["Required"]=1; }
			else
			{ $FormFieldsArray[$Count]["Required"]=0; }
			
			if(isset($_REQUEST[$NumericOnlyFieldName]))
			{ $FormFieldsArray[$Count]["NumericOnly"]=1; }
			else
			{ $FormFieldsArray[$Count]["NumericOnly"]=0; }
			
			if(isset($_REQUEST[$ValidateEmailFieldName]))
			{ $FormFieldsArray[$Count]["ValidateEmail"]=1; }
			else
			{ $FormFieldsArray[$Count]["ValidateEmail"]=0; }
			
			if(isset($_REQUEST[$ShowInListing]))
			{ $FormFieldsArray[$Count]["ShowInListing"]=1; }
			else
			{ $FormFieldsArray[$Count]["ShowInListing"]=0; }
			
			$FormFieldsArray[$Count]["Sequence"]=$Count + 1;
			
			$Count++;
		}
		$_SESSION['FormArray'] = array();
		$_SESSION['FormArray'] = $FormFieldsArray;
?>
	<form name="FormFieldsMoreOptions" id="FormFieldsMoreOptions" method="post" action="" onsubmit="return ValidateFormFieldsMoreOptions();">
       	<input type="hidden" name="FormFieldsMoreOptionsFlag" id="FormFieldsMoreOptionsFlag" value="" />
        <table width="100%" cellpadding="5" cellspacing="0" border="1" bordercolor="#cccccc" style="border-collapse:collapse;">
        	<?php
				foreach($_SESSION['FormArray'] as $Key => $Elements)
				{
			?>
            <tr>
            	<td width="25%" align="left"><strong><?= $Elements["FieldName"]?></strong></td>
                <td width="15%" align="left"><?= $Elements["Type"][0] ?></td>
                <td width="15%" align="left">Required = <?= $YesNo[$Elements["Required"]]?></td>
                <td width="15%" align="left">Numeric Only = <?= $YesNo[$Elements["NumericOnly"]]?></td>
                <td width="15%" align="left">Validate Email = <?= $YesNo[$Elements["ValidateEmail"]]?></td>
                <td width="15%" align="left">Show In Listing = <?= $YesNo[$Elements["ShowInListing"]]?></td>
            </tr>
            <?php
					if(in_array($Elements["Type"][1], $OptionsIDs))
					{
			?>
            <tr>
            	<td colspan="6" align="left">
                	<?= $Elements["FieldName"]?> <?= $Elements["Type"][0] ?> Values (Enter one value per line)
                    <textarea name="Options[]" id="Options<?= $Count ?>" style="width:99%; height:50px;"></textarea>
                    <input type="hidden" name="ArrayKeys[]" value="<?= $Key ?>" />
                </td>
            </tr>
            <?php
					}
					else if($Elements["Type"][1]==6)
					{
			?>
            <input type="hidden" name="FileKeys[]" value="<?= $Key ?>" />
            <tr>
            	<td colspan="6" align="left">
                	<?= $Elements["FieldName"]?> file format must be:
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
                                	<td><input type="checkbox" name="<?= RemoveSpecialCharacters($Elements["FieldName"]) ?>_Extensions[]" value="<?= $Format ?>" /></td>
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
            <?php
					}
				}
			?>
        </table>
        <div align="right" style="margin-top:10px;"><input type="submit" name="SubmitButton" id="SubmitButton" value="Submit" /></div>
    </form>
<?php
	}
	
	else if(isset($_REQUEST['ValidateFormFieldsFlag']) && $_REQUEST['ValidateFormFieldsFlag']=='true')
	{
		$_SESSION['tmpField']=$Fields;
		$FieldItem = explode("\r\n", $Fields);
?>
		<form name="FormFieldsType" id="FormFieldsType" method="post" action="" onsubmit="return ValidateFormFieldsType();">
       	<input type="hidden" name="ValidateFormFieldsTypeFlag" id="ValidateFormFieldsTypeFlag" value="" />
        <table width="100%" cellpadding="5" cellspacing="0" border="1" bordercolor="#cccccc" style="border-collapse:collapse;">
<?php
		$Count=0;
		$_SESSION['tmpFieldArray'] = array();
		foreach($FieldItem as $Field)
		{
			
			if($Field!='')
			{
				if(in_array($Field, $_SESSION['tmpFieldArray']))
				{
					showmessage("You can't multiple fields with same name. Try Again!");
					redirect("create_form.php", 0);
				}
				$_SESSION['tmpFieldArray'][] = $Field;
?>
			<input type="hidden" name="FieldName[]" id="FieldName<?= $Count ?>" value="<?= trim($Field) ?>" />
            <tr>
            	<td width="25%" align="left"><?= $Field ?></td>
                <td width="15%" align="left">
                <select name="InputType[]" id="InputType<?=$Count?>">
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
                <td width="15%" align="left">
                	<table cellpadding="0" cellspacing="0" border="0">
                    	<tr>
                        	<td><input type="checkbox" name="Required<?=$Count?>" id="Required<?=$Count?>" /></td>
                            <td>Required</td>
                        </tr>
                	</table>
                </td>
                <td width="15%" align="left">
                	<table cellpadding="0" cellspacing="0" border="0">
                    	<tr>
                        	<td><input type="checkbox" name="NumericOnly<?=$Count?>" id="NumericOnly<?=$Count?>" onclick="DisableEnable(this.checked, 'ValidateEmail<?=$Count?>');" /></td>
                            <td>Numeric Only</td>
                        </tr>
                	</table>
                </td>
                <td width="15%" align="left">
                	<table cellpadding="0" cellspacing="0" border="0">
                    	<tr>
                        	<td><input type="checkbox" name="ValidateEmail<?=$Count?>" id="ValidateEmail<?=$Count?>" onclick="DisableEnable(this.checked, 'NumericOnly<?=$Count?>');" /></td>
                            <td>Validate Email</td>
                        </tr>
                	</table>
                </td>
                <td width="15%" align="left">
                	<table cellpadding="0" cellspacing="0" border="0">
                    	<tr>
                        	<td><input type="checkbox" name="ShowInListing<?=$Count?>" id="ShowInListing<?=$Count?>" /></td>
                            <td>Show In Listing</td>
                        </tr>
                	</table>
                </td>
            <tr>
<?php
			$Count++;
			}
		}
?>
		</table>
        <div align="right" style="margin-top:10px;"><input type="submit" name="SubmitButton" id="SubmitButton" value="Submit" /></div>
        </form>
<?php
	}
	else
	{
		unset($_SESSION['tmpField']);
		unset($_SESSION['tmpFieldArray']);
?>
<div align="left">
    Enter form fields (One field per line)
    <form name="FormFields" id="FormFields" method="post" action="" onsubmit="return ValidateFormFields();">
    	<input type="hidden" name="ValidateFormFieldsFlag" id="ValidateFormFieldsFlag" />
    	<textarea name="Fields" id="Fields" style="width:100%; height:200px;"><?=$_SESSION['tmpField']?></textarea>
        <div align="right" style="margin-top:10px;"><input type="submit" name="SubmitButton" id="SubmitButton" value="Create Form" /></div>
    </form>
</div>
<?php
	}
?>
</body>
</html>