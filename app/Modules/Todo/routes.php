<?php
$router->get('/api/todos', [\App\Modules\Todo\TodoController::class, 'index']);
$router->get('/api/todos/{id}', [\App\Modules\Todo\TodoController::class, 'show']);
$router->post('/api/todos', [\App\Modules\Todo\TodoController::class, 'store']);
$router->put('/api/todos/{id}', [\App\Modules\Todo\TodoController::class, 'update']);
$router->delete('/api/todos/{id}', [\App\Modules\Todo\TodoController::class, 'destroy']);