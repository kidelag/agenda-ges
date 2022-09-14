<?php

use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

require_once  'vendor/autoload.php';
require_once 'config.php';

try {
    $client = new MyGes\Client('skolae-app', MYGES_USERNAME, MYGES_PASSWORD);
} catch(MyGes\Exceptions\BadCredentialsException $e) {
    die($e->getMessage()); // bad credentials
}

$me = new MyGes\Me($client);

$start = new DateTime();
$start->setTime(0, 0, 0);
$end = new DateTime();
$end->setTime(0, 0, 0);
$end = $end->add(DateInterval::createFromDateString('1 month'));

$agenda = $me->getAgenda($start->getTimestamp() * 1000, $end->getTimestamp() * 1000);

$calendar = new Calendar();

foreach($agenda as $a) {

    $start = new DateTime();
    $s = $start->setTimestamp($a->start_date / 1000);
    $end = new DateTime();
    $e = $end->setTimestamp($a->end_date / 1000);

    $address = '';
    if(isset($a->rooms[0]->name) && isset($a->rooms[0]->campus)) {
        $address = $a->rooms[0]->campus.' - '.$a->rooms[0]->name;
    }

    $calendar->event(Event::create($a->name)
        ->startsAt($s)
        ->endsAt($e)
        ->address($address)
        ->description($a->discipline->teacher)
    );
}

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="cal.ics"');
echo $calendar->get();
