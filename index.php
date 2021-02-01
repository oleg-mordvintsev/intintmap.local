<?php

declare(strict_types=1);

// Определяем версию PHP
if (version_compare(PHP_VERSION, '7.2', '<') || version_compare(PHP_VERSION, '8.0', '>=')) {
    echo "Требуется версия PHP 7.2 - 7.4";
    exit;
}

// Проверка загруженного расширения
if (!extension_loaded('shmop')) {
    echo "Требуется расширение `shmop`.";
    exit;
}

// ОЗУ пиковые данные
register_shutdown_function(function () {
    echo "Использовано памяти: ", round((memory_get_peak_usage() / 1024 / 1024) * 10) / 10, "Mb";
});


### ОСНОВНЫЕ ПАРАМЕТРЫ ###

// Получаем идентификатор ключа
$shmopKey = ftok(__FILE__, 'o');

// Права доступа
$shmopPermissions = 0644;

// Размер резервируемой памяти
//$shmopSize = 1024 ** 3 * 100; // 100Gb
$shmopSize = 1024 ** 3; // 1Gb


### ПОДГОТОВКА ###

// Ресурс, пробуем создать или получить доступ
$shmop = shmop_open($shmopKey, 'c', $shmopPermissions, $shmopSize);

// Проверка
if ($shmop === false) {
    echo "Не возможно получить доступ к памяти.";
    exit;
}


### ЗАПУСК ###

require __DIR__ . DIRECTORY_SEPARATOR . 'IntIntMap.php';
$shmopObj = new IntIntMap($shmop, shmop_size($shmop));


### ТЕСТИРОВАНИЕ ###

// Генерируем случайные 100 000 значений
$generator = (function() use($shmopObj){
    for ($n = 0; 100000 > $n; $n++) {
        $key = rand(0, $shmopObj->getMaxKey());
        $value = rand(0, 4294967296);
        yield $key => $value;
    }
})();

// Проверка значений генератора и в ОЗУ
foreach ($generator as $key => $value) {
    $shmopObj->put($key, $value);
    $item = $shmopObj->get($key);
    if ($item != $value) {
        echo "Ошибка, проверка не пройдена. Ключ: {$key}. Значение в ОЗУ: {$item}. Значение в массиве: {$value}.<br>";
        exit;
    }
    $preKey = $key;
    $preValue = $value;
}

echo "Тест пройден!<br>";
