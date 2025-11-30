<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    public function __construct(readonly private EmployeeService $service)
    {
    }

    public function index(IndexEmployeeRequest $request)
    {
        $employees = $this->service->getEmployees($request->validated());
        return response()->json([
            'data' => EmployeeResource::collection($employees),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage()
                , 'total' => $employees->total()],
        ], Response::HTTP_OK);
    }
}
