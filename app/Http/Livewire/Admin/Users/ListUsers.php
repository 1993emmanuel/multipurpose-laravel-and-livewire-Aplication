<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class ListUsers extends Component
{
    //englobar las variables en un array
    public $state = [];
    //variable usuario
    public $user;
    //estado del modal ya sea editar o crear
    public $showEditModal = false;

    public $userIdBeingRemoved = null;
    
    public function addNew(){
        $this->state = [];
        $this->showEditModal = false;
        $this->dispatchBrowserEvent('show-form');
    }

    //function create new user
    public function createUser(){
        $validateData = Validator::make($this->state, [
            'name'=>'required',
            'email'=> 'required|email|unique:users',
            'password' => 'required|confirmed'
        ])->validate();
        $validateData['password'] = bcrypt($validateData['password']);
        User::create($validateData);
        // pasando un mensaje
        // session()->flash('message','User added successfully!!!');
        $this->dispatchBrowserEvent('hide-form',['message'=>'User added successfully']);
    }

    //funcion para editar un usuario
    public function edit(User $user){
        $this->showEditModal = true;
        $this->user = $user;
        $this->state = $user->toArray();
        $this->dispatchBrowserEvent('show-form');
    }

    public function updateUser(){
        $validateData = Validator::make($this->state, [
            'name'=>'required',
            'email'=> 'required|email|unique:users,email,'.$this->user->id,
            'password' => 'sometimes|confirmed'
        ])->validate();
        if(!empty($validateData['password'])){
            $validateData['password'] = bcrypt($validateData['password']);
        }
        $this->user->update($validateData);
        $this->dispatchBrowserEvent('hide-form',['message'=>'User edited successfully**']);
    }

    //show delete modal
    public function confirmUserRemoval($userId){
        $this->userIdBeingRemoved = $userId;
        $this->dispatchBrowserEvent('show-delete-modal');
    }

    public function deleteUser(){
        $user = User::findOrFail($this->userIdBeingRemoved);
        $user->delete();
        $this->dispatchBrowserEvent('hide-delete-modal',['message'=>'User deleted successfully °°°']);
    }

    public function render()
    {
        $users = User::latest()->paginate();
        return view('livewire.admin.users.list-users',[
            'users'=>$users
        ]);
    }
}
