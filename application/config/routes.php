<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'v1/rest_server';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;

/*
| -------------------------------------------------------------------------
| REST API Routes
| -------------------------------------------------------------------------
*/
$route['v1/user/read/(:any)'] = 'v1/user/read/id/$1'; // defaulting to JSON (user/1)
$route['v1/user/read/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/user/read/id/$1/format/$3$4'; // get it in XML (read/1.xml)

$route['v1/access_user/read/(:any)'] = 'v1/access_user/read/id/$1'; // defaulting to JSON (read/1)
$route['v1/access_user/read/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/access_user/read/id/$1/format/$3$4'; // get it in XML (read/1.xml)

$route['v1/master/read_cvn_category/(:any)'] = 'v1/master/read_cvn_category/id/$1'; // defaulting to JSON (read/1)
$route['v1/master/read_cvn_category/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/master/read_cvn_category/id/$1/format/$3$4'; // get it in XML (read/1.xml)

$route['v1/master/read_cvn_size/(:any)'] = 'v1/master/read_cvn_size/id/$1'; // defaulting to JSON (read/1)
$route['v1/master/read_cvn_size/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/master/read_cvn_size/id/$1/format/$3$4'; // get it in XML (read/1.xml)

$route['v1/master/read_cvn_color/(:any)'] = 'v1/master/read_cvn_color/id/$1'; // defaulting to JSON (read/1)
$route['v1/master/read_cvn_color/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/master/read_cvn_color/id/$1/format/$3$4'; // get it in XML (read/1.xml)

$route['v1/master/read_cvn_material/(:any)'] = 'v1/master/read_cvn_material/id/$1'; // defaulting to JSON (read/1)
$route['v1/master/read_cvn_material/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/master/read_cvn_material/id/$1/format/$3$4'; // get it in XML (read/1.xml)

$route['v1/master/read_category_by_dept/(:any)'] = 'v1/master/read_category_by_dept/id/$1'; // defaulting to JSON (read/1)
$route['v1/master/read_category_by_dept/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/master/read_category_by_dept/id/$1/format/$3$4'; // get it in XML (read/1.xml)

$route['v1/master/read_size_by_dept/(:any)'] = 'v1/master/read_size_by_dept/id/$1'; // defaulting to JSON (read/1)
$route['v1/master/read_size_by_dept/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/master/read_size_by_dept/id/$1/format/$3$4'; // get it in XML (read/1.xml)

$route['v1/master/read_color_by_dept/(:any)'] = 'v1/master/read_color_by_dept/id/$1'; // defaulting to JSON (read/1)
$route['v1/master/read_color_by_dept/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/master/read_color_by_dept/id/$1/format/$3$4'; // get it in XML (read/1.xml)

$route['v1/master/read_material_by_dept/(:any)'] = 'v1/master/read_material_by_dept/id/$1'; // defaulting to JSON (read/1)
$route['v1/master/read_material_by_dept/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/master/read_material_by_dept/id/$1/format/$3$4'; // get it in XML (read/1.xml)

$route['v1/master/read_sku_by_brand/(:any)'] = 'v1/master/read_sku_by_brand/id/$1'; // defaulting to JSON (read/1)
$route['v1/master/read_sku_by_brand/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/master/read_sku_by_brand/id/$1/format/$3$4'; // get it in XML (read/1.xml)

$route['v1/master/read_discount_by_sku/(:any)'] = 'v1/master/read_discount_by_sku/id/$1'; // defaulting to JSON (read/1)
$route['v1/master/read_discount_by_sku/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/master/read_discount_by_sku/id/$1/format/$3$4'; // get it in XML (read/1.xml)

//BRAND
$route['v1/brand/read_by_client/(:any)'] = 'v1/brand/read_by_client/id/$1'; // defaulting to JSON (read/1)
$route['v1/brand/read_by_client/(:any)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'v1/brand/read_by_client/id/$1/format/$3$4'; // get it in XML (read/1.xml)
