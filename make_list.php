<?php

require_once __DIR__ . '/vendor/autoload.php';

// Функция для удаления элемента по 'id'
function arrInc(&$array, $look = 'id', $for = 0, $target = 'count', $mod = 1) {
    foreach ($array as &$item) {
        if ($item[ $look ] == $for) {
            $item[ $target ] += $mod;
            break;
        }
    }
}

function arrDec(&$array, $look = 'id', $for = 0, $target = 'count', $mod = 1) {
    foreach ($array as &$item) {
        if ($item[ $look ] == $for) {
            $item[ $target ] -= $mod;
            break;
        }
    }
}

function arrSet(&$array, $look = 'id', $for = 0, $target = 'count', $value = 1) {
    foreach ($array as &$item) {
        if ($item[ $look ] == $for) {
            $item[ $target ] = $value;
            break;
        }
    }
}

function arrGet($array, $look = 'id', $for = 0) {
    foreach ($array as $item) {
        if ($item[ $look ] == $for) {
            return $item;
        }
    }
    return [];
}

/**
 * Устанавливает (иди добавляет) значение в массив, ключ элемента которого равен искомому значению
 *
 * @param $array
 * @param $look
 * @param $for
 * @param $to
 * @param $value
 * @return void
 */
function arrPut(&$array, $look = 'id', $for = 0, $to = 'letters', $value = null) {
    foreach ($array as &$item) {
        if ($item[ $look ] == $for) {
            if (is_array($item[ $to ])) {
                $item[ $to ][] = $value;
            } else {
                $item[ $to ] = $value;
            }
        }
    }
}

function shuffle_assoc($list) {
    if (!is_array($list)) return $list;
    $keys = array_keys($list);

    shuffle($keys);

    $random = array();

    foreach ($keys as $key){
        $random[$key] = $list[$key];
    }

    return $random;
}

function shuffle_forced(array $array):array {
    $n = count($array);
    if ($n <= 1) {
        return []; // Дерандж невозможен для пустого массива или массива из одного элемента
    }

    $dest_array = $source_array = $array;

    do {
        // Перемешиваем массив случайным образом
        shuffle($dest_array);
        $isDeranged = true;

        foreach ($source_array as $key => $value) {
            if ($dest_array[$key] === $source_array[$key]) {
                $isDeranged = false;
                break;
            }
        }

    } while (!$isDeranged);

    return $dest_array;
}

$json = file_get_contents(__DIR__ . '/members.json');

$recipients_list = [];

$senders = [];
$max_cards = 0;

foreach (json_decode($json, true) as $s) {
    $max_cards = max($max_cards, $s['cards_count']);

    $id = $s['id'];

    $senders[] = ['will_recieve' => 0, 'will_send' => $s['cards_count'], ...$s ];

    $senders_list[] = [
        'id'        =>  $s['id'],
        'fio'       =>  $s['fio'],
        'email'     =>  $s['email'],
        'letters'   =>  []
    ];
    $recipients_list[ $s['email'] ] = 0;

    /*$recipients_list[] = [
        'id'        =>  $s['id'],
        'fio'       =>  $s['fio'],
        'email'     =>  $s['email'],
        'letters'   =>  []
    ];*/

}

while (true) {
    $total_send_letters = 0;

    // clean datasets
    foreach ($senders_list as $i => $s) {
        $senders_list[$i]['letters'] = [];
    }
    foreach ($recipients_list as $i => $r) {
        $recipients_list[$i] = 0;
    }

    // iterate each sender
    foreach ($senders as $sender) {
        $possible_recipients = array_filter($senders, function ($item) use ($sender){
            return $item['will_recieve'] < 3;
        });

        $possible_recipients = array_filter($possible_recipients, function ($item) use ($sender){
            return $item['id'] !== $sender['id'];
        });

        $possible_recipients = shuffle_forced($senders);
        $possible_recipients = array_slice($possible_recipients, 0, $sender['will_send']);

        foreach ($possible_recipients as $recipient) {
            arrInc($senders, 'id', $recipient['id'], 'will_recieve');
            arrPut($senders_list, 'id', $sender['id'], 'letters', [
                'id'        =>  $recipient['id'],
                'email'     =>  $recipient['email'],
                'fio'       =>  $recipient['fio'],
                'address'   =>  $recipient['address']
            ]);

            // $recipients_list[$recipient['email']] = ($recipients_list[$recipient['email']] ?? 0) + 1; // by AI
            $recipients_list[$recipient['email']]++;

            $total_send_letters++;
        }
    }

    $all_recieved_letters = true;
    foreach ($recipients_list as $recieved_count) {
        $all_recieved_letters = $all_recieved_letters && (bool)$recieved_count;
    }
    if ($all_recieved_letters) {
        break;
    }
}

sort($senders_list);

$bottom_message = [
    'автоматический распределитель писем',
    'тайный письмомиксер',
    'волшебный шляп (м.р. от шляпа)',
    'генератор неслучайных чисел',
    'ручной слоувомбат',
    'неосязаемый почтальон сновидений',
    'сотрудник техотдела ООО Психотроника'
];

$message = '';
foreach ($senders_list as $sender) {

    $waiter = count($sender['letters']) > 1 ? 'ваших открыток очень ждут' : 'вашу открытку очень ждёт';
    $message .= <<<HEAD
----------------------------------------------------------------------
Кому: {$sender['email']}
----
Уважаемый(ая) {$sender['fio']}, {$waiter}:\n\n
HEAD;
    foreach ($sender['letters'] as $letter) {
        $message .= <<<LETTER
* {$letter['fio']}, по адресу: {$letter['address']}\n
LETTER;
    }
    $who = $bottom_message[ mt_rand(0, count($bottom_message))-1 ];
    $message .= <<<BOTTOM

С уважением, ваш покорный слуга, {$who}.\n  
BOTTOM;

}

var_dump($senders_list);
var_dump($message);
var_dump($recipients_list);
var_dump($total_send_letters);


















