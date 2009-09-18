<?
if($_POST['action'] == 'auth' && !empty($_POST['login']) && !empty($_POST['password'])) {
	require_once('config.php');
	require_once('DBWrapper.php');
	$db = new DBWrapper(SERVER, USERNAME, PASSWORD, DB_NAME);
	$user_data = $db->get_data_array_from_db("
		SELECT `Id`, `UniversityName`, `ScheduleId` FROM `Users`
		WHERE `Username`='??'
		AND `Password`=MD5('??')", array($_POST['login'], $_POST['password']));
	if(!empty($user_data)) {
		@session_start();
		$_SESSION['auth'] = $user_data[0];
		header('Location: ./');
	} else {
		$content = "Пользователь не найден!";
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" media="screen" href="main.css" />
        <title></title>
    </head>
	<body>
		<? if(!empty($content)) : ?>
			<div class="login_error">
				<?= $content ?>
			</div>
		<? endif; ?>
		<form method="POST" action="./auth.php">
			<input type="hidden" name="action" value="auth" />
			<fieldset class="form">
				<legend>Авторизация пользователей:</legend>
				<div class="field">
					<label for="login">Имя пользователя:</label>
					<input type="text" name="login" />
				</div>
				<div class="field">
					<label for="password">Пароль:</label>
					<input type="password" name="password" />
				</div>
				<input type="submit" value="Вход!" />
			</fieldset>
		</form>
	</body>
</html>
