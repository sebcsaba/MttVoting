<div class="thcm-fp-title"><?h($request->getData('voting')->getTitle())?></div>
<div class="content-list-introtext">
	<p><?h($request->getData('voting')->getDescription())?></p>
	<? foreach ($request->getData('result') as $result) { ?>
		<div class="answer">
			<?h($result['title'])?>:
			<?h($result['cnt'])?>
		</div>
	<? } ?>
</div>