<?php
require 'vendor/autoload.php';

use Respect\Validation\Validator as v;

// $dotenv = new Dotenv\Dotenv(__DIR__);
// $dotenv->load();

// $access_key = getenv('ACCESS_KEY');
// $secret_key = getenv('SECRET_KEY');
// $associate_tag = getenv('ASSOCIATE_TAG');
// $app_title = getenv("APP_TITLE");

$access_key = "AKIAJK53EZUJXECHQB6Q";
$secret_key = "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";
$associate_tag = 'assocs00-22';
$app_title = "NetGuru amz discount";


$amazon = new Lib\Amazon($access_key, $secret_key, $associate_tag);

$search_indices = $amazon->getSearchIndices();

$items = [];
$keyword = '';
$search_index = '';
$has_searched = false;

if (!empty($_GET['keyword'])) {
  $keyword = $_GET['keyword'];
  $search_index = $_GET['search_index'];
  $has_searched = true;

  $keyword_validator = v::alnum()
    ->noWhitespace()
    ->length(3, 80);
  if ($keyword_validator->validate($_GET['keyword']) && in_array($search_index, $search_indices)) {
    $response = $amazon->itemSearch($keyword, $search_index);
    $items = $response->Items->Item;
  } 
}

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, [
  'cache' => 'cache',
  'auto_reload' => true
]);

$selected_filter = new Twig_SimpleFilter('selected', function ($string) {
  $exploded_string =  explode(',', $string);
  $selected_value = $exploded_string[0];
  $current_value = $exploded_string[1];
  return ($selected_value == $current_value) ? 'selected' : '';
});

$twig->addFilter($selected_filter);

$template = $twig->loadTemplate('index.html');
echo $template->render([
  'app_title' => $app_title,
  'search_indices' => $search_indices,
  'keyword' => $keyword,
  'search_index' => $search_index,
  'items' => $items,
  'has_searched' => $has_searched
]);