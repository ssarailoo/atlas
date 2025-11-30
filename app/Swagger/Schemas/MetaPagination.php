<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="MetaPagination",
 *     type="object",
 *     @OA\Property(property="current_page", type="integer"),
 *     @OA\Property(property="last_page", type="integer"),
 *     @OA\Property(property="per_page", type="integer"),
 *     @OA\Property(property="total", type="integer")
 * )
 */
class MetaPagination {}
