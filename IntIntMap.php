<?php

declare(strict_types=1);

/**
 * Требуется написать IntIntMap, который по произвольному int ключу хранит произвольное int значение
 * Важно: все данные (в том числе дополнительные, если их размер зависит от числа элементов)
 * требуется хранить в выделенном заранее блоке в разделяемой памяти
 * для доступа к памяти напрямую необходимо (и достаточно) использовать следующие два метода:
 * \shmop_read и \shmop_write
 */
class IntIntMap
{

    /**
     * Ресурс или экземпляр shmop_open
     * @var resource|shmop
     */
    private $id;

    /**
     * Размер выделенной памяти в байтах
     * @var int
     */
    private $size;

    /**
     * Максимально допустимый ключ
     * @var int
     */
    private $maxKey;

    /**
     * Кол-во байт на единицу данных
     * @var int
     */
    private $bytes = 10;

    /**
     * IntIntMap constructor.
     * @param $shm_id
     * @param int $size
     */
    public function __construct($shm_id, int $size) // Убрал resource - смотрите https://www.php.net/manual/ru/function.shmop-open.php#refsect1-function.shmop-open-changelog
    {
        if (!$this->validType($shm_id)) {
            echo "Передан не известный тип, вместо 'resource' или 'shmop'.";
            exit;
        }
        $this->id = $shm_id;
        $this->size = $size;
        $this->calculateMaxKey();
    }

    /**
     * Метод записи с получением предыдущего значения
     * @param int $key произвольный ключ
     * @param int $value произвольное значение
     * @return int|null предыдущее значение
     */
    public function put(int $key, int $value): ?int
    {
        $old = $this->get($key); // Нарушает SOLID (S), но требуется для задачи.
        $value = str_pad((string)$value, $this->bytes, "0", STR_PAD_LEFT);
        shmop_write($this->id, $value, $key * $this->bytes);
        return $old;
    }

    /**
     * Метод чтения
     * @param int $key ключ
     * @return int|null значение, сохраненное ранее по этому ключу
     */
    public function get(int $key): ?int
    {
        $value = shmop_read($this->id, $key * $this->bytes, $this->bytes);
        if ('' !== trim($value)) {
            $value = intval($value);
        } else {
            $value = null;
        }
        return $value;
    }

    /**
     * Валидация типа
     * @param $shm_id
     * @return bool
     */
    private function validType(&$shm_id): bool
    {
        $type = gettype($shm_id);
        if ('resource' === $type || 'shmop' === $type) {
            return true;
        }
        return false;
    }

    /**
     * Вычисляем максимальный ключ
     */
    private function calculateMaxKey(): void
    {
        $this->maxKey = intdiv(shmop_size($this->id), $this->bytes) - 1;
    }

    /**
     * @return int
     */
    public function getMaxKey(): int
    {
        return $this->maxKey;
    }
}