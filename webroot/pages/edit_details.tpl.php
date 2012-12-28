<div class="thcm-fp-title">Szavazás módosítása</div>
<div class="content-list-introtext">
	<form id="edit_detail" action="index.php" method="post" class="voteform">
		<input type="hidden" name="do" value="SaveDetails"/>
		<input type="hidden" name="id" value="<?h($request->getData('voting')->getId())?>"/>
		<table>
			<tr>
				<td class="label">Cím:</td>
				<td><input type="text" name="title" class="full" value="<?h($request->getData('voting')->getTitle())?>"/></td>
			</tr>
			<tr>
				<td class="label">Leírás:</td>
				<td><textarea class="full" name="description"><?h($request->getData('voting')->getDescription())?></textarea></td>
			</tr>
			<tr>
				<td class="label">Lehetséges válaszok:</td>
				<td>
					<ul>
						<? foreach ($request->getData('voting')->getAnswers() as $answer) { ?>
							<li><?h($answer)?></li>
						<? } ?>
					</ul>
					<input type="text" name="answer_pt" id="answer_prototype" class="full" onfocus="addNewAnswerField();"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<p>
						<? if ($request->getData('voting')->getPrivate()) { ?>
							Ez egy privát szavazás, az eredmény nem fogja tartalmazni, hogy ki hogyan szavazott.
						<? } else { ?>
							Ez egy publikus szavazás, az eredmény tartalmazni fogja, hogy ki hogyan szavazott.
						<? } ?>
						<span class="footnote">(Ezt a beállítást utólag nem lehet módosítani.)</span>
					</p>
				</td>
			</tr>
			<tr>
				<td class="label">
					Résztvevők:
					<span class="footnote">
						(Ha legalább 3 karaktert megadsz, megjelenik egy lista amiből válaszhatsz.)
					</span>
				</td>
				<td>
					<ul>
						<? foreach ($request->getData('voting')->getParticipants() as $participant) { ?>
							<li><?h($participant->getUser()->getLoginName())?></li>
						<? } ?>
						<li id="participant_prototype">
							<span>Participant name</span>
							<input type="hidden"/>
							<i class="delete_icon" onclick="onDeleteNewParticipant(this);"/>
						</li>
					</ul>
					<input type="text" id="participant_search" class="full"/>
					<script type="text/javascript">participantSearchInit();</script>
				</td>
			</tr>
		</table>
		<input type="button" onclick="submitForm($('form#edit_detail'));" value="Mentés"/>
	</form>
	<hr/>
	<form id="edit_close" action="index.php" method="post" class="voteform onebutton">
		<input type="hidden" name="do" value="Close"/>
		<input type="hidden" name="id" value="<?h($request->getData('voting')->getId())?>"/>
		<input type="button" onclick="if(confirm('Biztosan lezárod a szavazást?')){submitForm($('form#edit_close'));}" value="Lezárás"/>
	</form>
	<form id="edit_delete" action="index.php" method="post" class="voteform onebutton">
		<input type="hidden" name="do" value="ConfirmDelete"/>
		<input type="hidden" name="id" value="<?h($request->getData('voting')->getId())?>"/>
		<input type="button" onclick="submitForm($('form#edit_delete'));" value="Törlés"/>
	</form>
</div>
<? if ($request->getData('reload_leftmenu')) { ?>
	<script type="text/javascript">reloadLeftMenu();</script>
<? } ?>