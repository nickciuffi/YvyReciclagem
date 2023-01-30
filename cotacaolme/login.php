<html>
<head>
	<meta charset="UTF-8"/>
	<script type="text/javascript">
		function body_onload() {
			document.getElementById("upwd").focus();
		}
	</script>	
</head>
<body onload="javascript:body_onload();">
	<form method="POST" action="admin.php">
		<input type="hidden" name="action" id="action" value="login"/>
		<span>Área restrita</span>
		<input name="upwd" id="upwd" type="password" placeholder="[Digite a senha de acesso]"/>
		<input type="submit" value="Entrar" />
	</form>
</body>
</html>