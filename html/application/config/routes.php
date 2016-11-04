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

/*--------------------------------------
 * Rotas do controller Admins.php
 *--------------------------------------*/
$route['admins/add_admin'] = 'admins/add_admin';
$route['admins/info'] = 'admins/info';
$route['admins/(:any)'] = 'admins/admins/$1';
$route['admins'] = 'admins/admins';
/*--------------------------------------
 * Rotas do controller Autenticador.php 
 *--------------------------------------*/
$route['autenticador'] = 'paginas/view';
/*---------------------------------------
 * Rotas do controller Usuarios.php 
 *--------------------------------------*/
$route['usuarios/(:any)'] = 'usuarios/usuarios/$1';
$route['usuarios'] = 'usuarios/usuarios';
/*---------------------------------------
 * Rotas do controller Regras.php 
 *--------------------------------------*/
$route['regras'] = 'regras/regras';
/*---------------------------------------
 * Rotas do controller Configs.php
 *--------------------------------------*/
$route['configs'] = 'configs/all';

/*---------------------------------------
 * Rotas do controller Permissoes.php
 *--------------------------------------*/
$route['permissoes/(:any)'] = 'permissoes/permissoes/$1';
$route['permissoes'] = 'permissoes/permissoes';
/*---------------------------------------
 * Rotas do controller Pessoas.php 
 *--------------------------------------*/
$route['pessoas/(:any)'] = 'pessoas/pessoas/$1';
/*---------------------------------------
 * Rotas do controller Modulos.php 
 *--------------------------------------*/
$route['mod/(:any)'] = 'modulos/view/$1';
/*---------------------------------------
 * Rotas do controller Ajuda.php
 *--------------------------------------*/
$route['ajuda/list_manuais'] = 'ajuda/list_manuais';
$route['ajuda/(:any)'] = 'ajuda/view/$1';
/*---------------------------------------
 * Rotas do controller Paginas.php 
 *--------------------------------------*/
$route['(:any)'] = 'paginas/view/$1';
/*---------------------------------------
 * Rotas padrões
 *--------------------------------------*/
$route['default_controller'] = 'paginas/view';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
