<?php
//No direct to access this file
defined('_JEXEC') or die('Restricted access');

$plugin    = JPluginHelper::getPlugin('editors-xtd', '123rfbutton');
$plgParams = new JRegistry($plugin->params);
$localSave = $plgParams->get('local_save', 'images/123rf');

$link    = $_POST['link'];
$content = file_get_contents($link);
$XML     = new SimpleXMLElement($content);
$url     = $XML->download->url;

function getImage($url)
{
    $headers[]  = 'Accept; image/gif, image/x-bitmap, image/jpeg, image/pjeg';
    $headers[]  = 'Connection: Keep-Alive';
    $headers[]  = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
    $user_agent = 'php';
    $process    = curl_init($url);
    curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($process, CURLOPT_TIMEOUT, 30);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);

    $return = curl_exec($process);
    curl_close($process);
    return $return;
}

$imagename = basename(strtoupper($_POST['format']) . '_' . $_POST['itemid'] . '(' . $_POST['width'] . 'x' . $_POST['height'] . 'px).' . $_POST['format']);

if (!JFolder::exists(JPATH_ROOT . '/' . $localSave)) {
    JFolder::create(JPATH_ROOT . '/' . $localSave);
}

$image = getImage($url);

file_put_contents(JPATH_ROOT . '/' . $localSave . '/' . $imagename, $image);

if ($image) {
    $image_source = JUri::root() . $localSave . '/' . $imagename;
    echo $image_source;
} else {
    echo 'error';
}
