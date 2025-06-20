<?php
/**
 * Framework Tukuchi - Admin Users Controller
 * Controlador para la gestión de usuarios en el panel de administración
 */

namespace Tukuchi\App\Controllers\Admin;

use Tukuchi\Core\Controller;
use Tukuchi\App\Models\User;

class UsersController extends AdminController
{
    /**
     * Mostrar lista de usuarios
     */
    public function indexAction($params = [])
    {
        $userModel = new User();
        $users = $userModel->all();
        
        $data = [
            'title' => 'Gestión de Usuarios - Administración',
            'users' => $users,
            'debug_message' => 'Esta página debería mostrarse con el layout de administración, incluyendo el menú lateral.'
        ];

        // Forzar renderizado con layout de administración
        $this->view->renderWithLayout('admin/users/index', $data, 'admin');
    }

    /**
     * Mostrar formulario para crear un nuevo usuario
     */
    public function createAction($params = [])
    {
        $data = [
            'title' => 'Crear Nuevo Usuario - Administración'
        ];

        $this->renderAdmin('admin/users/create', $data);
    }

    /**
     * Guardar un nuevo usuario
     */
    public function storeAction($params = [])
    {
        if (!$this->isPost()) {
            $this->redirect('admin/users/create');
            return;
        }

        $name = $this->getPost('name', '');
        $email = $this->getPost('email', '');
        $password = $this->getPost('password', '');
        $role = $this->getPost('role', 'user');

        // Validación básica
        if (empty($name) || empty($email) || empty($password)) {
            $session = $this->getService('session');
            $session->flash('error', 'Todos los campos son obligatorios.');
            $this->redirect('admin/users/create');
            return;
        }

        $userModel = new User();
        $existingUser = $userModel->where('email', $email)->first();

        if ($existingUser) {
            $session = $this->getService('session');
            $session->flash('error', 'El correo electrónico ya está registrado.');
            $this->redirect('admin/users/create');
            return;
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($userModel->create($data)) {
            $logger = $this->getService('logger');
            $logger->info('Nuevo usuario creado por admin', [
                'email' => $email,
                'role' => $role,
                'user_id' => $this->adminUser->id,
                'ip' => $this->request->getClientIp()
            ]);

            $session = $this->getService('session');
            $session->flash('success', 'Usuario creado correctamente.');
            $this->redirect('admin/users');
        } else {
            $session = $this->getService('session');
            $session->flash('error', 'Error al crear el usuario. Por favor, intenta nuevamente.');
            $this->redirect('admin/users/create');
        }
    }

    /**
     * Mostrar formulario para editar un usuario existente
     */
    public function editAction($params = [])
    {
        $userId = $params['id'] ?? 0;
        $userModel = new User();
        $user = $userModel->find($userId);

        if (!$user) {
            $this->renderAdmin('admin/errors/404', [
                'title' => 'Usuario no encontrado',
                'message' => 'El usuario que intentas editar no existe.'
            ]);
            return;
        }

        $data = [
            'title' => 'Editar Usuario - Administración',
            'user' => $user
        ];

        $this->renderAdmin('admin/users/edit', $data);
    }

    /**
     * Actualizar un usuario existente
     */
    public function updateAction($params = [])
    {
        if (!$this->isPost()) {
            $this->redirect('admin/users');
            return;
        }

        $userId = $params['id'] ?? 0;
        $userModel = new User();
        $user = $userModel->find($userId);

        if (!$user) {
            $session = $this->getService('session');
            $session->flash('error', 'Usuario no encontrado.');
            $this->redirect('admin/users');
            return;
        }

        $name = $this->getPost('name', '');
        $email = $this->getPost('email', '');
        $role = $this->getPost('role', 'user');
        $password = $this->getPost('password', '');

        // Validación básica
        if (empty($name) || empty($email)) {
            $session = $this->getService('session');
            $session->flash('error', 'Nombre y correo electrónico son obligatorios.');
            $this->redirect("admin/users/edit/{$userId}");
            return;
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'role' => $role
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($userModel->update($userId, $data)) {
            $logger = $this->getService('logger');
            $logger->info('Usuario actualizado por admin', [
                'user_id' => $userId,
                'email' => $email,
                'role' => $role,
                'admin_id' => $this->adminUser->id,
                'ip' => $this->request->getClientIp()
            ]);

            $session = $this->getService('session');
            $session->flash('success', 'Usuario actualizado correctamente.');
            $this->redirect('admin/users');
        } else {
            $session = $this->getService('session');
            $session->flash('error', 'Error al actualizar el usuario. Por favor, intenta nuevamente.');
            $this->redirect("admin/users/edit/{$userId}");
        }
    }

    /**
     * Eliminar un usuario
     */
    public function deleteAction($params = [])
    {
        if (!$this->isPost()) {
            $this->redirect('admin/users');
            return;
        }

        $userId = $params['id'] ?? 0;
        $userModel = new User();
        $user = $userModel->find($userId);

        if (!$user) {
            $session = $this->getService('session');
            $session->flash('error', 'Usuario no encontrado.');
            $this->redirect('admin/users');
            return;
        }

        // Evitar que un administrador se elimine a sí mismo
        if ($userId == $this->adminUser->id) {
            $session = $this->getService('session');
            $session->flash('error', 'No puedes eliminar tu propia cuenta.');
            $this->redirect('admin/users');
            return;
        }

        if ($userModel->delete($userId)) {
            $logger = $this->getService('logger');
            $logger->info('Usuario eliminado por admin', [
                'user_id' => $userId,
                'email' => $user->email,
                'admin_id' => $this->adminUser->id,
                'ip' => $this->request->getClientIp()
            ]);

            $session = $this->getService('session');
            $session->flash('success', 'Usuario eliminado correctamente.');
        } else {
            $session = $this->getService('session');
            $session->flash('error', 'Error al eliminar el usuario. Por favor, intenta nuevamente.');
        }

        $this->redirect('admin/users');
    }
}
