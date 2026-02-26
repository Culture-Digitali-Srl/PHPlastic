<?php

namespace App\Modules\Todo;

use App\Core\Controller;
use App\Core\DB;

class TodoController extends Controller
{
    public function index()
    {
        $todos = DB::query('SELECT * FROM todos')->fetchAll();
        return $this->jsonResponse($todos);
    }

    public function show($id)
    {
        $todo = DB::query('SELECT * FROM todos WHERE id = ?', [$id])->fetch();
        return $this->jsonResponse($todo);
    }

    public function store()
    {
        $data = $_POST;
        DB::query('INSERT INTO todos (title, status) VALUES (?, ?)', [$data['title'], $data['status']]);
        return $this->jsonResponse(['message' => 'Todo created successfully']);
    }

    public function update($id)
    {
        $data = $_POST;
        DB::query('UPDATE todos SET title = ?, status = ? WHERE id = ?', [$data['title'], $data['status'], $id]);
        return $this->jsonResponse(['message' => 'Todo updated successfully']);
    }

    public function destroy($id)
    {
        DB::query('DELETE FROM todos WHERE id = ?', [$id]);
        return $this->jsonResponse(['message' => 'Todo deleted successfully']);
    }
}
