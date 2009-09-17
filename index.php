<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" media="screen" href="main.css" />
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
				isset($_REQUEST['week_type']) &&
				isset($_REQUEST['day_of_week']) &&
				!empty($_REQUEST['subject'])
			) {
				$db->send_query(
					"INSERT INTO `Schedule`
						(`ScheduleId`,
						`GroupNumber`,
						`WeekType`,
						`DayOfWeek`,
						`Time`,
						`Place`,
						`Subject`,
						`Person`)
					VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					array(
						SCHEDULE_ID,
						intval($_REQUEST['group_number']),
						intval($_REQUEST['week_type']),
						intval($_REQUEST['day_of_week']),
						$_REQUEST['time'],
						$_REQUEST['place'],
						$_REQUEST['subject'],
						$_REQUEST['person']
						)
					);
			}
		}
        ?>
		<form method="POST" action="./index.php">
			<fieldset class="form">
				<legend>Новая запись в расписании</legend>
				<input type="hidden" name="action" value="add" />
				<div class="field">
					<label for="group_number">Номер группы:</label>
					<input type="text" name="group_number" />
				</div>
				<div class="field">
					<label for="week_type">Тип недели:</label>
					<select name="week_type">
						<option value="0">каждая</option>
						<option value="1">нечетная</option>
						<option value="2">четная</option>
					</select>
				</div>
				<div class="field">
					<label for="day_of_week">День недели:</label>
					<select name="day_of_week">
						<option value="1">понедельник</option>
						<option value="2">вторник</option>
						<option value="2">среда</option>
						<option value="2">четверг</option>
						<option value="2">пятница</option>
						<option value="2">суббота</option>
						<option value="0">воскресенье</option>
					</select>
				</div>
				<div class="field">
					<label for="time">Время:</label>
					<input type="text" name="time" />
				</div>
				<div class="field">
					<label for="place">Место:</label>
					<input type="text" name="place" />
				</div>
				<div class="field">
					<label for="subject">Предмет:</label>
					<input type="text" name="subject" />
				</div>
				<div class="field">
					<label for="person">Преподаватель:</label>
					<input type="text" name="person" />
				</div>
				<input type="submit" />
			</fieldset>
		</form>
		<div class="clear"></div>
		<table>
			<? $allowed_keys = array('GroupNumber', 'WeekType', 'DayOfWeek', 'Time', 'Place', 'Subject', 'Person'); ?>
			<tr>
				<? foreach($allowed_keys as $key) : ?>
					<th>
						<?= $key ?>
					</th>
				<? endforeach; ?>
			</tr>
			<?
			$data = $db->get_data_array_from_db("SELECT * FROM `Schedule` WHERE `ScheduleId`='%s' ORDER BY `GroupNumber`,`DayOfWeek`", SCHEDULE_ID);
			foreach($data as $current_record) :
			?>
			<tr>
				<? 
				foreach($current_record as $field_name => $field_data) :
					if(in_array($field_name, $allowed_keys)) :
				?>
					<td>
						<?= $field_data; ?>
					</td>
				<?
					endif;
				endforeach;
				?>
			</tr>
		<? endforeach; ?>
		</table>
    </body>
</html>
