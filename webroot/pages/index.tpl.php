<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-hu" lang="hu-hu">
<head>
	<title>MTT vezetőségi szavazás</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="robots" content="noindex, nofollow" />
	<meta name="keywords" content="Tolkien, Gyűrűk Ura, MTT" />
	<meta name="description" content="MTT vezetőségi szavazás" />
	<link href="http://www.tolkien.hu/templates/tolkienhu/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href='http://www.tolkien.hu/modules/mod_events_cal/mod_events_cal1.5.css' rel='stylesheet' type='text/css' />
	<link rel="stylesheet" href="http://www.tolkien.hu/templates/system/css/system.css" type="text/css" />
	<link rel="stylesheet" href="http://www.tolkien.hu/templates/system/css/general.css" type="text/css" />
	<link rel="stylesheet" href="http://www.tolkien.hu/templates/tolkienhu/css/template.css" type="text/css" />
	<link rel="stylesheet" type="text/css" href="http://www.tolkien.hu/components/com_agora/style/Pyxes.css" />
<link rel="stylesheet" href="resources/style.css" />
<script type="text/javascript" src="resources/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="resources/privatevoting.js"></script>
</head>
<body>
	<div id="layout-header">
		<div id="layout-header-top-back">
			<div id="layout-header-top"></div>
		</div>
		<div id="position-header">
			<table id="header-table" cellspacing="0" cellpadding="0" onclick="document.location='/';">
				<tr>
					<td id="header-left-cell">
						<div id="header-left-cell-r">&nbsp;</div></td>
					<td id="header-middle-cell" style="">
						<div id="header-middle-cell-caption" style="background-image: url(http://www.tolkien.hu/templates/tolkienhu/images/headers/1.jpg); background-position: 444px 126px;"></div></td>
					<td id="header-right-cell">&nbsp;</td>
				</tr>
			</table>
		</div>
		<div id="layout-header-bottom-back">
			<div id="layout-header-bottom"></div>
		</div>
	</div>

	<table id="layout-table" cellspacing="0" cellpadding="0">
		<tr id="layout-table-content-row">
			<td id="layout-left-deadspace"><div id="layout-left-deadspace-top">&nbsp;</div></td>

			<td id="layout-column-left">
				<div id="layout-column-left-top"></div>

				<div id="position-category">
					<div id="pc-both">
						<div class="moduletable">
							<ul id="mainlevel">
								<? foreach ($request->getData('answerableFor') as $voting) { ?>
									<li><a href="javascript:openPage('Voting',<?h($voting->getId())?>)"><?h($voting->getTitle())?></a></li>
								<? } ?>
								<li><a href="javascript:openPage('AllFor')">- Összes -</a></li>
							</ul>
						</div>

					</div>
				</div>

				<div id="layout-column-left-ad-bottom"></div>
			</td>

			<td id="layout-column-center">
				<div id="position-content">
					<div id="thcm-functions"></div>
					<div id="thcm-category-highlights">
						<ul id="thcm-category-contents">
							<li id="central-content-for-privatevoting">
								<div class="thcm-fp-title">
									Szavazás használata
								</div>
								<div class="content-list-introtext">
									<p>
										Ez itt az MTT vezetősége számára készített szavazóprogram.
									</p>
									<p>
										A jobb oldali menüben tudsz szavazásokat létrehozni, a korábban létrehozottakat
										adminisztrálni.
									</p>
									<p>
										A bal oldalon látod a számodra elérhető aktív szavazásokat.
									</p>
								</div>
							</li>
						</ul>
					</div>
				</div></td>

			<td id="layout-column-right">
				<div id="login-box">
					<div id="position-authentication">
						<div class="moduletable">
							<div id="form-logout">
								<p>Üdv, <?h($request->getUser()->getLoginName())?>!</p>
							</div>
						</div>
					</div>
				</div>

				<div id="layout-rmenu">


					<div id="position-function">
						<div class="moduletable_menu">
							<ul class="menu">
								<li><a href="javascript:openPage('CreateNew')"><span>Új szavazás</span></a></li>
								<li><a href="javascript:openPage('ActivesOf')"><span>Nyitottak</span></a></li>
								<li><a href="javascript:openPage('AllOf')"><span>Összes</span></a></li>
							</ul>
						</div>
					</div>
				</div>

				<div id="layout-rmenu-bottom"></div>
			</td>
			
			<td id="layout-right-deadspace">
				<div id="layout-right-deadspace-top">&nbsp;</div>
			</td>
		</tr>
	</table>

	<div id="layout-footer-holder">
		<div id="layout-footer">
			<div id="layout-footer-c"></div>
		</div>
	</div>
</body>
</html>