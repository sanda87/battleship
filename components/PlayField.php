<?php

namespace app\components;

/**
 * Class PlayField
 */
class PlayField
{
    const EMPTY_POINT = -1;
    const FILLED_POINT = 1;
    const BOUNDARY_POINT = 0;

    /**
     * Поле
     * @var array
     */
    private $data = [];
    /**
     * Свободные точки
     * @var array
     */
    private $freePoints = [];

    /**
     * Размер поля по абсцисс
     * @var int
     */
    private $sizeX;
    /**
     * Размер поля по ординат
     * @var int
     */
    private $sizeY;

    /**
     * Playfield constructor.
     * @param $size_x
     * @param $size_y
     */
    public function __construct($size_x, $size_y)
    {
        if (!is_int($size_x) || !is_int($size_y)) {
            throw new \InvalidArgumentException("Incorrect size of the playing field");
        }
        $this->sizeX = $size_x;
        $this->sizeY = $size_y;
        $this->create($size_x, $size_y);
    }

    /**
     * Создать поле
     * @param $size_x
     * @param $size_y
     */
    private function create($size_x, $size_y)
    {
        for ($y = 0; $y < $size_y; $y++) {
            $this->data[$y] = [];
            for ($x = 0; $x < $size_x; $x++) {
                $this->data[$y][$x] = self::EMPTY_POINT;
                $this->freePoints[] = [$x, $y];
            }
        }
    }

    /**
     * Добавить корабль
     * @param int $size_ship размер корабля
     */
    public function addShip($size_ship)
    {
        if (!is_int($size_ship) || $size_ship > $this->sizeX || $size_ship > $this->sizeY) {
            throw new \InvalidArgumentException("Wrong ship size");
        }
        $direction = mt_rand(0, 1); // down, right
        while (true) {
            $successfully_placed = true;
            $free_point = $this->getRandomFreePoint();
            $x = $free_point[0];
            $y = $free_point[1];
            for ($i = 0; $i < $size_ship; $i++) {
                $new_x = $direction ? $x + $i : $x;
                $new_y = !$direction ? $y + $i : $y;
                try {
                    if (!$this->isFree($new_x, $new_y)) {
                        $successfully_placed = false;
                        break;
                    }
                } catch (\InvalidArgumentException $exception) {
                    $successfully_placed = false;
                    break;
                }
            }
            if ($successfully_placed) {
                for ($i = 0; $i < $size_ship; $i++) {
                    $new_x = $direction ? $x + $i : $x;
                    $new_y = !$direction ? $y + $i : $y;
                    $this->fillPoint($new_x, $new_y);
                }
                break;
            }
        }

    }

    /**
     * Проверяет свободна ли точка
     * @param $x
     * @param $y
     * @return bool
     */
    public function isEmptyPoint($x, $y)
    {
        if (!$this->isPoint($x, $y)) {
            throw new \InvalidArgumentException("X or Y is incorrect");
        }
        return $this->data[$y][$x] === self::EMPTY_POINT;
    }

    /**
     * Проверяет является ли точка зоной вокруг корабля
     * @param $x
     * @param $y
     * @return bool
     */
    public function isBoundaryPoint($x, $y)
    {
        if (!$this->isPoint($x, $y)) {
            throw new \InvalidArgumentException("X or Y is incorrect");
        }
        return $this->data[$y][$x] === self::BOUNDARY_POINT;
    }

    /**
     * Проверяет является ли точка частью корабля
     * @param $x
     * @param $y
     * @return bool
     */
    public function isFilledPoint($x, $y)
    {
        if (!$this->isPoint($x, $y)) {
            throw new \InvalidArgumentException("X or Y is incorrect");
        }
        return $this->data[$y][$x] === self::FILLED_POINT;
    }

    /**
     * Можно ли разместить в этой точке корабль 1x1 c учетом соседних 8 точек
     * @param $x
     * @param $y
     * @return bool
     */
    public function isFree($x, $y)
    {
        if (!$this->isPoint($x, $y)) {
            throw new \InvalidArgumentException("X or Y is incorrect");
        }

        for ($delta_x = -1; $delta_x <= 1; $delta_x++) {
            for ($delta_y = -1; $delta_y <= 1; $delta_y++) {
                $new_x = $x + $delta_x;
                $new_y = $y + $delta_y;
                try {
                    if ($this->isFilledPoint($new_x, $new_y)) {
                        return false;
                    }
                } catch (\InvalidArgumentException $exception) {
                    continue;
                }
            }
        }
        return true;
    }

    /**
     * Получить массив всех точек поля
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Получить массив для сохранения только с заполненными точками
     * @return array
     */
    public function getDataForSave()
    {
        $filled_data = [];
        for ($y = 0; $y < $this->sizeY; $y++) {
            for ($x = 0; $x < $this->sizeX; $x++) {
                if ($this->isFilledPoint($x, $y)) {
                    $filled_data[] = [$x, $y];
                }
            }
        }
        return $filled_data;
    }

    /**
     * Заполнить точку
     * @param $x
     * @param $y
     * @return bool
     */
    public function fillPoint($x, $y)
    {
        if (!$this->isPoint($x, $y)) {
            throw new \InvalidArgumentException("X or Y is incorrect");
        }
        for ($delta_x = -1; $delta_x <= 1; $delta_x++) {
            for ($delta_y = -1; $delta_y <= 1; $delta_y++) {
                $new_x = $x + $delta_x;
                $new_y = $y + $delta_y;
                try {
                    if ($delta_x == 0 && $delta_y == 0) {
                        $this->data[$new_y][$new_x] = self::FILLED_POINT;
                    } else {
                        if ($this->isEmptyPoint($new_x, $new_y)) {
                            $this->data[$new_y][$new_x] = self::BOUNDARY_POINT;
                        }
                    }
                    $this->deleteFreePoint($new_x, $new_y);
                } catch (\InvalidArgumentException $exception) {
                    continue;
                }

            }
        }
        return true;
    }

    /**
     * Являются ли координаты точкой на поле
     * @param $x
     * @param $y
     * @return bool
     */
    public function isPoint($x, $y)
    {
        return $x >= 0 && $x < $this->sizeX && $y >= 0 && $y < $this->sizeY;
    }

    /**
     * Удаляет точку из свободных
     * @param $x
     * @param $y
     */
    private function deleteFreePoint($x, $y)
    {
        foreach ($this->freePoints as $index => $free_point) {
            if ($free_point[0] === $x && $free_point[1] === $y) {
                unset($this->freePoints[$index]);
                break;
            }
        }
    }

    /**
     * Получить случайную свободную точку
     * @return mixed
     */
    private function getRandomFreePoint()
    {
        if (!count($this->freePoints)) {
            throw new \RuntimeException("Incorrect size of the playing field");
        }
        return $this->freePoints[array_rand($this->freePoints)];
    }
}