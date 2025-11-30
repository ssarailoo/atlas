<?php

namespace App\Swagger\Parameters;

use OpenApi\Annotations as OA;

/**
 * @OA\Parameter(
 *     parameter="Pagination",
 *     name="pagination",
 *     in="query",
 *     description="Pagination parameters",
 *     required=false,
 *     @OA\Schema(
 *         type="object",
 *         @OA\Property(property="page", type="integer", default=1, minimum=1),
 *         @OA\Property(property="per_page", type="integer", default=15, minimum=1, maximum=100)
 *     )
 * )
 */
class Pagination
{
    // Only for Swagger references
}
