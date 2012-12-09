<div class="thcm-fp-title"><?h($request->getData('voting')->getTitle())?></div>
<div class="content-list-introtext">
	<form id="voting" action="index.php?do=Vote" method="post">
		<input type="hidden" name="do" value="Vote"/>
		<input type="hidden" name="id" value="<?h($request->getData('voting')->getId())?>"/>
		<p><?h($request->getData('voting')->getDescription())?></p>
		<? foreach ($request->getData('voting')->getAnswers() as $id => $answer) { ?>
			<div class="answer">
				<input type="radio" name="answer_id" value="<?h($id)?>" id="answer_<?h($id)?>"/>
				<label for="answer_<?h($id)?>"><?h($answer)?></label>
			</div>
		<? } ?>
		<p class="important">
			<? if ($request->getData('voting')->getPrivate()) { ?>
				Ez egy privát szavazás, az eredmény nem fogja tartalmazni, hogy ki hogyan szavazott!
			<? } else { ?>
				Ez egy publikus szavazás, az eredmény tartalmazni fogja, hogy ki hogyan szavazott!
			<? } ?>
		</p>
		<input type="button" onclick="submitForm($('form#voting'))" value="Szavazás"/>
	</form>
</div>