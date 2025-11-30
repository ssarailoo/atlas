<?php

namespace App\Swagger\Parameters;

use OpenApi\Annotations as OA;

/**
 * @OA\Parameter(
 *     parameter="Page",
 *     name="page",
 *     in="query",
 *     description="Page number",
 *     required=false,
 *     @OA\Schema(type="integer", default=1, minimum=1)
 * )
 *
 * @OA\Parameter(
 *     parameter="PerPage",
 *     name="per_page",
 *     in="query",
 *     description="Number of items per page",
 *     required=false,
 *     @OA\Schema(type="integer", default=15, minimum=1, maximum=100)
 * )
 */
class Pagination
{
    // Only for Swagger references
}
