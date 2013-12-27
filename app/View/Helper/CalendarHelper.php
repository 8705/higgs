<?php

class CalendarHelper extends AppHelper {
	public $helpers = array('Html');
	public function make($id, $date, $task) {
		$yyyy = date("Y", mktime(0, 0, 0, $date['month'], $date['day'], $date['year']));
		$mm   = date("m", mktime(0, 0, 0, $date['month'], $date['day'], $date['year']));
		$dd   = date("d", mktime(0, 0, 0, $date['month'], $date['day'], $date['year']));

		$prev = date("Y-m-d", mktime(0, 0, 0, $date['month']-1, $date['day'], $date['year']));
		$next = date("Y-m-d", mktime(0, 0, 0, $date['month']+1, $date['day'], $date['year']));

		$cal  = "<table id=\"taskcalendar\" class=\"table calendar\" data-cal-month=\"".h($yyyy)."\">";
		$cal .= "<tr>";
		$cal .=
			"<th>".
				$this->Html->link(__('←'), array('action' => $this->action, '?'=>array('date'=>$prev, 'task_id'=>$id)))
			."</th>";
		$cal .= "<th colspan=\"5\">".h($yyyy)."年".h($mm)."月</th>";
		$cal .=
			"<th>".
				$this->Html->link(__('→'), array('action' => $this->action, '?'=>array('date'=>$next, 'task_id'=>$id)))
			."</th>";
		$cal .= "</tr>";
		$cal .= "<tr><th>日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th></tr>";

		$wd1   = date("w", mktime(0, 0, 0, $mm, 1, $yyyy));
		$lastd = date("d", mktime(0, 0, 0, $mm + 1, 0, $yyyy));
		$d     = 0;

		for ($i = 0; $i <= 5; $i++) {
			if ($d >= $lastd) {break;}
			$cal .= "<tr>";
			for ($j = 0; $j <= 6; $j++) {
				$d = $i * 7 + $j - $wd1 + 1;
				$isToday = '';
				$active_day = h($yyyy)."-".h($mm)."-".$d;
				$today = date('Y-n-j');
				if($j == 0) $isToday = 'sunday';
				if($j == 6) $isToday = 'saturday';
				if($today == $active_day)$isToday= 'today';
				if ($d > $lastd or $d  < 1) {
					$cal .= "<td class=\"" .$isToday. "\" data-cal-date=\"".h($active_day)."\"></td>";
				} else {
					$cal .= "<td class=\"connected ".$isToday."\" data-cal-date=\"".h($active_day)."\">$d";
					if(array_key_exists((int)$yyyy, $task)) {
						if(array_key_exists((int)$mm, $task[(int)$yyyy])) {
							if(array_key_exists($d, $task[(int)$yyyy][(int)$mm])) {
								foreach($task[(int)$yyyy][(int)$mm][$d] as $t) {
									$cal .= "<p class=\"calendartask ".h($t['status'])."\" id=\"task_".h($t['id'])."\" data-task-id=\"".h($t['id'])."\">".h($t['body'])."</p>";
								}
							}
						}
					}
					$cal .= "</td>";
				}
			}
			$cal .= "</tr>";
		}
		$cal .= "</table>";
		echo $cal;
	}
}