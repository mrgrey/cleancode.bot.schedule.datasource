<?php
require_once('config.php');
require_once('DBWrapper.php');

$group_number = intval($_REQUEST['gr']);
$date = $_REQUEST['date'];

$db = new DBWrapper(SERVER, USERNAME, PASSWORD, DB_NAME);
if(empty($date) && empty($group_number)) {
	$data = array('status'=>'wronggroup');
} else {
	if(!empty($date)) {
		$date_array = explode('.', $date);
		$query_time = mktime(0, 0, 0,$date_array[1], $date_array[0], $date_array[2]);
	} else {
		$query_time = time();
	}
	$day_of_week = date('w', $query_time);
	$is_group_exists_result = $db->get_data_array_from_db("SELECT COUNT(*) FROM `Schedule` WHERE `GroupNumber` = '%s'", $group_number, MYSQL_NUM);
	if($is_group_exists_result[0][0] == 0) {
		$data = array('status'=>'wronggroup');
	} else {
		$first_september_day_of_week = date('w', mktime(0, 0, 0, 9, 1, 2009));
		$first_week_sunday_timestamp = date('U', mktime(0, 0, 0, 9, 1 - $first_september_day_of_week, 2009));

		$week_number = floor(($query_time - $first_week_sunday_timestamp) / 604800) + 1; //Magic number is 7*24*60*60
		$week_type = $week_number % 2 + 1;

		$db_data = $db->get_data_array_from_db(
			"SELECT * FROM `Schedule` WHERE
				`ScheduleId` = '%s'
				AND `GroupNumber` = '%s' 
				AND (
					`WeekType` = '%s'
					OR `WeekType` = '0'
					)
				AND `DayOfWeek` = '%s'",
			array(SCHEDULE_ID, $group_number, $week_type, $day_of_week));
		if(count($db_data) == 0) {
			$data = array(
				'status'=>'nolessons',
				'data'=>array(
					'week_number'=>$week_number,
					'day'=>(string)$day_of_week,
					'group'=>(string)$group_number
				)
			);
		} else {
			$data = array(
				'status'=>'success',
				'data'=>array(
					'week_number'=>$week_number,
					'day'=>(string)$day_of_week,
					'group'=>(string)$group_number
				)
			);
			foreach($db_data as $current_lesson) {
				$data['data']['lessons'][] = array(
					'time'=>$current_lesson['Time'],
					'place'=>$current_lesson['Place'],
					'subject'=>$current_lesson['Subject'],
					'person_name'=>$current_lesson['Person']
				);
			}
		}
	}
}
echo json_encode($data);
?>
