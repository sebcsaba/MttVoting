<div class="thcm-fp-title">Új szavazás</div>
<div class="content-list-introtext">
	<form id="edit_new" action="index.php?do=Vote" method="post">
		<input type="hidden" name="do" value="SaveNew"/>
		<table>
			<tr>
				<td class="label">Cím:</td>
				<td><input type="text" name="title" class="full"/></td>
			</tr>
			<tr>
				<td class="label">Leírás:</td>
				<td><textarea class="full"></textarea></td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="answer">
						<input type="radio" name="private" value="0" id="private_0"/>
						<label for="private_0">Ez egy publikus szavazás, az eredmény
							tartalmazni fogja, hogy ki hogyan szavazott.</label>
					</div>
					<div class="answer">
						<input type="radio" name="private" value="1" id="private_1"/>
						<label for="private_1">Ez egy privát szavazás, az eredmény
							nem fogja tartalmazni, hogy ki hogyan szavazott.</label>
					</div>
				</td>
			</tr>
			<tr>
				<td class="label">Lehetséges válaszok:</td>
				<td>
					<input type="text" name="answer_pt" id="answer_prototype" class="full" onfocus="addNewAnswerField();"/>
				</td>
			</tr>
		</table>
		<input type="button" onclick="submitForm($('form#edit_new'))" value="Mentés"/>
	</form>
</div>