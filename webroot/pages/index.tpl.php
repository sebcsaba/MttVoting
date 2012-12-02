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
				<li><a href="#"><?h($voting->getTitle())?></a></li>
			<? } ?>
			<? if (is_empty($request->getData('answerableFor'))) { ?>
				<li>(Nincs ilyen szavazás)</li>
			<? } ?>
		</ul>
	</div>
</body>
</html>
