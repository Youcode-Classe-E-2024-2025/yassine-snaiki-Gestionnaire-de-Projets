<?php

namespace app\controllers;
use app\core\Controller;
use app\core\Application;
use app\core\Request;
use app\models\User;

class AuthController extends Controller {
    public function login() {
        return $this->render("login");
    }
    public function register() {
        return $this->render("register");
    }

    public function handleLogin(Request $request) {
        if(User::validate($request->getBody())) {
            $user = User::findByEmail($request->getBody()["email"]);
            if($user) {
                $userInstance = new User($user);
                if($userInstance->checkPassword($request->getBody()["password"])){
                    $_SESSION['user'] = [
                                'id' => $userInstance->id,
                                'email' => $userInstance->email,
                                'firstname' => $userInstance->firstname,
                                'lastname' => $userInstance->lastname
                            ];
                    header('Location: /projects');
                    exit();
                }else $_SESSION['error'] = "Invalid password";
            }
            else $_SESSION['error'] = "User not found";
        }else $_SESSION['error'] = "Both fields are required";
        header('Location: /login');
        }
    
    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function handleRegister(Request $request) {
        $user = $request->getBody();
        if(User::validate($user)) {
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
            $userInstance = new User($user);
            if($userInstance->save())
            header('Location: /login');
        }
    }
}