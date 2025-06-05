<?php

namespace App\Http\Controllers;


use App\Services\CronTaskService;
use Illuminate\Http\Request;

class CronTaskController extends Controller
{
    public function __construct(private CronTaskService $cronService) {}

    public function create(Request $request)
    {
        $validated = $request->validate([
            'command' => 'required|string',
            'schedule' => 'required|string'
        ]);

        try {
            $task = $this->cronService->createTask(
                $validated['command'],
                $validated['schedule']
            );

            return response()->json($task, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
