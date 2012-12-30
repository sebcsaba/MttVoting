<div class="thcm-fp-title">Szavazás törlése</div>
<div class="content-list-introtext">
	<p class="important"><?h($request->getData('voting')->getTitle())?></p>
	<p><?h($request->getData('voting')->getDescription())?></p>
	<form id="edit_delete" action="index.php" method="post" class="voteform">
		<input type="hidden" name="do" value="Delete"/>
		<input type="hidden" name="id" value="<?h($request->getData('voting')->getId())?>"/>
		<p>Biztosan törölni akarod ezt a szavazást?</p>
		<input type="button" onclick="submitForm($('form#edit_delete'));" value="Igen, biztosan törlöm"/>
	</form>
	<p><input type="button" onclick="openPage('EditVoting',{id:<?h($request->getData('voting')->getId())?>})" value="Nem, inkább mégsem törlöm"/></p>
</div>