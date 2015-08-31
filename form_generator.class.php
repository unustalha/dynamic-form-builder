<?php
class FormGenerator
{
	private $FormTableQuery;
	private $TableName;
	private $FormID;
	private $FormName;
	private $InsertQuery;
	private $CheckBoxCode=array();
	private $EmailHTML;
	private $FileUploadCode=array();
	
	function setFileUploadCode($Code)
	{
		$this->FileUploadCode[] = $Code;
	}
	
	function getFileUploadCode()
	{
		return $this->FileUploadCode;
	}
	
	function setEmailHTML($HTML)
	{
		$this->EmailHTML = $HTML;
	}
	
	function getEmailHTML()
	{
		return $this->EmailHTML;
	}
	
	function setCheckBoxCode($Code)
	{
		$this->CheckBoxCode[] = $Code;
	}
	
	function getCheckBoxCode()
	{
		return $this->CheckBoxCode;
	}
	
	function setInsertQuery($Query)
	{
		$this->InsertQuery = $Query;
	}
	
	function getInsertQuery()
	{
		return $this->InsertQuery;
	}
	
	function setFormName($FormName)
	{
		$this->FormName = $FormName;
	}
	
	function getFormName()
	{
		return $this->FormName;
	}
	
	function setFormID($FormID)
	{
		$this->FormID = $FormID;
	}
	
	function getFormID()
	{
		return $this->FormID;
	}
	
	function setTableName($TableName)
	{
		$this->TableName = $TableName;
	}
	
	function getTableName()
	{
		return $this->TableName;
	}
	
	function setFormTableQuery($Query)
	{
		$this->FormTableQuery = $Query;
	}
	
	function getFormTableQuery()
	{
		return $this->FormTableQuery;
	}
	
	function CreateTable()
	{
		global $db;
		$FormTableQuery = $this->getFormTableQuery();
		$TableName = $this->getTableName();
		$db->query('CREATE TABLE '.$TableName.' (
		`TableID` INT(11) NOT NULL AUTO_INCREMENT, 
		'.$FormTableQuery.'
		`CreatedDate` DATETIME, 
		PRIMARY KEY (`TableID`));');
		
		mkdir("forms/".$TableName."/", "777");
	}
	
	function SaveForm()
	{
		$FormID = $this->getFormID();
		$TableName = $this->getTableName();
		
		global $db, $OptionsIDs;
		$FormTableQuery = '';
		$InsertQuery = '';
		$EmailHTML='';
		foreach($_SESSION['FormArray'] as $Key => $Elements)
		{
			$Label = addslashes($Elements["FieldName"]);
			$HTML_Name = RemoveSpecialCharacters($Label);
			$_SESSION['FormArray'][$Key]["HTML_Name"]=$HTML_Name;
			$Type = $Elements["Type"][1];
			$IsRequired = $Elements["Required"];
			$IsNumeric = $Elements["NumericOnly"];
			$IsEmail = $Elements["ValidateEmail"];
			$ShowInListing = $Elements["ShowInListing"];
			$Sequence = $Elements["Sequence"];
			
			if($Type==4)
			{
				$CheckBoxesCode = "
	if(isset($"."_REQUEST['".$HTML_Name."']))
	{
		$"."CheckBoxValues ='';
		$"."CountCheckBox=0;
		foreach($"."_REQUEST['".$HTML_Name."'] as $"."CheckBoxValue)
		{
			if($"."CountCheckBox==0)
			{
				$"."CheckBoxValues .= $"."CheckBoxValue;
			}
			else
			{
				$"."CheckBoxValues .= &quot;, &quot;. $"."CheckBoxValue;
			}
			$"."CountCheckBox++;
		}
	}";
				$this->setCheckBoxCode($CheckBoxesCode);
				$InsertQuery .= $HTML_Name . "='$"."CheckBoxValues', ";
				
				$EmailHTML .= "
		<tr>
			<td align='left'>".$Label."</td>
			<td align='left'>$"."CheckBoxValues</td>
		</tr>";
				$FormTableQuery .= $HTML_Name.' VARCHAR(255), ';
			}
			else if($Type==1)
			{
				$InsertQuery .= $HTML_Name . "='$" . $HTML_Name . "', ";
				$EmailHTML .= "
		<tr>
			<td align='left'>".$Label."</td>
			<td align='left'>&quot;.nl2br($".$HTML_Name.").&quot;</td>
		</tr>";
				$FormTableQuery .= $HTML_Name.' TEXT, ';
			}
			else if($Type==6)
			{
				$FileUploadCode ='';
				//mkdir("forms/".$TableName."/", "777");
				//mkdir("forms/".$TableName."/".strtolower($HTML_Name)."/", "777");
				foreach($Elements["FileType"] as $Extensions)
				{
					$FileUploadCode .='
	$'.$HTML_Name.'Extensions[]="'.$Extensions.'";';
				}
				$FileUploadCode .='
	$'.$HTML_Name.'Extension = GetExtension($_FILES["'.$HTML_Name.'"]["name"]);
	if(!in_array($'.$HTML_Name.'Extension, $'.$HTML_Name.'Extensions))
	{
		echo "<script>";
			echo "alert(\'Wrong '.$Label.' File Extension. Try again!\');";
			echo "history.back();";
		echo "</script>";
		die();
	}
	$'.$HTML_Name.'Name = uploadfile($_FILES["'.$HTML_Name.'"], "'.strtolower($HTML_Name).'");
	';
				
				$this->setFileUploadCode($FileUploadCode);
				
				$InsertQuery .= $HTML_Name . "='$".$HTML_Name."Name', ";
				$EmailHTML .= "
		<tr>
			<td align='left'>".$Label."</td>
			<td align='left'><a href='&quot;.DOMAINNAME.&quot;forms/".$TableName."/".strtolower($HTML_Name)."/&quot;.$".$HTML_Name."Name.&quot;'>Click here to view it</a></td>
		</tr>";
				$FormTableQuery .= $HTML_Name.' VARCHAR(255), ';
			}
			else
			{
				$InsertQuery .= $HTML_Name . "='$" . $HTML_Name . "', ";
				$EmailHTML .= "
		<tr>
			<td align='left'>".$Label."</td>
			<td align='left'>$".$HTML_Name."</td>
		</tr>";
				$FormTableQuery .= $HTML_Name.' VARCHAR(255), ';
			}
			//$Sequence = FormElementSequence($FormID);
			$db->query("insert into form_elements set 
			FormID='$FormID', 
			Label='$Label', 
			HTML_Name='$HTML_Name', 
			Type='$Type', 
			IsRequired='$IsRequired', 
			IsNumeric='$IsNumeric', 
			IsEmail='$IsEmail', 
			ShowInListing='$ShowInListing', 
			Sequence='$Sequence'");
			$ElementID = mysql_insert_id();
			
			if(in_array($Elements["Type"][1], $OptionsIDs))
			{
				foreach($Elements["OptionValues"] as $Values)
				{
					$db->query("insert into element_options set 
					ElementID='$ElementID', 
					Value='$Values'");
				}
			}
			if($Elements["Type"][1]==6)
			{
				foreach($Elements["FileType"] as $Type)
				{
					$db->query("insert into element_options set 
					ElementID='$ElementID', 
					Value='$Type'");
				}
			}
		}
		$this->setFormTableQuery($FormTableQuery);
		$this->setInsertQuery($InsertQuery);
		$this->setEmailHTML($EmailHTML);
	}

	function GenerateFormHTML($object)
	{
		$FormID = $this->getFormID();
		$TableName = $this->getTableName();
		$FormTitle = $this->getFormName();
		ob_start();
?>
	<form name="<?=$TableName?>" id="<?=$TableName?>" method="post" action="<?=$TableName?>.php" onsubmit="return Validate<?=$TableName?>();" enctype="multipart/form-data">
	<input type="hidden" name="Validate<?=$TableName?>Flag" id="Validate<?=$TableName?>Flag" />
	<table width="100%" cellpadding="5" cellspacing="0" border="0">
		<tr>
			<td colspan="2" class="tableheading"><strong><?= $object["$FormTitle"] ?></strong></td>
		</tr>
		<?php
			$Validation_JS='';
			foreach($_SESSION['FormArray'] as $Key => $Elements)
			{
		?>
		<tr>
			<td align="left" class="tdrightcontents"><?=$Elements["FieldName"]?><?php if($Elements["Required"]==1) { echo '&nbsp;<span class="mandatory">*</span>'; } ?></td>
			<td align="left" class="tdleftcontents">
			<?php
				if($Elements["Type"][1]==7)
				{
					if($Elements["Required"]==1)
					{
					$Validation_JS .='
			if('.$Elements["HTML_Name"].'.value==\'\')
			{
					alert("'.SELECT_ALERT.$Elements["FieldName"].'");
					'.$Elements["HTML_Name"].'.focus();
					return false;
			}';
					}
			?>
            <script>
$(document).ready(function(){
	$('#<?= $Elements["HTML_Name"] ?>').simpleDatepicker({ startdate: <?php echo $startdate = date("Y") - 50; ?>, enddate: <?php echo $enddate = date("Y"); ?> });
});
</script>
			<input class="formElementDev" type="text" name="<?= $Elements["HTML_Name"] ?>" id="<?= $Elements["HTML_Name"] ?>" />
			<?php
				}
				else if($Elements["Type"][1]==6)
				{
					if($Elements["Required"]==1)
					{
					$Validation_JS .='
			if('.$Elements["HTML_Name"].'.value==\'\')
			{
				alert("'.SELECT_ALERT.$Elements["FieldName"].' to upload");
				'.$Elements["HTML_Name"].'.focus();
				return false;
			}';
					}
			?>
			<input type="file" name="<?= $Elements["HTML_Name"] ?>" id="<?= $Elements["HTML_Name"] ?>" />
            <?php
					if(isset($Elements["FileType"]))
					{
						$AvailableExtensions='Only ';
						$CountExt=0;
						$ExtensionValidation='';
						foreach($Elements["FileType"] as $Ext)
						{
							$CountExt++;
							if($CountExt==1)
							{
								$AvailableExtensions .= $Ext;
								$ExtensionValidation .= 'CheckExtension('.$Elements["HTML_Name"].'.value)!="'.$Ext.'"';
							}
							else
							{
								$AvailableExtensions .= ", ".$Ext;
								$ExtensionValidation .= ' && CheckExtension('.$Elements["HTML_Name"].'.value)!="'.$Ext.'"';
							}
						}
						$AvailableExtensions .= ' files allowed';
						echo "<br /><i>* ".$AvailableExtensions."</i>";
						
						$Validation_JS .='
			if('.$ExtensionValidation.')
			{
				alert("'.$AvailableExtensions.'");
				'.$Elements["HTML_Name"].'.focus();
				return false;
			}';

					}
				}
				else if($Elements["Type"][1]==5)
				{
					if($Elements["Required"]==1)
					{
					$Validation_JS .='
			if('.$Elements["HTML_Name"].'.value==\'\')
			{
				alert("'.SELECT_ALERT.$Elements["FieldName"].'");
				'.$Elements["HTML_Name"].'.focus();
				return false;
			}';
					}
			?>
			<select class="formElementDev" name="<?= $Elements["HTML_Name"] ?>" id="<?= $Elements["HTML_Name"] ?>">
				<option value="">Please Select</option>
				<?php
					foreach($Elements["OptionValues"] as $Values)
					{
				?>
				<option value="<?=$Values?>"><?=$Values?></option>
				<?php
					}
				?>
			</select>
			<?php
				}
				else if($Elements["Type"][1]==4)
				{
					foreach($Elements["OptionValues"] as $Values)
					{
			?>
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td><input type="checkbox" name="<?= $Elements["HTML_Name"] ?>[]" value="<?=$Values?>" /></td>
					<td><?=$Values?></td>
				</tr>
			</table>
			<?php			
					}
				}
				else if($Elements["Type"][1]==3)
				{
					$Count=0;
					foreach($Elements["OptionValues"] as $Values)
					{
						$Count++;
						if($Count==1)
						{
							$Checked=' checked="checked"';
						}
						else
						{
							$Checked='';
						}
			?>
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td><input type="radio" name="<?= $Elements["HTML_Name"] ?>" value="<?=$Values?>"<?=$Checked?> /></td>
					<td><?=$Values?></td>
				</tr>
			</table>
			<?php			
					}
				}
				else if($Elements["Type"][1]==2)
				{
					if($Elements["Required"]==1)
					{
					$Validation_JS .='
			if('.$Elements["HTML_Name"].'.value==\'\')
			{
				alert("'.ENTER_ALERT.$Elements["FieldName"].'");
				'.$Elements["HTML_Name"].'.focus();
				return false;
			}';
					}
			?>
			<input class="formElementDev" type="password" name="<?= $Elements["HTML_Name"] ?>" id="<?= $Elements["HTML_Name"] ?>" />
			<?php
				}
				else if($Elements["Type"][1]==1)
				{
					if($Elements["Required"]==1)
					{
					$Validation_JS .='
			if('.$Elements["HTML_Name"].'.value==\'\')
			{
				alert("'.ENTER_ALERT.$Elements["FieldName"].'");
				'.$Elements["HTML_Name"].'.focus();
				return false;
			}';
					}
			?>
			<textarea name="<?= $Elements["HTML_Name"] ?>" id="<?= $Elements["HTML_Name"] ?>"></textarea>
			<?php
				}
				else
				{
					if($Elements["NumericOnly"]==1)
					{
						$JsFunctions = ' onKeyPress="return numbersonly(event, false)"';
					}
					else
					{
						$JsFunctions='';
					}
					if($Elements["Required"]==1)
					{
					$Validation_JS .='
			if('.$Elements["HTML_Name"].'.value==\'\')
			{
				alert("'.ENTER_ALERT.$Elements["FieldName"].'");
				'.$Elements["HTML_Name"].'.focus();
				return false;
			}';
					}
					
					if($Elements["ValidateEmail"]==1)
					{
						$Validation_JS .='
			if(isEmail('.$Elements["HTML_Name"].'.value)==false)
			{
				alert("'.ENTER_ALERT.$Elements["FieldName"].' in correct format");
				'.$Elements["HTML_Name"].'.focus();
				return false;
			}';
					}
			?>
			<input class="formElementDev" type="text" name="<?= $Elements["HTML_Name"] ?>" id="<?= $Elements["HTML_Name"] ?>"<?=$JsFunctions?> />
			<?php
				}
			?>
			</td>
		</tr>
		<?php
			}
		?>
        <tr>
        	<td>&nbsp;</td>
            <td><table cellpadding="0" cellspacing="0" border="0">
                    	<tr>
                    	  <td valign="top"><img id="siimage" align="left" style="margin-right: 5px; border: 1px solid #CCC;" src="captcha.php?sid=<?php echo md5(time()) ?>" /></td>
                        	<td valign="top">
                            	<a tabindex="-1" style="border-style: none" href="#" title="Refresh Image" onClick="document.getElementById('siimage').src = 'captcha.php?sid=' + Math.random(); return false"><img src="images/refresh.gif" alt="Reload Image" border="0" onClick="this.blur()" align="bottom" /></a>
                            </td>
                        </tr>
                    </table></td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
            <td>Enter the sum of above two values</td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
            <td><input type="text" name="SecurityCode" id="SecurityCode" value="" class="txt-filed" /></td>
        </tr>
		<tr>
			<td width="25%" align="left">&nbsp;</td>
			<td width="75%" align="left"><input class="formElementButton" type="reset" name="Reset" id="Reset" value="Reset" />&nbsp;<input class="formElementButton" type="submit" name="SubmitButton" id="SubmitButton" value="Submit" /></td>
		</tr>
	</table>
	</form>
<script>
	function isEmail(theStr) 
	{
		var atIndex = theStr.indexOf('@');
		var dotIndex = theStr.indexOf('.', atIndex);
		var flag = true;
		theSub = theStr.substring(0, dotIndex+1)
		if ((atIndex < 1)||(atIndex != theStr.lastIndexOf('@'))||(dotIndex < atIndex + 2)||(theStr.length <= theSub.length)) 
		{	 
			flag = false; 
		}
		else 
		{ 
			flag = true; 
		}
		return(flag);
	}
	
	function numbersonly(e, decimal)
	{
		var key;
		var keychar;
		
		if(window.event)
		{
			key = window.event.keyCode;
		}
		else if(e)
		{
			key = e.which;
		}
		else
		{
			return true;
		}
		
		keychar = String.fromCharCode(key);
		
		if((key==null) || (key==0) || (key==8) ||  (key==9) || (key==13) || (key==27) )
		{
			return true;
		}
		else if ((("0123456789").indexOf(keychar) > -1))
		{
			return true;
		}
		else if (decimal)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function CheckExtension(filename)
	{
		var parts = filename.split('.');
		var extension = parts[parts.length-1];
		return extension.toLowerCase();
	}
	
	function Validate<?=$TableName?>()
	{
		with(document.<?=$TableName?>)
		{
			<?=$Validation_JS ?>
			
			else if(SecurityCode.value=='')
			{
				alert("ErrorMsg", "Please enter security code", SecurityCode);
				return false;
			}
			else if(SecurityCode.value!='')
			{
				alert("ErrorMsg", "Please wait...");
				$.getJSON("fireaction.php?ValidateSecutiryCode="+SecurityCode.value+"&refresh="+Math.random(), null, function(data){
					if(data.Error==1)
					{
						$('.jqimessage').html(data.Msg);
						return false;
					}
					else
					{
						Error.value=0;
						Validate<?=$TableName?>Flag.value='true';
						document.<?=$TableName?>.submit();
					}
				});
			}
		}
		return true;
	}
	</script>
	<?php
		$HTML = ob_get_contents();
		ob_end_clean();
		
		$return['HTML'] = $HTML;
		return $return;
	}
	
	function CreateFormHTMLFile($object)
	{
		$FormID = $this->getFormID();
		$Return = $this->GenerateFormHTML($object);
		$TableName = $this->getTableName();
		
		$HTML = $Return['HTML'];
		$HTMLFile = fopen("forms/".$TableName."/".$TableName.".html", "w+");
		fwrite($HTMLFile, $HTML, strlen($HTML));
		fclose($HTMLFile);
	}
	
	function CreatePhpInsertCode($object)
	{
		$FormID = $this->getFormID();
		$TableName = $this->getTableName();
		$FormTitle = $this->getFormName();
		$InsertQuery = $this->getInsertQuery();
		ob_start();
?>
&lt;?php
include_once(&quot;../../classes/commonfunctions.php&quot;);
if(isset($_REQUEST[&quot;Validate<?=$TableName?>Flag&quot;]) &amp;&amp; $_REQUEST[&quot;Validate<?=$TableName?>Flag&quot;]=='true')
{
	extract($_REQUEST);

<?php
	$FileUploadCodes = $this->getFileUploadCode();
	foreach($FileUploadCodes as $FileUploadCode)
	{
		echo $FileUploadCode;
	}
	
	$CheckBoxCodes = $this->getCheckBoxCode();
	foreach($CheckBoxCodes as $CheckBoxCode)
	{
		echo $CheckBoxCode;
	}
	
	if(isset($object['SaveInDB']))
	{
?>
	
    
    $Query = &quot;insert into <?=$TableName?> set <?=$InsertQuery?> CreatedDate='&quot;.date(&quot;Y-m-d H:i:s&quot;).&quot;'&quot;;
	
    $db-&gt;query($Query);
    
<?php
	}
	if($object['EmailToSend']!='')
	{
?>
	$EmailHTML="
    <table width='100%' cellpadding='5' cellspacing='0' border='0'>
		<tr>
		    <td colspan='2'><?= $FormTitle ?> Request</td>
		</tr>
		<tr>
		    <td width='25%'>&nbsp;</td>
		    <td width='75%'>&nbsp;</td>
		</tr>
		<?= $this->getEmailHTML()?>

	</table>";

	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: No Reply&lt;no-replay@domain.com&gt;\r\n";
    
    $SendToEmail = getFieldDataByID('EmailToSend', 'TableID', <?=$FormID?>, 'forms');
	$ReturnMessage = getFieldDataByID('ThankYouMessage', 'TableID', <?=$FormID?>, 'forms');

	mail($SendToEmail, '<?= $FormTitle ?> Request', $EmailHTML, $headers);
	
    echo '&lt;script&gt;';
	echo 'alert(&quot;'.$ReturnMessage.'&quot;)';
	echo '&lt;/script&gt;';
    
<?php
	}
	if($object['ReturnURL']!='')
	{
?>
    redirect(&quot;<?= $object["ReturnURL"] ?>&quot;, 0);
<?php
	}
?>
}
?&gt;
<?php
		$PhpCode = htmlspecialchars_decode(ob_get_contents());
		ob_end_clean();
		
		$PhpFile = fopen("forms/".$TableName."/".$TableName.".php", "w+");
		fwrite($PhpFile, $PhpCode, strlen($PhpCode));
		fclose($PhpFile);
	}
	
	function CreateListingCode($object)
	{
		$FormID = $this->getFormID();
		$TableName = $this->getTableName();
		$FormTitle = $this->getFormName();
		$InsertQuery = $this->getInsertQuery();
		
		$TableStart = '
	<table width="100%" cellpadding="5" cellspacing="0" border"1" bordercolor="#cccccc" style="border-collapse:collapse;">
		<tr>
			<td width="5%" align="center">S.No</td>';
		$TableHeadings='';
		$TableData='';
		foreach($_SESSION['FormArray'] as $Key => $Elements)
		{
			if($Elements["ShowInListing"]==1)
			{
				$TableHeadings .= '
			<td align="center">'.$Elements["FieldName"].'</td>';
				if($Elements["Type"][1]==6)
				{
					$TableData[]= '
			<td align="center">
				<a target="_blank" href="<?= DOMAINNAME?>forms/'.$TableName.'/'.strtolower(RemoveSpecialCharacters($Elements["FieldName"])).'/<?=$db->f("'.RemoveSpecialCharacters($Elements["FieldName"]).'")?>">View</a>
			</td>';
				}
				else
				{
					$TableData[]= '
			<td align="center"><?=$db->f("'.RemoveSpecialCharacters($Elements["FieldName"]).'")?></td>';
				}
				
			}
		}
		$TableHeadings .= '
			<td align="center">View Details</td>';
		//$ListingPageHTML = 	$TableStart.$TableHeadings.'</tr><tr>'.$TableData.'</tr><table>';
		
		ob_start();
?>
&lt;?php
include_once(&quot;../../classes/commonfunctions.php&quot;);
$db-&gt;query(&quot;select * from <?= $TableName ?> order by CreatedDate desc&quot;);
if($db-&gt;num_rows()==0)
{
	echo '&lt;div align=&quot;center&quot;&gt;No records found&lt;/div&gt;';
}
else
{
?&gt;
	&lt;div align=&quot;left&quot;&gt;&lt;?=$db->num_rows()?&gt; records found&lt;/div&gt;
    <?= $TableStart.$TableHeadings.'
		</tr>' ?>

&lt;?php
    
    $Sno=0;
    
    while($db-&gt;next_record())
    {
    	$Sno++;
?&gt;
    	<tr>
        	<td align="center">&lt;?=$Sno?&gt;</td>
            <?php
				foreach($TableData as $Data)
				{
					echo $Data;
				}
			?>
            
            <td align="center"><a href="#" onclick="ShowRecord_<?=$TableName?>('&lt;?=$db->f('TableID')?&gt;');">Details</a></td>
        </tr>
&lt;?php
    }
?&gt;
	</table>
&lt;?php
}
?&gt;

&lt;script&gt;
function ShowRecord_<?=$TableName?>(TableID)
{
	window.open("view_record_<?=$TableName?>.php?TableID="+TableID+"&amp;refresh="+Math.random());
}
&lt;/script&gt;
<?php		
		
		$ListingCode = htmlspecialchars_decode(ob_get_contents());
		ob_end_clean();
		
		$ListingFile = fopen("forms/".$TableName."/".$TableName."_listing.php", "w+");
		fwrite($ListingFile, $ListingCode, strlen($ListingCode));
		fclose($ListingFile);
	}
	
	function CreateViewRecordCode($object)
	{
		$FormID = $this->getFormID();
		$TableName = $this->getTableName();
		$FormTitle = $this->getFormName();
		$InsertQuery = $this->getInsertQuery();
		ob_start();
?>
&lt;?php
include_once(&quot;../../classes/commonfunctions.php&quot;);
if(isset($_REQUEST[&quot;TableID&quot;]) && $_REQUEST[&quot;TableID&quot;]!='')
{
	$db-&gt;query(&quot;select * from <?= $TableName ?> where TableID='&quot;.$_REQUEST[&quot;TableID&quot;].&quot;'&quot;);
    if($db-&gt;num_rows()==0)
    {
?&gt;
    	&lt;div align=&quot;center&quot;&gt;No record found&lt;/div&gt;
&lt;?php
    }
    else
    {
    $db-&gt;next_Record();
?&gt;
<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<tr>
    	<td colspan="2" align="left"><strong><?= $FormTitle ?> Details</strong></td>
    </tr>
    <tr>
    	<td width="25%" align="left">&nbsp;</td>
        <td width="75%" align="left">&nbsp;</td>
    </tr>
    <?php
		foreach($_SESSION['FormArray'] as $Key => $Elements)
		{
	?>
    <tr>
    	<td align="left"><?= $Elements["FieldName"] ?></td>
        <?php
			if($Elements["Type"][1]==6)
			{
		?>
        <td align="left">
				<a target="_blank" href="&lt;?= DOMAINNAME?&gt;forms/<?=$TableName?>/<?=strtolower(RemoveSpecialCharacters($Elements["FieldName"]))?>/&lt;?=$db->f("<?=RemoveSpecialCharacters($Elements["FieldName"])?>")?&gt;">Click here to view it</a>
			</td>
        <?php
			}
			else
			{
		?>
        <td>&lt;?=$db->f("<?=RemoveSpecialCharacters($Elements["FieldName"])?>")?&gt;</td>
        <?php
			}
		?>
    </tr>
    <?php
		}
	?>
    <tr>
    	<td>Created Date</td>
        <td>&lt;?=formatdate($db->f('CreatedDate'), &quot;F d, Y&quot;)?&gt;</td>
    </tr>
</table>
&lt;?php    
    }
}
else
{
?&gt;
&lt;script&gt;
	alert(&quot;Error in loading data&quot;);
	window.close();
&lt;/script&gt;
&lt;?php	
}
?&gt;
<?php		
		$HTML = htmlspecialchars_decode(ob_get_contents());
		ob_end_clean();
		
		$ViewRecordFile = fopen("forms/".$TableName."/view_record_".$TableName.".php", "w+");
		fwrite($ViewRecordFile, $HTML, strlen($HTML));
		fclose($ViewRecordFile);
	}
	
	function SetValuesSession()
	{
		unset($_SESSION['FormArray']);
		global $db, $db2, $db3, $OptionsIDs, $InputTypes;
		$FormID = $this->getFormID();
		$TableName = $this->getTableName();
		
		$db->query("select * from forms where TableID='$FormID'");
		
		while($db->next_Record())
		{
			$FormDetails['FormTitle']=$db->f('FormName');
			$FormDetails['ReturnURL']=$db->f('ReturnURL');
			$FormDetails['EmailToSend']=$db->f('EmailToSend');
			$FormDetails['ThankYouMessage']=$db->f('ThankYouMessage');
			
			$db2->query("select * from form_elements where FormID='$FormID' order by Sequence asc");
			$Count=0;
			while($db2->next_Record())
			{
				$_SESSION['FormArray'][$Count]["FieldName"]=$db2->f('Label');
				$_SESSION['FormArray'][$Count]["Type"]=array($InputTypes[$db2->f('Type')], $db2->f('Type'));
				$_SESSION['FormArray'][$Count]["Required"]=$db2->f('IsRequired');
				$_SESSION['FormArray'][$Count]["NumericOnly"]=$db2->f('IsNumeric');
				$_SESSION['FormArray'][$Count]["ValidateEmail"]=$db2->f('IsEmail');
				$_SESSION['FormArray'][$Count]["ShowInListing"]=$db2->f('ShowInListing');
				$_SESSION['FormArray'][$Count]["Sequence"]=$db2->f('Sequence');
				
				if(in_array($db2->f('Type'), $OptionsIDs))
				{
					$db3->query("select * from element_options where ElementID='".$db2->f('TableID')."'");
					while($db3->next_Record())
					{
						$_SESSION['FormArray'][$Count]["OptionValues"][]=$db3->f('Value');
					}
				}
				if($db2->f('Type')==6)
				{
					$db3->query("select * from element_options where ElementID='".$db2->f('TableID')."'");
					while($db3->next_Record())
					{
						$_SESSION['FormArray'][$Count]["FileType"][]=$db3->f('Value');
					}
				}
				$Count++;
			}
		}
		return $FormDetails;
	}
	
	function DeleteFiles()
	{
		$TableName = $this->getTableName();
		//unlink("forms/".$TableName."/".$TableName.".html");
		//unlink("forms/".$TableName."/".$TableName.".php");
		//unlink("forms/".$TableName."/".$TableName."_listing.php");
		//unlink("forms/".$TableName."/view_record_".$TableName.".php");
	}
	
	function UpdateForm($FormDetails)
	{
		global $db;
		$FormID = $this->getFormID();
		$db->query("delete from form_elements where FormID='$FormID'");
		
		$this->DeleteFiles();
		
		$this->SaveForm();
		
		$this->CreateFormHTMLFile($FormDetails);
		
		$this->CreatePhpInsertCode($FormDetails);
		
		$this->CreateListingCode($FormDetails);
		
		$this->CreateViewRecordCode($FormDetails);
	}
}
?>