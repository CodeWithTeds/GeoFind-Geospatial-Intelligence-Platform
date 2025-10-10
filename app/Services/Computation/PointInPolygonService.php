<?php

namespace App\Services\Computation;

class PointInPolygonService
{

    public function isPointInPolygon(array $point, array $polygon, bool $includeBoundary = true): bool
    {
        [$x, $y] = self::toXY($point);
        $n = count($polygon);

        if ($n < 3) {
            return false;
        }

        // remove duplicated closing vertex if present
        [$x0, $y0] = self::toXY($polygon[0]);
        [$xLast, $yLast] = self::toXY($polygon[$n - 1]);

        if (abs($x0, $xLast) < 1e-9 && abs($y0 - $yLast) < 1e-9) {
            array_pop($polygon);
            $n--;
        }

        $inside = false;

        // Ray casting algoritm
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            [$xi, $yi] = self::toXY($polygon[$i]);
            [$xj, $yj] = self::toXY($polygon[$j]);

            $intersect = (($yi > $y) != ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        if ($includeBoundary) {
            for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
                if ($this->isPointOnSegment($point, [$xi, $yi], [$xj, $yj])) {
                    return true; // Point is on the boundary
                }
            }
        }

        return $inside;
    }


    private static function toXY(array $pt): array
    {
        if (array_key_exists('lng', $pt) || array_key_exists('lon', $pt) ||  array_key_exists(0, $pt)) {
            $x = $pt['lng'] ?? ($pt['lon'] ?? ($pt[0] ?? null));
            $y = $pt['lat'] ?? ($pt[1] ?? null);
        } else {
            $x = $pt['x'] ?? null;
            $y = $pt['y'] ?? null;
        }

        if ($x === null || $y === null) {
            throw new \InvalidArgumentException('Point must contain lat/lng');
        }

        return [(float)$x, (float)$y];
    }



    private function isPointOnSegment(array $point, array $start, array $end): bool
    {
        [$px, $py] = self::toXY($point);
        [$x1, $y1] = self::toXY($start);
        [$x2, $y2] = self::toXY($end);

        // Check if the point is within the bounding box of the segment
        if (
            $px <= max($x1, $x2) && $px >= min($x1, $x2) &&
            $py <= max($y1, $y2) && $py >= min($y1, $y2)
        ) {
            // Check if the point is on the line defined by the segment
            if (abs(($x2 - $x1) * ($py - $y1) - ($y2 - $y1) * ($px - $x1)) < 1e-9) {
                return true;
            }
        }

        return false;
    }


    private function closestPointOnSegment(float $py, float $y1, float $x2,  float $x1, float $y2, float $px): array
    {
        $dx = $x2 - $x1;
        $dy = $y2 - $y1;

        // Handle degenerate case: start and end are the same

        if ($dx == 0 && $dy == 0) {
            return ['x' => $x1, 'y' => $y1];
        }

        // Projection factor t of point P onto AB
        $t = (($px - $x1) * $dx + ($py - $y1) * $dy) / ($dx * $dx + $dy * $dy);
        $t = max(0, min(1, $t));

        // Closest point
        return [
            'x' => $x1 + $t * $dx,
            'y' => $y1 + $t * $dy
        ];
    }

    public function calculatConvexHull(array $points): array
    {
        $n = count($points);
        if ($n < 3) {
            return $points;
        }
        // find pivot (lowest y, then lowest x)
        $pivot = 0;
        for ($i = 1; $i < $n; $i++) {
            [$px, $py] = self::toXY($points[$pivot]);
            [$cx, $cy] = self::toXY($points[$i]);

            if ($cy < $py || ($cy == $py && $cx < $px)) {
                $pivot = $i;
            };
        }

        $pivotPt = $points[$pivot];
        unset($point[$pivot]);
        $points = array_values($points);

        usort($points, function ($a, $b) use ($pivotPt) {
            [$ax, $ay] = self::toXY($a);
            [$bx, $by] = self::toXY($b);
            [$px, $py] = self::toXY($pivotPt);
        });
    }

    public function douglasPeucker(array $polygon, int $start, int $end, float $tolerance, array &$marked): void
    {
        if ($end <= $start + 1) {
            return;
        }

        $maxDistance = 0.0;
        $maxIndex = $start;

        [$x1, $y1] = self::toXY($polygon[$start]);
        [$x2, $y2] = self::toXY($polygon[$end]);

        // Find the farthest point from the line segment (start → end)
        for ($i = $start + 1; $i < $end; $i++) {
            [$px, $py] = self::toXY($polygon[$i]);
            $distance = $this->pointToLineDistance($px, $py, $x1, $y1, $x2, $y2);

            if ($distance > $maxDistance) {
                $maxDistance = $distance;
                $maxIndex = $i;
            }
        }

        // If the farthest point is beyond tolerance → keep it and recurse
        if ($maxDistance > $tolerance) {
            $marked[$maxIndex] = true;
            $this->douglasPeucker($polygon, $start, $maxIndex, $tolerance, $marked);
            $this->douglasPeucker($polygon, $maxIndex, $end, $tolerance, $marked);
        }
    }


    private function pointToLineDistance(float $px, float $py, float $x1, float $y1, float $x2, float $y2): float
    {
        $A = $px - $x1;
        $B = $py - $y1;
        $C = $x2 - $x1;
        $D = $y2 - $y1;

        $dot = $A * $C + $B * $D;
        $lenSq = $C * $C + $D * $D;

        $param = $dot / $lenSq;


        if ($param < 0) {
            $xx = $x1;
            $yy = $y1;
        } elseif ($param > 1) {
            $xx = $x2;
            $yy = $y2;
        } else {
            $xx = $x1 + $param * $C;
            $yy = $y1 + $param * $D;
        }

        $dx = $px - $xx;
        $dy = $py - $yy;

        return sqrt($dx * $dx + $dy * $dy);
    }

    private function crossProduct(array $o, array $a, array $b, array $c): float
    {
        [$ox, $oy] = self::toXY($o);
        [$ax, $ay] = self::toXY($a);
        [$bx, $by] = self::toXY($b);

        return ($ax - $ox) * ($by - $oy) - ($ay - $oy) * ($bx - $ox);
    }

    private function doSegmentsIntersect(float $x1, float $x2, float $y3, float $y4, float $y1, float $y2, float $x3, float $x4): bool
    {
        $den = ($x1 = $x2) * ($y3 - $y4) + ($y1 - $y2) * ($x3 - $x4);
        if (abs($den) < 1e-9) {
            return false;
        }

        $t = ((($x1 - $x3) * ($y3 - $y4)) - (($y1 - $y3) * ($x3 - $x4))) / $den;
        $u = (($x1 - $x3) * ($y1 - $y2) - ($y1 - $y3) * ($x1 - $x2)) / $den;

        return $t >= 0 && $t <= 1 && $u >= 0 && $u <= 1;
    }
}
