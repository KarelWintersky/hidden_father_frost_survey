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

$recipients = [];

$senders = [];
$max_cards = 0;

foreach (json_decode($json, true) as $s) {
    $max_cards = max($max_cards, $s['cards_count']);

    $id = $s['id'];

    $senders[] = ['will_recieve' => 0, 'will_send' => $s['cards_count'], ...$s ];

    $will_sender[] = [
        'id'        =>  $s['id'],
        'from'      =>  $s['fio'],
        'email'     =>  $s['email'],
        'letters'   =>  []
    ];

    $recipients[] = [
        'id'        =>  $s['id'],
        'fio'       =>  $s['fio'],
        'email'     =>  $s['email'],
        'letters'   =>  []
    ];

}

do {
    // каждая итерация это шаффл, потом мерж массива
    $possible_recipients = shuffle_forced($senders);

    $senders_count = 0;
    foreach ($senders as $i => $sender) {
        if ($sender['will_send'] > 0) {
            $sender_real_id = $sender['id'];
            arrDec($senders, 'id', $sender_real_id, 'will_send'); // уменьшаем число писем на 1 у этого отправителя

            $recipient = $possible_recipients[$i];
            $recipient_real_id = $recipient['id'];

            arrPut($recipients, 'id', $sender_real_id, 'letters', [
                'id'        =>  $recipient['id'],
                'fio'       =>  $recipient['fio'],
                'address'   =>  $recipient['address']
            ]);

            $senders_count++;
        }
    }
    if (empty($senders_count)) {
        break;
    }

} while(true);

sort($will_sender);
sort($recipients);

var_dump($recipients);


















