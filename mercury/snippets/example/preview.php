<?
	$edit = isset($_POST['edit']) && $_POST['edit'];
	/*
	if ($edit) {
?>
<script type="text/javascript">
activateEditable();
</script>
<?
	}*/
?>
<div style="width: 400px; border: 1px solid red;">
<h4>Test "Example" Snippet</h4>
<a class="btn btn-large" href="javascript:void(0)">Uneditable button: <?=@$_POST['first_name']?></a><br/>
<a class="btn btn-large <?=@$_POST['btn_type']?>" href="javascript:void(0)">Editable button: <span <? if ($edit) {?>contenteditable="true" onchange="onChangeEditable(this, 'editable')"<? } ?>><?=@$_POST['editable']?></span></a>
</div>