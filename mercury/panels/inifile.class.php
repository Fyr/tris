<?
/**
  Reads data from ini file and create a data array or object
**/
class IniFile
{
	private $sIniFilePath = ''; // relative or absolute path to the ini file
	
	/**
	  Constructor. Creates ini file class.
	  @param (string) $sPath - path to ini file. Default value is ''
	**/
	public function __construct($sPath = '')
	{
		if($sPath != '')
		{
			$this->setIniFilePath($sPath);
		}
	}
	
	/**
	  Sets the ini file path. Throws exception on invalid path.
	  @param (string) $sPath - path to the ini file
	  @return (boolean) - true, if file is readable
	**/
	public function setIniFilePath($sPath)
	{
		if(is_readable($sPath))
		{
			$this->sIniFilePath = $sPath;
			return true;
		}
		else
		{
			throw new Exception("$sPath does not exist or is not readable.");
		}
		return false;
	}
	
	/**
	  Parses ini file and returns data as an object. Throws exception if path not set
	  @return (object) - data of ini file as stdClass
	**/
	public function getIniDataObj()
	{
		if(empty($this->sIniFilePath))
		{
			throw new Exception("No ini file path defined");
		}
		$aData = parse_ini_file($this->sIniFilePath, true);
		$oData = new stdClass();
		foreach ($aData as $key => $value)
		{
			if(is_array($value))
			{
				foreach($value as $key2 => $value2)
				{
					$oData->$key->$key2 = $value2;
				}
			}
			else
			{
				$oData->$key = $value;
			}
		}
		return $oData;
	}
	
	/**
	  Parses ini file and returns data as an array. Throws exception if path not set
	  @return (array) - data of ini file as associative array
	**/
	public function getIniDataArray()
	{
		if (empty($this->sIniFilePath))
		{
			throw new Exception("No ini file path defined");
		}
		return parse_ini_file($this->sIniFilePath, true);
	}
}
?>