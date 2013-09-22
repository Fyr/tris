<?
require_once('inifile.class.php');

$ini = new IniFile('../../snippets.ini');
$aSnippets = $ini->getIniDataArray();
?>
<div class="mercury-snippet-panel">
  <ul>
<?
foreach ($aSnippets as $id => $info) {
?>
    <li data-filter="<?=$id.', '.$info['title']?>">
      <img alt="<?=$info['title']?>" data-snippet="<?=$id?>" <?=(isset($info['options']) && !$info['options']) ? 'data-options="false"' : ''?> src="<?=$info['img']?>"/>
      <h4><?=$info['title']?></h4>
      <div class="description"><?=$info['description']?></div>
    </li>
<?
}
?>
	<li></li>
  </ul>
</div>