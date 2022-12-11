<?php

namespace App\Library\Traits;


use App\Library\Fraction\Fraction;


trait Utils
{
    /**
     * Сгенерировать случайное число
     *
     * @param  array  $except
     * @param  int  $min
     * @param  int  $max
     *
     * @return int
     */
    public function rand (array $except = [], int $min = -9, int $max = 9): int
    {
        do {
            $int = mt_rand($min, $max);
        } while (in_array($int, $except));

        return $int;
    }

    /**
     * Сгенерировать случайную дробь
     *
     * @param  array  $except
     * @param  int  $min
     * @param  int  $max
     *
     * @return Fraction
     */
    public function randSimplifiedFraction (array $except = [], int $min = -9, int $max = 9): Fraction
    {
        return $this->randFraction($except, $min, $max)->simplify();
    }

    /**
     * Сгенерировать случайную дробь
     *
     * @param  array  $except
     * @param  int  $min
     * @param  int  $max
     *
     * @return Fraction
     */
    public function randFraction (array $except = [], int $min = -9, int $max = 9): Fraction
    {
        do {
            $denominator = mt_rand(1, $max);
        } while (in_array($denominator, array_merge($except, [0])));

        do {
            $numerator = mt_rand($min, $max);
        } while (in_array($numerator, array_merge($except, [0])) && $numerator % $denominator !== 0);

        return $this->fraction($numerator, $denominator);
    }

    /**
     * Создать дробь
     *
     * @param  int  $numerator
     * @param  int  $denominator
     *
     * @return Fraction
     */
    public function fraction (int $numerator, int $denominator = 1): Fraction
    {
        return new Fraction($numerator, $denominator);
    }

    public function arr2fracArr (array $array): array
    {
        $fracArr = explode(' ', (new Fraction($array[0], $array[1]))->__toString());

        if (count($fracArr) === 2) {
            $fracPart = explode('/', $fracArr[1]);

            return [
                $fracPart[0],
                $fracPart[1],
                $fracArr[0],
            ];
        } else {
            $fracPart = explode('/', $fracArr[0]);

            return [
                $fracPart[0],
                $fracPart[1],
            ];
        }
    }
}
