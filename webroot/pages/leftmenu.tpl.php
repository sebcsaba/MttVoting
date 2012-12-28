<ul id="mainlevel">
	<? foreach ($request->getData('interestingFor') as $voting) { ?>
		<li><a href="javascript:openPage('Voting',{id:<?h($voting->getId())?>})"><?h($voting->getTitle())?></a></li>
	<? } ?>
	<li><a href="javascript:openPage('AllFor')">- Összes -</a></li>
</ul>
