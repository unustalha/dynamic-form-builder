function UpdateSequence()
{
	with(document.formobject)
	{
		ValidateFormFieldsFormFlag.value='true';
	}
	return true;
}

function DisableEnable(Checkbox, Element)
{
	if(Checkbox=='true' || Checkbox=='1')
	{
		document.getElementById(Element).disabled=true;
	}
	else
	{
		document.getElementById(Element).disabled=false;
	}
}
function ValidateAddNewField()
{
	with(document.AddNewFieldForm)
	{
		if(FieldName.value=='')
		{
			alert("Please enter field name");
			FieldName.focus();
			return false;
		}
		AddNewFieldFormFlag.value='true';
	}
	return true;
}

function LoadFileOrOptions(DropDown)
{
	if(DropDown.value=='3' || DropDown.value=='4' || DropDown.value=='5')
	{
		document.getElementById("TrOptions").style.display='';
		document.getElementById("TrExtensions").style.display='none';
	}
	else if(DropDown.value=='6')
	{
		document.getElementById("TrOptions").style.display='none';
		document.getElementById("TrExtensions").style.display='';
	}
	else
	{
		document.getElementById("TrOptions").style.display='none';
		document.getElementById("TrExtensions").style.display='none';
	}
}

function ValidateFormFields()
{
	with(document.FormFields)
	{
		if(Fields.value=='')
		{
			alert("Please enter form fields");
			Fields.focus();
			return false;
		}
		ValidateFormFieldsFlag.value='true';
	}
	return true;
}

function ValidateFormFieldsType()
{
	with(document.FormFieldsType)
	{
		ValidateFormFieldsTypeFlag.value='true';
	}
	return true;
}

function ValidateFormFieldsMoreOptions()
{
	with(document.FormFieldsMoreOptions)
	{
		FormFieldsMoreOptionsFlag.value='true';
	}
	return true;
}
function ValidateCreateForm()
{
	with(document.CreateForm)
	{
		ValidateCreateFormFlag.value='true';
	}
	return true;
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