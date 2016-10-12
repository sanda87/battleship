<?php
/**
 * Created by PhpStorm.
 * User: Sanda
 * Date: 12.10.2016
 * Time: 9:19
 */

namespace app\helpers;


class PlayFieldHelper
{

    public static function isFilledPoint($filled_points, $x, $y)
    {
        foreach ($filled_points as $point) {
            if ($point[0] == $x && $point[1] == $y) {
                return true;
            }
        }
        return false;
    }

}