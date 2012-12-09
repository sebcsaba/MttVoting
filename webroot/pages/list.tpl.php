<div class="thcm-fp-title"><?h($request->getData('title'))?></div>
<div class="content-list-introtext">
	<? foreach ($request->getData('votings') as $voting) { ?>
		<p><a href="javascript:openPage('<?h($request->getData('linksToPage'))?>',{id:<?h($voting->getId())?>})"><?h($voting->getTitle())?></a></p>
	<? } ?>
	<? if (is_empty($request->getData('votings'))) { ?>
		<p>(Nincs ilyen szavazás)</p>
	<? } ?>
</div>