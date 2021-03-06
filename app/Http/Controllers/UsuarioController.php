<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use stdClass;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $us = $request->all();
        $usuario = new Usuario();
        $usuario->nome = $us['nome'];
        $usuario->email = $us['email'];
        $usuario->senha = $us['senha'];

        $usuarioExiste = Usuario::where('email', $us['email'])->first();

        if ($usuarioExiste) {
            return response('Já existe um usuário com esse email registrado.', 400);
        }

        $confirmarSenha = $us['confirmarSenha'];
        if ( $usuario->senha != $confirmarSenha ) {
            return response('As senhas não coincidem.', 400);
        }

        $usuario->senha = Hash::make($usuario->senha);

        $usuario->save();
        return $usuario;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function show(int $usuario_id)
    {
        //
        return Usuario::with('carros')->find($usuario_id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function edit(Usuario $usuario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Usuario $usuario)
    {
        //
        if (!$usuario) return response('Usuário Inválido', 400);

        $nome = $request->input('nome');
        $email = $request->input('email');
        $senha = $request->input('senha');
        $confirmarSenha = $request->input('confirmarSenha');

        if (!$nome) return response('Campo nome é obrigatório.', 422);
        if (!$email) return response('Campo email é obrigatório.', 422);

        if ($email != $usuario->email) {
            $checkEmail = Usuario::where('email', '=', $email)->first();
            if ($checkEmail) return response('Já existe um usuário com esse email', 422);
        }

        if ($senha) {
            if ($senha != $confirmarSenha) return response('As senhas não coincidem.', 422);
        }

        $usuario->nome = $nome;
        $usuario->email = $email;
        if ($senha) {
            $usuario->senha = Hash::make($senha);
        }

        $res = $usuario->save();

        if ($res) {
            return response($usuario, 200);
        }

        return response('Erro ao atualizar o usuário.', 400);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Usuario $usuario)
    {
        //
    }

    public function login(Request $request) {
        $email = $request->input('email');
        $senha = $request->input('senha');

        if (!$email || !$senha) {
            return response('Credenciais inválidas.', 400);
        }

        $usuario = Usuario::where('email', $email)->first();

        if (!$usuario) {
            return response('Credenciais inválidas.', 400);
        }

        if (!Hash::check($senha, $usuario->senha)) {
            return response('Credenciais inválidas.', 400);
        }

        return $usuario;
    }
}
