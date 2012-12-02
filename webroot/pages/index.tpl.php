<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>MTT vezetőségi szavazás</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="resources/style.css"/>
</head>
<body>
	<div id="logininfo">
		Üdv, <?h($request->getData('username'))?>!
	</div>
	<div id="answerable_for">
		Ezeken szavazhatsz:
		<ul>
			<? foreach ($request->getData('answerableFor') as $voting) { ?>
				<li><a href="#/view?id=<?h($voting->getId())?>"><?h($voting->getTitle())?></a></li>
			<? } ?>
			<? if (is_empty($request->getData('answerableFor'))) { ?>
				<li>(Nincs ilyen szavazás)</li>
			<? } ?>
		</ul>
		<a href="#/getAllFor">Mutas az összes szavazást, amin részt vettem.</a>
	</div>
	<div id="opened_by">
		Ezeket a nyitottakat hoztad létre:
		<ul>
			<? foreach ($request->getData('openedOf') as $voting) { ?>
				<li><a href="#/edit?id=<?h($voting->getId())?>"><?h($voting->getTitle())?></a></li>
			<? } ?>
			<? if (is_empty($request->getData('openedOf'))) { ?>
				<li>(Nincs ilyen szavazás)</li>
			<? } ?>
		</ul>
		<a href="#/getAllOf">Mutas az összes szavazást, amit nyitottam.</a>
	</div>
	<div id="footer">
		Ha bármi összedőlt vagy nem működik, kérdezd <a href="https://facebook.com/sebcsaba">sebcsabát</a>!
	</div>
</body>
</html>
