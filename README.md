# Задание от OK.RU

## Вопросы по выполнению задания

- Куда прислать результат? Github подойдет? Пока буду считать, что это так.
- Для какой или каких версий пишется задание? Пока буду считать, что для 7.2+. Вопрос возник из-за того, что
  написанный `__construct(resource $shm_id` имеет `resource`, который в PHP 8.0 должен быть экземпляром, да и `resource`,
  насколько помню, с версии 7.2 лучше не писать.
- Зачем в конструктор передается размер выделенной памяти и еще в int, предельное значение которого меньше возможного?
- Какую следует использовать архитектуру при написании? Пока буду использовать простейшую, т.к. наверняка для продакшена
  использоваться не будет.
- Нужны ли тесты? Пока буду считать, что нужны.
- Скрипт должен быть воркером? Пока считаю, что нет, т.к. общий скрипт будет/может содержать тест.
- Реализовать только `get()` и `put()`, т.е. удаление не предусматривать? Пока считаю, что не надо.
- Почему речь о 100Gb, если предельные данные не будут превышать 40Gb, т.е. (4294967296 * 10) / 1024 / 1024 / 1024?

## Задание

```php
<?php

/**
 * Требуется написать IntIntMap, который по произвольному int ключу хранит произвольное int значение
 * Важно: все данные (в том числе дополнительные, если их размер зависит от числа элементов) требуется хранить в выделенном заранее блоке в разделяемой памяти
 * для доступа к памяти напрямую необходимо (и достаточно) использовать следующие два метода:
 * \shmop_read и \shmop_write
 */
class IntIntMap
{
    /**
     * IntIntMap constructor.
     * @param resource $shm_id результат вызова \shmop_open
     * @param int $size размер зарезервированного блока в разделяемой памяти (~100GB)
     */
    public function __construct(resource $shm_id, int $size)
    {
        // ...
    }

    /**
     * Метод должен работать со сложностью O(1) при отсутствии коллизий, но может деградировать при их появлении
     * @param int $key произвольный ключ
     * @param int $value произвольное значение
     * @return int|null предыдущее значение
     */
    public function put(int $key, int $value): ?int
    {
        // ...
    }

    /**
     * Метод должен работать со сложностью O(1) при отсутствии коллизий, но может деградировать при их появлении
     * @param int $key ключ
     * @return int|null значение, сохраненное ранее по этому ключу
     */
    public function get(int $key): ?int
    {
        // ...
    }
}
```

## Результат
```html
Тест пройден!
Использовано памяти: 0.4Mb
```