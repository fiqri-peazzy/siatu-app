<?php

namespace App\Livewire\Sistem\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class ListUser extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    
    // Form Properties
    public $isModalOpen = false;
    public $userId;
    public $name = '';
    public $email = '';
    public $password = '';
    public $nip = '';
    public $nis = '';
    public $role = '';
    public $is_active = 1;
    
    public $isEditMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('nip', 'like', '%' . $this->search . '%')
                      ->orWhere('nis', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->role($this->roleFilter);
            })
            ->orderBy('name')
            ->paginate(10);

        $roles = Role::all();

        return view('livewire.sistem.user.list-user', compact('users', 'roles'))
            ->layout('components.layouts.app', ['header' => 'Manajemen Pengguna']);
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['userId', 'name', 'email', 'password', 'nip', 'nis', 'role', 'is_active']);
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->nip = $user->nip;
        $this->nis = $user->nis;
        $this->is_active = $user->is_active;
        $this->role = $user->roles->first()?->name ?? '';
        $this->password = ''; // Kosongkan password saat edit
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'nip' => 'nullable|string|max:50',
            'nis' => 'nullable|string|max:50',
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'required|boolean',
        ];

        if (!$this->isEditMode) {
            $rules['password'] = 'required|string|min:6';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        $validated = $this->validate($rules);

        if ($this->isEditMode) {
            $user = User::findOrFail($this->userId);
            
            $dataToUpdate = [
                'name' => $this->name,
                'email' => $this->email,
                'nip' => $this->nip,
                'nis' => $this->nis,
                'is_active' => $this->is_active,
            ];
            
            if (!empty($this->password)) {
                $dataToUpdate['password'] = Hash::make($this->password);
            }
            
            $user->update($dataToUpdate);
            $user->syncRoles([$this->role]);
            
            session()->flash('success', 'Data pengguna berhasil diperbarui.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'nip' => $this->nip,
                'nis' => $this->nis,
                'is_active' => $this->is_active,
            ]);
            
            $user->assignRole($this->role);
            
            session()->flash('success', 'Pengguna baru berhasil ditambahkan.');
        }

        $this->isModalOpen = false;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $user->delete();
        session()->flash('success', 'Pengguna berhasil dihapus.');
    }
}
