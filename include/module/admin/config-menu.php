<?php


$menu_ar = array();
$menu_index = array();


$submenu = array();
$submenu["realtime"] = array(
	"name" => _t("admin.realtime"), 
	"access" => "admin",
	"url" => "/admin/?section=realtime",
	"file" => "index.tpl",
	"title" => _t("admin.realtime"), 
);
$submenu["analytics"] = array(
	"name" => _t("admin.analytics"), 
	"access" => "admin",
	"url" => "/admin/?section=analytics",
	"file" => "analytics.tpl",
	"title" => _t("admin.analytics")
);
$submenu["social"] = array(
	"name" => _t("admin.social"), 
	"access" => "admin",
	"url" => "/admin/?section=social",
	"file" => "social.tpl",
	"title" => _t("admin.social"),
);
$submenu["manual"] = array(
	"name" => _t("admin.manual"), 
	"access" => "admin",
	"handler" => "module-manual.php",
	"url" => "/admin/?section=manual",
	"file" => "manual.tpl",
	"title" => _t("admin.manual")
);
$menu_index = array_merge($menu_index, array_keys($submenu));

$menu_ar["index"] = array(
	"name" => _t("admin.homepage"), 
	"access" => "admin",
	"file" => "index.tpl",
	"title" => _t("admin.homepage_title"),
	"submenu" => $submenu
);





$submenu = array();
$submenu["structure"] = array(
	"name" => _t("admin.structure_manage"), 
	"access" => "admin",
	"handler" => "module-structure.php",
	"table" => TABLE_STRUCTURE,
	"url" => "/admin/?section=structure",
	"file" => "structure.tpl",
	"title" => _t("admin.structure_title")
);
$submenu["menu"] = array(
	"name" => _t("admin.menu"), 
	"access" => "admin",
	"handler" => "module-table.php",
	"table" => TABLE_MENU,
	"url" => "/admin/?section=menu",
	"file" => "menu.tpl",
	"title" => _t("admin.menu_title")
);
$submenu["documents"] = array(
	"name" => _t("admin.documents"), 
	"access" => "admin",
	"handler" => "module-table.php",
	"table" => TABLE_DOCUMENTS,
	"url" => "/admin/?section=documents",
	"file" => "documents.tpl",
	"title" => _t("admin.documents_title")
);
$submenu["seo"] = array(
	"name" => _t("admin.seo"), 
	"access" => "admin",
	"handler" => "module-table.php",
	"table" => TABLE_SEO,
	"url" => "/admin/?section=seo",
	"file" => "seo.tpl",
	"title" => _t("admin.seo_title")
);
$menu_index = array_merge($menu_index, array_keys($submenu));


$menu_ar["structure_docs"] = array(
	"name" => _t("admin.structure"), 
	"access" => "admin",
	"file" => "index.tpl",
	"title" => _t("admin.homepage_title"),
	"submenu" => $submenu
);
$menu_ar["users"] = array(
	"name" => _t("admin.users"), 
	"access" => "admin",
	"handler" => "module-table.php",
	"table" => TABLE_USERS,
	"url" => "/admin/?section=users",
	"file" => "users.tpl",
	"title" => _t("admin.users_title")
);

$menu_ar["media_library"] = array(
	"name" =>  _t("admin.media_library"), 
	"access" => "admin",
	"handler" => "module-media.php",
	"url" => "/admin/?section=media_library",
	"file" => "media_library.tpl",
	"title" =>  _t("admin.media_library")
);


foreach ($this->website->modules as $module) {
	$module = MSV_get("website.".$module);
	if (!empty($module->adminMenu) && $module->adminMenu) {
		
		$submenu = array();

		foreach ($module->tables as $tableInfo) {
			$name = $tableInfo["name"];
			
			$submenu[$name] = array(
				"name" => _t("table.".$name), 
				"table" => $name, 
				"access" => "admin",
				"handler" => "module-table.php",
				"url" => "/admin/?section=".$module->name."&table=".$name,
				"file" => "custom.tpl",
				"title" => _t("table.".$name)." - ".$name
			);
		}
		$menu_ar[$module->name] = array(
			"name" => _t("module.".$module->name), 
			"access" => "admin",
			"handler" => "module-table.php",
			"url" => "/admin/?section=".$module->name,
			"file" => "custom.tpl",
			"title" => $module->description,
			"submenu" => $submenu
		);
		
		$menu_index = array_merge($menu_index, array_keys($submenu));
	}
}




$menu_ar["mail_template"] = array(
	"name" => _t("admin.mail_template"), 
	"access" => "admin",
	"handler" => "module-table.php",
	"table" => TABLE_MAIL_TEMPLATES,
	"url" => "/admin/?section=mail_template",
	"file" => "mail_template.tpl",
	"title" => _t("admin.mail_template_title")
);


$menu_ar["module_settings"] = array(
	"name" => _t("admin.module_settings"), 
	"access" => "superadmin",
	"url" => "/admin/?section=module_settings",
	"file" => "module_settings.tpl",
	"title" => _t("admin.module_settings_title")
);



$submenu = array();
$submenu["site_settings"] = array(
	"name" => _t("admin.site_settings"), 
	"access" => "admin",
	"handler" => "module-table.php",
	"table" => TABLE_SETTINGS,
	"url" => "/admin/?section=site_settings",
	"file" => "site_settings.tpl",
	"title" => _t("admin.site_settings_title")
);
$submenu["locales"] = array(
	"name" => _t("admin.locales"), 
	"access" => "admin",
	"handler" => "module-locales.php",
	"url" => "/admin/?section=locales",
	"file" => "locales.tpl",
	"title" => _t("admin.locales_title")
);
$submenu["robots"] = array(
	"name" => _t("admin.robots"), 
	"access" => "admin",
	"handler" => "module-robots.php",
	"url" => "/admin/?section=robots",
	"file" => "robots.tpl",
	"title" => _t("admin.robots_title")
);
$submenu["sitemap"] = array(
	"name" => _t("admin.sitemap"), 
	"access" => "superadmin",
	"handler" => "module-sitemap.php",
	"url" => "/admin/?section=sitemap",
	"file" => "sitemap.tpl",
	"title" => _t("admin.sitemap_title")
);
$menu_index = array_merge($menu_index, array_keys($submenu));

$menu_ar["dev_tools"] = array(
	"name" => _t("admin.dev_tools"), 
	"access" => "admin",
	"title" => _t("admin.dev_tools"),
	"submenu" => $submenu
);




//$menu_ar["design"] = array(
//	"name" => _t("admin.design"), 
//	"access" => "superadmin",
//	"url" => "/admin/?section=design",
//	"file" => "design.tpl",
//	"title" => _t("admin.design_title")
//);
//$menu_ar["history"] = array(
//	"name" => _t("admin.history"), 
//	"access" => "superadmin",
//	"url" => "/admin/?section=history",
//	"file" => "history.tpl",
//	"title" => _t("admin.history_title")
//);


$menu_index = array_merge($menu_index, array_keys($menu_ar));


