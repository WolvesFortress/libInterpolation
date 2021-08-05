<?php

declare(strict_types=1);

namespace radondev\libInterpolation;

use pocketmine\math\Vector3;

class Interpolation
{
    /**
     * Linear interpolation between two values.
     *
     * @param float $first
     * @param float $second
     * @param float $percentage Number between 0.0 and 1.0
     * @return float
     */
    public static function linearValue(float $first, float $second, float $percentage): float
    {
        return ($second - $first) * $percentage + $first;
    }

    /**
     * Linear interpolation between two 3d-vectors.
     *
     * @param Vector3 $first
     * @param Vector3 $second
     * @param float $percentage Number between 0.0 and 1.0
     * @return Vector3
     */
    public static function linearVector(Vector3 $first, Vector3 $second, float $percentage): Vector3
    {
        return $first->addVector(
            $second->subtractVector($first)->multiply($percentage)
        );
    }

    /**
     * Cubic interpolation based on two values and the given percentage/progress.
     * Source: "Math for Game Developers - Removing Abrupt Transitions (Cubic Interpolation)" by Jorge Rodriguez (https://youtu.be/PqQH3r5Ia-Y)
     * @see https://youtu.be/PqQH3r5Ia-Y
     *
     * @param float $first
     * @param float $second
     * @param float $percentage
     * @return float
     */
    public static function cubic(float $first, float $second, float $percentage): float
    {
        return ($second - $first) * (-2 * $percentage ** 3 + 3 * $percentage ** 2) + $first;
    }

    /**
     * Cubic interpolation based on two vectors and the given percentage/progress.
     * Smooths the movement between the two vectors by accelerating and decelerating.
     * Source: "Math for Game Developers - Removing Abrupt Transitions (Cubic Interpolation)" by Jorge Rodriguez (https://youtu.be/PqQH3r5Ia-Y)
     * @see https://youtu.be/PqQH3r5Ia-Y
     *
     * @param Vector3 $first
     * @param Vector3 $second
     * @param float $percentage
     * @return Vector3
     */
    public static function cubicSmoothVector(Vector3 $first, Vector3 $second, float $percentage): Vector3
    {
        return $first->addVector(
            $second->subtractVector($first)->multiply(-2 * $percentage ** 3 + 3 * $percentage ** 2)
        );
    }

    /**
     * Source: "Programming & Using Splines - Part#1" by javidx9 (https://youtu.be/9_aJGUTePYo)
     * @see https://youtu.be/9_aJGUTePYo
     * @see https://en.wikipedia.org/wiki/Cubic_Hermite_spline
     *
     * @param Vector3 $p0 Used as the starting control point for the actual curve between $p1 and $p2
     * @param Vector3 $p1 The starting point of the interpolated curve
     * @param Vector3 $p2 The ending point of the interpolated curve
     * @param Vector3 $p3 Used as the ending control point for the actual curve between $p1 and $p2
     * @param float $percentage Number between 0.0 and 1.0
     * @param float $alpha Standard uniform Catmull-Rom spline: 0.0; Centripetal Catmull–Rom spline: 0.5; Chordal Catmull-Rom spline
     * @return Vector3
     */
    public static function catmullRomSplines(Vector3 $p0, Vector3 $p1, Vector3 $p2, Vector3 $p3, float $percentage, float $alpha = 0.5): Vector3
    {
        $tSquared = $percentage ** 2;
        $tCubed = $percentage ** 3;

        $q1 = -$tCubed + 2.0 * $tSquared - $percentage;
        $q2 = 3.0 * $tCubed - 5.0 * $tSquared + 2.0;
        $q3 = -3.0 * $tCubed + 4.0 * $tSquared + $percentage;
        $q4 = $tCubed - $tSquared;

        return new Vector3(
            $alpha * ($p0->getX() * $q1 + $p1->getX() * $q2 + $p2->getX() * $q3 + $p3->getX() * $q4),
            $alpha * ($p0->getY() * $q1 + $p1->getY() * $q2 + $p2->getY() * $q3 + $p3->getY() * $q4),
            $alpha * ($p0->getZ() * $q1 + $p1->getZ() * $q2 + $p2->getZ() * $q3 + $p3->getZ() * $q4)
        );
    }
}