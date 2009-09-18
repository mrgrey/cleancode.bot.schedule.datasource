<?
	@session_start();
	if(empty($_SESSION['auth'])) {
		header("Location: auth.php");
	}

function parse_field_value($field_name, $field_value) {
	$parse_array = array(
		'WeekType' => array('каждая', 'нечетная', 'четная') ,
		'DayOfWeek' => array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота')
	);

	if(!in_array($field_name, array_keys($parse_array))) {
		return $field_value;
	} else {
		return $parse_array[$field_name][$field_value];
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
		<?
		require_once('config.php');
		require_once('DBWrapper.php');
		$db = new DBWrapper(SERVER, USERNAME, PASSWORD, DB_NAME);

		if($_REQUEST['action'] == 'add' && isset($_SESSION['auth'])) {
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
					VALUES ('??', '??', '??', '??', '??', '??', '??', '??')",
					array(
						intval($_SESSION['auth']['ScheduleId']),
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
		} else if ($_REQUEST['action'] == 'delete' && isset($_SESSION['auth'])) {
			$record_id = intval($_REQUEST['record_id']);
			$schedule_id = intval($_SESSION['auth']['ScheduleId']);
			$db->send_query("DELETE FROM `Schedule` WHERE `Id`='??' AND `ScheduleId`='??'", array($record_id, $schedule_id));
		}
		?>
		<form method="POST" action="./index.php">
			<fieldset class="form">
				<legend>Новая запись в расписании</legend>
				<input type="hidden" name="action" value="add" />
				<div class="field">
					<label for="group_number">Номер группы:</label>
					<input type="text" name="group_number" value="<?= strip_tags($_REQUEST['group_number']) ?>" />
				</div>
				<div class="field">
					<label for="week_type">Тип недели:</label>
					<select name="week_type">
						<? $week_type_checked = intval($_REQUEST['group_number']); ?>
						<? for($i=0; $i<=2; $i++) : ?>
							<? $week_type_checked_text = ($i == $week_type_checked) ? ' selected' : ''; ?>
							<option value="<?= $i ?>" <?= $week_type_checked_text ?>><?= parse_field_value("WeekType", $i) ?></option>
						<? endfor; ?>
					</select>
				</div>
				<div class="field">
					<label for="day_of_week">День недели:</label>
					<select name="day_of_week">
						<? $day_of_week_checked = intval($_REQUEST['day_of_week']); ?>
						<? for($i=1; $i<=6; $i++) : ?>
							<? $day_of_week_checked =  ($i == $week_type_checked) ? ' selected' : ''; ?>
							<option value="<?= $i ?>" <?= $day_of_week_checked ?>><?= parse_field_value("DayOfWeek", $i) ?></option>
						<? endfor; ?>
						<option value="0"><?= parse_field_value("DayOfWeek", 0) ?></option>
					</select>
				</div>
				<div class="field">
					<label for="time">Время:</label>
					<input type="text" name="time" value="<?= strip_tags($_REQUEST['time']) ?>" />
				</div>
				<div class="field">
					<label for="place">Место:</label>
					<input type="text" name="place" value="<?= strip_tags($_REQUEST['place']) ?>" />
				</div>
				<div class="field">
					<label for="subject">Предмет:</label>
					<input type="text" name="subject" value="<?= strip_tags($_REQUEST['subject']) ?>" />
				</div>
				<div class="field">
					<label for="person">Преподаватель:</label>
					<input type="text" name="person" value="<?= strip_tags($_REQUEST['person']) ?>" />
				</div>
				<input type="submit" />
			</fieldset>
		</form>
		<div class="clear"></div>
		<table>
			<? 
			$schedule_id = intval($_SESSION['auth']['ScheduleId']);
			$allowed_keys = array(
				'GroupNumber' => 'Номер группы',
				'WeekType' => 'Тип недели',
				'DayOfWeek' => 'День недели',
				'Time' => 'Время',
				'Place' => 'Место',
				'Subject' => 'Предмет',
				'Person' => 'Преподаватель');

			?>
			<tr>
				<? foreach($allowed_keys as $key => $value) : ?>
					<th>
						<?= $value ?>
					</th>
				<? endforeach; ?>
			</tr>
			<?
			$data = $db->get_data_array_from_db("SELECT * FROM `Schedule` WHERE `ScheduleId`='??' ORDER BY `GroupNumber`,`DayOfWeek`", $schedule_id);
			foreach($data as $current_record) :
			?>
			<tr>
				<? foreach($current_record as $field_name => $field_data) : ?>
					<? if(in_array($field_name, array_keys($allowed_keys))) : ?>
						<td>
							<?= parse_field_value($field_name, $field_data) ?>
						</td>
					<? endif; ?>
				<? endforeach; ?>
				<td>
					<form method="POST" action="./index.php">
						<input type="hidden" name="action" value="delete" />
						<input type="hidden" name="record_id" value="<?= $current_record['Id'] ?>" />
						<input type="submit" value="Удалить" onclick="return confirm('Вы действительно хотите удалить запись?');" />
					</form>
				</td>
			</tr>
		<? endforeach; ?>
		</table>
    </body>
</html>
