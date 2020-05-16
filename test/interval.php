<?php

$now = new DateTime();
$end = new DateTime("2020-05-06T20:52:53+02:00");
//$end->add(new DateInterval('P1Y'));
$end->add(DateInterval::createFromDateString('+30 days'));

$interval = $now->diff($end);

echo $interval->format('%R%a days') . "\n";

echo date(DATE_ATOM, strtotime( $interval->format('%R%a days') , time() ) ) . "\n";

?>