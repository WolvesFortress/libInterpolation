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
     * @see https://en.wikipedia.org/wiki/B%C3%A9zier_curve#Quadratic_B%C3%A9zier_curves
     *
     * @param float $first
     * @param float $second
     * @param float $third
     * @param float $percentage
     * @return float
     */
    public static function quadraticBezierValue(float $first, float $second, float $third, float $percentage): float
    {
        $inversePercentage = (1 - $percentage);

        return ($inversePercentage ** 2 * $first) + (2 * $inversePercentage * $percentage * $second) + ($percentage ** 2 * $third);
    }

    /**
     * @see https://en.wikipedia.org/wiki/B%C3%A9zier_curve#Quadratic_B%C3%A9zier_curves
     *
     * @param Vector3 $first
     * @param Vector3 $second
     * @param Vector3 $third
     * @param float $percentage
     * @return Vector3
     */
    public static function quadraticBezierVector(Vector3 $first, Vector3 $second, Vector3 $third, float $percentage): Vector3
    {
        $inversePercentage = (1 - $percentage);

        $poly1 = $first->multiply($inversePercentage ** 2);
        $poly2 = $second->multiply(2 * $inversePercentage * $percentage);
        $poly3 = $third->multiply($percentage ** 2);

        return $poly1->addVector($poly2)->addVector($poly3);
    }

    /**
     * @see https://en.wikipedia.org/wiki/De_Casteljau%27s_algorithm
     * @see https://en.wikipedia.org/wiki/B%C3%A9zier_curve#Quadratic_B%C3%A9zier_curves
     *
     * @param float $first
     * @param float $second
     * @param float $third
     * @param float $percentage Number between 0.0 and 1.0
     * @return float
     */
    public static function quadraticDeCasteljauValue(float $first, float $second, float $third, float $percentage): float
    {
        $inversePercentage = (1 - $percentage);

        $d1 = ($inversePercentage * $first) + ($percentage * $second);
        $d2 = ($inversePercentage * $second) + ($percentage * $third);

        return ($inversePercentage * $d1) + ($percentage * $d2);
    }

    /**
     * @see https://en.wikipedia.org/wiki/De_Casteljau%27s_algorithm
     * @see https://en.wikipedia.org/wiki/B%C3%A9zier_curve#Quadratic_B%C3%A9zier_curves
     *
     * @param Vector3 $first
     * @param Vector3 $second
     * @param Vector3 $third
     * @param float $percentage Number between 0.0 and 1.0
     * @return Vector3
     */
    public static function quadraticDeCasteljauVector(Vector3 $first, Vector3 $second, Vector3 $third, float $percentage): Vector3
    {
        $inversePercentage = (1 - $percentage);

        $d1 = $first->multiply($inversePercentage)->addVector(
            $second->multiply($percentage)
        );
        $d2 = $second->multiply($inversePercentage)->addVector(
            $third->multiply($percentage)
        );

        return $d1->multiply($inversePercentage)->addVector(
            $d2->multiply($percentage)
        );
    }

    /**
     * Cubic interpolation based on two values and the given percentage/progress.
     * Smooths the movement between the two values by accelerating and decelerating.
     * Source: "Math for Game Developers - Removing Abrupt Transitions (Cubic Interpolation)" by Jorge Rodriguez (https://youtu.be/PqQH3r5Ia-Y)
     * @see https://youtu.be/PqQH3r5Ia-Y
     *
     * @param float $first
     * @param float $second
     * @param float $percentage
     * @return float
     */
    public static function cubicSmooth(float $first, float $second, float $percentage): float
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
        $percentageSquared = $percentage ** 2;
        $percentageCubed = $percentage ** 3;

        $q1 = -$percentageCubed + 2.0 * $percentageSquared - $percentage;
        $q2 = 3.0 * $percentageCubed - 5.0 * $percentageSquared + 2.0;
        $q3 = -3.0 * $percentageCubed + 4.0 * $percentageSquared + $percentage;
        $q4 = $percentageCubed - $percentageSquared;

        return new Vector3(
            $alpha * ($p0->getX() * $q1 + $p1->getX() * $q2 + $p2->getX() * $q3 + $p3->getX() * $q4),
            $alpha * ($p0->getY() * $q1 + $p1->getY() * $q2 + $p2->getY() * $q3 + $p3->getY() * $q4),
            $alpha * ($p0->getZ() * $q1 + $p1->getZ() * $q2 + $p2->getZ() * $q3 + $p3->getZ() * $q4)
        );
    }
}