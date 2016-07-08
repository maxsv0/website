<?php
// *** include/class.module.php
// *** DO NOT EDIT THIS FILE
// *** WILL BE OVERWRITTEN DURING UPDATE


class MSV_Module {
	public $website 			= "";	//website object link
	public $name 				= "";
	public $enabled 			= true;		// module will be loaded
	public $loaded 				= false;	// xml config loaded
	public $started 			= false;	// php file included
	public $pathConfig 			= "";
	public $pathModule 			= "";
	public $activationLevel 	= 5;
	public $activationUrl 		= "";
	public $useseo 				= "";		// list of tables for SEO module
	public $constants			= array();
	public $tables				= array();
	public $filters				= array();
	public $locales				= array();
	public $files				= array();
	public $api					= array();	// list of API functions
	public $dependency			= array();
	public $pageUrlParam 		= "";
	public $itemsPerPage 		= 10;
	
	// XML config data
	public $config				= array();
	public $configInstall		= array();
	public $configLocales		= array();
	
	// standart module information:
	public $date 				= "";
	public $title 				= "";
	public $version 			= "";
	public $description 		= "";
	
	function __construct($module) {
		$this->name = $module;
		
		
		$pathConfig = ABS_INCLUDE."/module/$module/config.xml";
		$pathModule = ABS_INCLUDE."/module/$module/$module.php";
		
		if (file_exists($pathConfig) && is_readable($pathConfig)) {
			$this->pathConfig = $pathConfig;
		}
		if (file_exists($pathModule) && is_readable($pathModule)) {
			$this->pathModule = $pathModule;
		}

		if (empty($this->pathConfig)) {
			$this->enabled = false;
			
			$pathConfig = ABS_INCLUDE."/module/-$module/config.xml";
			$pathModule = ABS_INCLUDE."/module/-$module/$module.php";
		   	
			if (file_exists($pathConfig) && is_readable($pathConfig)) {
				$this->pathConfig = $pathConfig;
			}
			if (file_exists($pathModule) && is_readable($pathModule)) {
				$this->pathModule = $pathModule;
			}
		}
		
		// load if enabled
		if ($this->enabled && !empty($this->pathConfig)) {
			// load config function requires name->WTF??
			// compare name in config with $module
			$this->loadConfig($this->pathConfig);
	
			$this->loaded = true;
		}
	}
	
	function runUrl($url) {
		if (!$this->pathModule) return false;
		
		// always include all files at /admin/ page
		if ($url === "*" || $url === "/admin/") {
			// include without check
			
			return include($this->pathModule);
		} else {
			
			// check activationUrl
			if (empty($this->activationUrl)) {
				return false;
			}
			
			$patternUrl = str_replace("*", "(.*)", $this->activationUrl);
			//?????if (strpos($patternUrl, "/") !== 0) $patternUrl = "/".$patternUrl;
			
			$pattern = "|^".$patternUrl."$|";
			preg_match($pattern, $url, $matches);
			
			if (empty($matches)) {
				return false;
			}
	
			// mark module as started
			$this->started = true;
			
			return include($this->pathModule);
		}
	}
	function runFilter($filter) {
		if ($this->name !== $filter["module"]) {
			return false;
		}
		
		if ($filter["url"] === "*") {
			$patternUrl = "(.*)";
		} else {
			$patternUrl = str_replace("*", "([-_a-z0-9A-Z\\.]+)", $filter["url"]);
			$patternUrl = str_replace("/", "\\/", $patternUrl);
		}
		
		$pattern = "|^".$patternUrl."[\/]?$|";

		// TODO: ... check
		$requestUrl = $this->website->requestUrl;

		preg_match($pattern, $requestUrl, $matches);

		if (empty($matches)) {
			return false;
		}
		$this->website->requestUrlMatch = $matches;
		
		if (!empty($filter["setpage"])) {
			
			// TODO: fix bug
			//$this->website->setRequestUrl($filter["setpage"]);
			$this->website->loadPage($filter["setpage"]);
		}
		

		if (!function_exists($filter["action"])) {
			return false;
		}
		
		$evalCode = $filter["action"]."(\$this);";
		eval($evalCode);
		
		return true;
	}
	
	function runInstallHook() {
		if (empty($this->installHook)) {
			return false;
		}
		$fnname = $this->installHook;
		
		if (!function_exists($fnname)) {
			return false;
		}
				
		$evalCode = $fnname."(\$this);";
		return eval($evalCode);
	}
	
	
	
	function loadConfig($pathConfig) {
		
		$configXML = simplexml_load_file($pathConfig); 
		// TODO: Check
		//if (!$xml) return false;
		
		// TODO: ...
		$this->config = $configXML->config;
		
		if (property_exists($configXML, "install")) {
			$this->configInstall = $configXML->install;
			
			foreach ($this->configInstall->param as $param) {
				$attributes = $param->attributes();
				$name = (string)$attributes["name"];
				$value = (string)$attributes["value"];
	
				$this->{$name} = $value;
			}
			foreach ($this->configInstall->dependency as $param) {
				$attributes = $param->attributes();
				$module = (string)$attributes["module"];
				$version = (string)$attributes["version"];
	
				$this->dependency[] = array(
					"module" => $module,
					"version" => $version,
				);
			}
			
			$files = array();
			foreach ($this->configInstall->file as $param) {
				$attributes = $param->attributes();
				$dir = (string)$attributes["dir"];
				$path = (string)$attributes["path"];
				
				$abs_path = $local_path = "";
				if ($dir === "abs") {
					$abs_path = ABS."/".$path;
					$local_path = $path;
				} elseif ($dir === "include") {
					$abs_path = ABS_INCLUDE."/".$path;
					$local_path = LOCAL_INCLUDE."/".$path;
				} elseif ($dir === "module") {
					$abs_path = ABS_MODULE."/".$path;
					$local_path = LOCAL_MODULE."/".$path;
				} elseif ($dir === "template") {
					$abs_path = ABS_TEMPLATE."/".$path;
					$local_path = LOCAL_TEMPLATE."/".$path;
				} elseif ($dir === "content") {
					$abs_path = UPLOAD_FILES_PATH."/".$path;
					$local_path = CONTENT_URL."/".$path;
				}
				
				// fix??
				if (substr($local_path, 0, 1) === "/") {
					$local_path = substr($local_path, 1);
				}
				
				$files[] = array(
					"dir" => $dir,
					"path" => $path,
					"abs_path" => $abs_path,
					"local_path" => $local_path,
					"url" => HOME_URL.$local_path,
				);
			}
			
			$this->files = $files;
		}
		
		
		if (property_exists($configXML, "locales")) {
			$this->configLocales = $configXML->locales;
			
			foreach ($this->configLocales as $listLocales) {
				
				foreach ($listLocales->locale as $locale) {
					$attributes = $locale->attributes();
					$nameLocale = (string)$attributes["name"];
					$listLocale = array();
					
					// skip loading unnecessary languages
					if ($nameLocale !== LANG) {
						continue;
					}

					foreach ($locale->field as $field) {
						$attributes = $field->attributes();
						$name = (string)$attributes["name"];
						$value = (string)$attributes["value"];
						
						$listLocale[$name] = $value;
					}
					$this->locales = array_merge($this->locales, $listLocale);
				}
			}
		}

		foreach ($this->config->constant as $constant) {
			$attributes = $constant->attributes();
			$name = (string)$attributes["name"];
			$value = (string)$attributes["value"];
			
			$this->constants[$name] = $value;
			
			// define constant for internal use
			define(strtoupper($name), $value); 
		}
		
		foreach ($this->config->table as $table) {
			$attributes = $table->attributes();
			$nameTable = (string)$attributes["name"];
			$index = (string)$attributes["index"];
			$title = (string)$attributes["title"];
			$useseo = (bool)$attributes["useseo"];
			
			$fields = array();
			foreach ($table->field as $field) {
				$attributes = $field->attributes();
				$name = (string)$attributes["name"];
				$type = (string)$attributes["type"];
				$selectfrom = (string)$attributes["select-from"];
				$listskip = (string)$attributes["listskip"];

				$fieldAr = array(
					"name" => $name,
					"type" => $type,
				);
				
				if (!empty($selectfrom)) {
					list($source, $sourceName) = explode(":", $selectfrom);
					
					
					// TODO: 
					// type 'select' conflict - api:create table, ???
					// without type 'select' - wrong default output
					
					//$fieldAr["type"] = "select";
					$fieldAr["select-from"] = array(
								"source" => $source,
								"name" => $sourceName,
							);
				}
				///// ----
				if (!empty($listskip)) {
					$fieldAr["listskip"] = (int)$listskip;
				}
				
				if ($type === "pic") {
					$max_height = (string)$attributes["max-height"];
					$max_width = (string)$attributes["max-width"];
					
					if (!empty($max_height)) {
						$fieldAr["max-height"] = $max_height;
					}
					if (!empty($max_width)) {
						$fieldAr["max-width"] = $max_width;
					}
				}
				
				$fields[$name] = $fieldAr;
			}
			
			$this->tables[$nameTable] = array(
				"index" => $index,
				"title" => $title,
				"name" => $nameTable,
				"useseo" => $useseo,
				"fields" => $fields,
			);
		}
		foreach ($this->config->filter as $filter) {
			$attributes = $filter->attributes();
			
			$action = (string)$attributes["action"];
			$url = (string)$attributes["url"];
			$setpage = (string)$attributes["setpage"];
			
			$this->filters[] = array(
				"module" => $this->name,
				"action" => $action,
				"url" => $url,
				"setpage" => $setpage,
			);
		}
		
		foreach ($this->config->api as $filter) {
			$attributes = $filter->attributes();
			
			$action = (string)$attributes["action"];
			$name = (string)$attributes["name"];
			
			$this->api[] = array(
				"module" => $this->name,
				"name" => $name,
				"action" => $action
			);
		}
		
		foreach ($this->config->param as $param) {
			$attributes = $param->attributes();
			$name = (string)$attributes["name"];
			$value = (string)$attributes["value"];

			$this->{$name} = $value;
			$this->config[$name] = $value;
		}
		
	}
	

}
