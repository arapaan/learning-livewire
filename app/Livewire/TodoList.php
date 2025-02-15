<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;

class TodoList extends Component
{

    use WithPagination;

    #[Rule('required|min:3|max:50')]
    public $name;

    public $search;

    public $EditingTodoID;

    #[Rule('required|min:3|max:50')]
    public $EditingTodoName;

    public function create() {
        $validated = $this->validateOnly('name');

        Todo::create($validated);

        $this->reset('name');

        session()->flash('success','Created.');
    }

    public function delete(Todo $todo) {
        $todo->delete();
    }

    public function toggle($todoID) {
        $todo = Todo::find($todoID);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($todoID) {
        $this->EditingTodoID = $todoID;
        $this->EditingTodoName = Todo::find($todoID)->name;
    }

    public function cancelEdit() {
        $this->reset('EditingTodoID','EditingTodoName');
    }

    public function update() {
        $this->validateOnly('EditingTodoName');

        Todo::find($this->EditingTodoID)->update(
            [
                'name' => $this->EditingTodoName
            ]
            );

            $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.todo-list',[
            'todos' => Todo::latest()->where('name','like', "%{$this->search}%")->paginate(5)
        ]);
    }
}
