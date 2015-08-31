<?php
include("db_mysql.php");
include("constants.php");
$db = new DB_Sql();
$db->Database = DATABASE_NAME;
$db->User     = DATABASE_USER;
$db->Password = DATABASE_PASSWORD;
$db->Host     = DATABASE_HOST;
$db1 = new DB_Sql();
$db1->Database = DATABASE_NAME;
$db1->User     = DATABASE_USER;
$db1->Password = DATABASE_PASSWORD;
$db1->Host     = DATABASE_HOST;
$db2 = new DB_Sql();
$db2->Database = DATABASE_NAME;
$db2->User     = DATABASE_USER;
$db2->Password = DATABASE_PASSWORD;
$db2->Host     = DATABASE_HOST;
$db3 = new DB_Sql();
$db3->Database = DATABASE_NAME;
$db3->User     = DATABASE_USER;
$db3->Password = DATABASE_PASSWORD;
$db3->Host     = DATABASE_HOST;
$db4 = new DB_Sql();
$db4->Database = DATABASE_NAME;
$db4->User     = DATABASE_USER;
$db4->Password = DATABASE_PASSWORD;
$db4->Host     = DATABASE_HOST;
$db5 = new DB_Sql();
$db5->Database = DATABASE_NAME;
$db5->User     = DATABASE_USER;
$db5->Password = DATABASE_PASSWORD;
$db5->Host     = DATABASE_HOST;

$db6 = new DB_Sql();
$db6->Database = DATABASE_NAME;
$db6->User     = DATABASE_USER;
$db6->Password = DATABASE_PASSWORD;
$db6->Host     = DATABASE_HOST;


function SubStrT($string='', $limit='', $ShowDots='.....')
{
	if(strlen($string)>$limit)
	{
		return substr($string, 0, $limit).$ShowDots;
	}
	else
	{
		return $string;
	}
}
// Convert non-standard characters to HTML
function tohtml($strValue)
{
 return htmlspecialchars($strValue);
}

// Convert value to URL

function tourl($strValue)
{
 return urlencode($strValue);
}

// Obtain specific URL Parameter from URL string

function get_param($param_name)
{
 global $HTTP_POST_VARS;
 global $HTTP_GET_VARS;

 $param_value = "";
 if(isset($HTTP_POST_VARS[$param_name]))
   $param_value = $HTTP_POST_VARS[$param_name];
 else if(isset($HTTP_GET_VARS[$param_name]))
   $param_value = $HTTP_GET_VARS[$param_name];
 return $param_value;
}

//Destroying Session on logout

function initialize($action)
{
	if (isset($action))
	{
		session_start();
		session_destroy();
	}
}
function get_session($param_name)
{
 global $HTTP_POST_VARS;
 global $HTTP_GET_VARS;
 global ${$param_name};

 $param_value = "";
 if(!isset($HTTP_POST_VARS[$param_name]) && !isset($HTTP_GET_VARS[$param_name]) && session_is_registered($param_name)) 
   $param_value = ${$param_name};

 return $param_value;
}

function tosql($value, $type)
{
 if($value == "")
   return "NULL";
 else
   if($type == "Number")
     return str_replace (",", ".", doubleval($value));
   else
   {
     if(get_magic_quotes_gpc() == 0)
     {
       $value = str_replace("'","''",$value);
       $value = str_replace("\\","\\\\",$value);
     }
     else
     {
       $value = str_replace("\\'","''",$value);
       $value = str_replace("\\\"","\"",$value);
     }

     return "'" . $value . "'";
   }
}

function strip($value)
{
 if(get_magic_quotes_gpc() == 0)
   return $value;
 else
   return stripslashes($value);
}


// Deleting of Directory
function deldir($dir){
   $current_dir = opendir($dir);
   while($entryname = readdir($current_dir)){
     if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!="..")){
         deldir("${dir}/${entryname}");
     }elseif($entryname != "." and $entryname!=".."){
         unlink("${dir}/${entryname}");
     }
   }
   closedir($current_dir);
   rmdir(${dir});
} 

// Making of Directory

function mkdir_p($target)
{
if (is_dir($target)||empty($target)) return 1; // best case check first
if (file_exists($target) && !is_dir($target)) return 0;
if (mkdir_p(substr($target,0,strrpos($target,'/'))))
  return mkdir($target); // crawl back up & create dir tree
return 0;
}
function php2sql($phpString){
	$pieces = explode("/", $phpString);
	$sqlString=$pieces[2]. "-". $pieces[1]. "-". $pieces[0];
	return $sqlString; 
}

// Takes care of SQL Injection

function sqlInjection($phpString){
	$pieces = explode("/", $phpString);
	$sqlString=$pieces[0];
	return $sqlString; 
}

function sql2php($sqlString){
	$pieces = explode("-", $sqlString);
	$phpString=$pieces[2]. "/". $pieces[1]. "/". $pieces[0];
	return $phpString; 
}

function getFieldDataByID($StringField,$WhereField,$WhereValue,$TableName)
{
		$sqlQuery="Select ".$StringField." from ".$TableName." where ".$WhereField."=".$WhereValue."";
		
		$db_conn=mysql_connect(DATABASE_HOST,DATABASE_USER, DATABASE_PASSWORD);
		$selectDB=mysql_select_db(DATABASE_NAME,$db_conn);
		$result=mysql_query($sqlQuery,$db_conn);
			while($row=mysql_fetch_array($result,MYSQL_NUM))
			{
				return $row[0];
			}

}
//Fetch Entire Data 
function FetchRecordByID($id,$primarykey,$tablename)
{
		//if(!(is_numeric($id)))
		//{
			//$url=$_SERVER['SCRIPT_FILENAME'];
			//redirect("index.php",0);
		//}

		$sqlQuery="Select * from ".$tablename." where ".$primarykey."='".$id."'";
		$db_conn=mysql_connect(DATABASE_HOST,DATABASE_USER, DATABASE_PASSWORD);
		$selectDB=mysql_select_db(DATABASE_NAME,$db_conn);
		$result=mysql_query($sqlQuery,$db_conn);
		$count=mysql_num_fields($result);
		$i=0;
		while($row=mysql_fetch_array($result,MYSQL_BOTH))
			{
				while($i<$count)
				{
					$object[mysql_field_name($result,$i)]=$row[$i];
					$i++;
				}
			}
return $object;
}

function getcurrentdate()
{
	return date(Y."-".m."-".d);	
}
function formatdate($DateValue, $Format='D F d, g:i a')
{
	if($DateValue=='0000-00-00' || $DateValue=='0000-00-00 00:00:00' || $DateValue==NULL)
	{
		return '-';
	}
	else
	{
		return date($Format, strtotime($DateValue));
	}
}
function getcurrentdatetime($Format='')
{
	if($Format!='')
	{
		return date($Format);
	}
	else
	{
		return date(d." - ".F." - ".Y." ".h.":".i.":".s." A");	
	}
}
function getcurrenttime()
{
	return date(h.":".i.":".s." A");	
}

function generatepassword($length)
{
  $password = "";
  $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
  $i = 0; 
  while ($i < $length) { 
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
    if (!strstr($password, $char)) { 
      $password .= $char;
      $i++;
    }
  }
  return $password;
}
function redirect($url,$time)
{
	echo '<meta http-equiv=refresh content='.$time.';URL='.$url.'>';
	exit;
}	
//Uploading File
function uploadfile($File,$dir)
{
		$completepath=$dir.'/'.strtolower(str_replace(" ","",$File['name']));
		//if(file_exists($completepath))
		//{
			$extension = explode('.',strtolower(str_replace(" ","",$File['name'])));
			$FileName=$extension[0].'_'.generatePassword(3).'.'.$extension[count($extension)-1];
		//}else
		//{
		//	$FileName=strtolower(str_replace(" ","",$File['name']));
		//}
			if(!file_exists($dir))
				mkdir($dir);
			$uploaddir = realpath ($dir);
			$path = $uploaddir."/";
		//******** Uploadin to a temporary location
			if (is_uploaded_file($File['tmp_name'])) 
			{
		//******** Copy the file to the location		
				$resultCopy = copy($File['tmp_name'], $path .$FileName);
				
				if (!$resultCopy )
					{
						echo "Transaction failed!";
						return 0;
					} 	
				else
					{
						return $FileName;
					}
			}
}

//Unlinking File
function unlinkfile($filelocations,$filename)
{
	foreach($filelocations as $location)
	{
		unlink($location.'/'.$filename);
	}
}

//Showing Alert Message
function showmessage($msg)
{
?>
<script language="javascript">
alert("<?=$msg?>");
</script>
<?php
}

//Window Close
function windowclose()
{
?>
	<script language="javascript">
	window.close();
	</script>
<?php
}

function refreshparentwindow()
{
?>
	<script language="javascript">
		window.opener.location.reload();
	</script>
<?php
}

//get Max Sequence Number
function getMaxSequence($tablename,$fieldname,$wherefield,$wherevalue,$db)
{
	$GetMaxSequence="select max($fieldname) from $tablename where $wherefield=$wherevalue";
	
	$db->query($GetMaxSequence);
	while($db->next_Record())
	{
		return $db->f(0);
	}
}

//You do not need to alter these functions
function getHeight($image) {
	$size = getimagesize($image);
	$height = $size[1];
	return $height;
}
//You do not need to alter these functions
function getWidth($image) {
	$size = getimagesize($image);
	$width = $size[0];
	return $width;
}

function SendMail($MailSendTo, $MailSubject, $MailMessage)
{
	$email_message = '<html>
		<head>
			<meta http-equiv="Content-Language" content="en-us">
			<meta name="GENERATOR" content="Microsoft FrontPage 5.0">
			<meta name="ProgId" content="FrontPage.Editor.Document">
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<title>Dear Recipient</title>
		</head>
		<body>
			<div>
				<font face="Arial" size="2">
					<div align="center">
						<p align="left"><strong><b><font face="Tahoma" color="#993300" size="1">
						<span style="font-size: 8pt; color: #993300; font-family: Tahoma">Dear Recepient,</span></font></b></strong>
					</div>
					<div align="center">
						<p align="left" dir="ltr">'.$MailSubject.'</p>
					</div>
					<div align="center">
						<p align="left" dir="ltr"></p>
					</div>
					<div align="center">'.$MailMessage.'</div>
				</font>
			</div>
			<p class="MsoBodyText" style="line-height: 100%" align="justify">
				<font color="#000000" face="Verdana" size="1">
					<strong>Thanks &amp; very best regards,<br /><br /></strong>
				</font>
				<strong>
					<font face="Tahoma" color="#993300" size="1">
						<span style="font-size: 8pt; color: #993300; font-family: Tahoma">Web Administrator<br /><br /></span>
					</font>
					<font face="Tahoma" size="1" color="#0000FF">'.FROMNAME.'</span>
					</font>
				</strong>
			</p>
		</body>
	</html>';
	//echo $email_message;
	//echo "<hr />";
	//echo "To: ".$MailSendTo;
	//die();
	
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: ".FROMNAME."<".FROMEMAIL.">\r\n";
	
	mail($MailSendTo, $MailSubject, $email_message, $headers);
}

function GetExtension($File)
{
	$info = pathinfo($File);
	return strtolower($info['extension']);
}

function delete_directory($dirname)
{
	if(is_dir($dirname))
	{
		$dir_handle = opendir($dirname);
	}
	if(!$dir_handle)
	{
		return false;
	}
	
	while($file = readdir($dir_handle))
	{
		if($file != "." && $file != "..")
		{
			if(!is_dir($dirname."/".$file))
			{
				unlink($dirname."/".$file);
			}
			else
			{
				delete_directory($dirname.'/'.$file);
			}
		}
	}
	closedir($dir_handle);
	rmdir($dirname);
	return true;
}

function FormElementSequence($FormID)
{
	$Sequence = getFieldDataByID("Sequence", "FormID", $FormID, "form_elements");
	$Sequence = $Sequence + 1;
	return $Sequence;
}
function RemoveSpecialCharacters($string)
{
	$new_string = preg_replace("/[^a-zA-Z0-9\s]/", "", $string);
	$return = str_replace(" ", "_", $new_string);
	return $return;
}
function TableFieldName($string)
{
	return str_replace(" ", "", $string);
}
function TableName($string)
{
	return str_replace(" ", "_", strtolower($string))."_".date("Ymdhis");
	//return str_replace(" ", "_", strtolower($string));
}
?>