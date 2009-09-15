<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
		require_once('config.php');
		require_once('DBWrapper.php');
		$db = new DBWrapper(SERVER, USERNAME, PASSWORD, DB_NAME);

		if($_REQUEST['action'] == 'add') {
			if(
				!empty($_REQUEST['group_number']) &&
				!empty($_REQUEST['week_type']) &&
				!empty($_REQUEST['day_of_week']) &&
				!empty($_REQUEST['time']) &&
				!empty($_REQUEST['place']) &&
				!empty($_REQUEST['subject']) &&
				!empty($_REQUEST['person'])
			) {

			}
		}
		//$db->send_query("SELECT * FROM `Schedule` WHERE `GroupNumber`='%s'", array(4512));
        $data = $db->get_data_array_from_db("SELECT * FROM `Schedule` WHERE `GroupNumber`='%s'", array(4512));
		print_r($data);
        ?>
    </body>
</html>
