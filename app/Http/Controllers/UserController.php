<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Localizacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('localizacao')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $localizacoes = Localizacao::where('ativo', true)->orderBy('nome_localizacao')->get();
        return view('users.create', compact('localizacoes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'localizacao_id' => ['required', 'exists:localizacoes,id'],
            'is_admin' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'localizacao_id' => $request->localizacao_id,
            'is_admin' => (bool)$request->input('is_admin'),
        ]);

        event(new Registered($user));

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $localizacoes = Localizacao::where('ativo', true)->orderBy('nome_localizacao')->get();
        return view('users.edit', compact('user', 'localizacoes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'localizacao_id' => ['required', 'exists:localizacoes,id'],
            'is_admin' => ['boolean'],
        ];

        // Apenas validar a senha se ela foi fornecida
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        // Atualizar dados básicos
        $user->name = $request->name;
        $user->email = $request->email;
        $user->localizacao_id = $request->localizacao_id;
        $user->is_admin = (bool)$request->input('is_admin');

        // Atualizar senha apenas se foi fornecida
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Impedir que o usuário exclua a si mesmo
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Você não pode excluir seu próprio usuário!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
