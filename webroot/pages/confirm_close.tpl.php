<div class="thcm-fp-title">Szavazás lezárása</div>
<div class="content-list-introtext">
	<p class="important"><?h($request->getData('voting')->getTitle())?></p>
	<p><?h($request->getData('voting')->getDescription())?></p>
	<form id="edit_close" action="index.php" method="post" class="voteform">
		<input type="hidden" name="do" value="Close"/>
		<input type="hidden" name="id" value="<?h($request->getData('voting')->getId())?>"/>
		<p>Biztosan le akarod zárni ezt a szavazást?</p>
		<input type="button" onclick="submitForm($('form#edit_close'));" value="Igen, biztosan lezárom"/>
	</form>
	<p><input type="button" onclick="openPage('EditVoting',{id:<?h($request->getData('voting')->getId())?>})" value="Nem, inkább mégsem zárom le"/></p>
</div>