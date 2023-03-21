<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateNotification extends Component
{

    public $users;

    public function mount()
    {

        $this->users = User::whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['super-admin']);
        })->get();;

    }

    public function render()
    {
        return view('livewire.create-notification');
    }
}
