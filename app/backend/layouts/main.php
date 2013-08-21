<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title><?= !empty($this->pageTitle) ? $this->pageTitle : '#CodeName#';  ?></title>
	
	<link rel="stylesheet" type="text/css" href="/assets/backend/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="/assets/backend/css/jquery-ui-1.10.0.custom.css">
	<link rel="stylesheet" type="text/css" href="/assets/backend/css/admin.css">
	<link rel="stylesheet" type="text/css" href="/assets/backend/css/chosen.css">
	
	
	<script src="/assets/backend/js/jquery-1.9.1.min.js"></script>
	<script src="/assets/backend/js/bootstrap.js"></script>
	<script src="/assets/backend/js/jquery-ui-1.10.0.custom.min.js"></script>
	<script src="/assets/backend/js/jquery.mousewheel.min.js"></script>
	
	<script src="/assets/backend/js/qqfileuploader.js"></script>
	
	<script src="/assets/backend/js/chosen.jquery.js"></script>
	<script src="/assets/backend/js/jquery.mCustomScrollbar.min.js"></script>
	
	<script src="/assets/backend/js/backend.js"></script>

</head>
<body>




<div class="movepage"><i class="icon-arrow-up"></i></div>
<div class="navbar navbar-inverse">
	<div class="navbar-inner">
		<div class="container">
			
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</a>
			<a class="brand bootsnippbrand" href="/admin">#CodeName#</a>
			
			<div class="nav-collapse collapse">	
				<ul class="nav">
					<li class="divider-vertical"></li>
					
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Контент блоки <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li class="dropdown-submenu">
								<a href="/admin/cblock">Список блоков</a>
								<ul class="dropdown-menu">
									<li><a href="/admin/cblock/add">Добавить</a></li>	
								</ul>
							</li>
						</ul>
					</li>		
					
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Разделы сайта <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li class="dropdown-submenu">
								<a href="/admin/contents">Список страниц</a>
								<ul class="dropdown-menu">
									<li><a href="/admin/contents/add">Добавить</a></li>	
								</ul>
							</li>
						</ul>
					</li>
					
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"> Общение<b class="caret"></b></a>
					</li>
					
					<li>
						<a href="/admin/crud" > Конструкторы</a>
					</li>

				</ul>
			</div>
			
		</div>
	</div>
</div>









	<?=$content?>
</body>
</html>