<?php
/**
 * Framework Tukuchi - User Controller
 * Controlador de ejemplo con funcionalidades completas
 */

namespace Tukuchi\App\Controllers;

use Tukuchi\Core\Controller;
use Tukuchi\Core\Validator;
use Tukuchi\App\Models\User;

class UserController extends Controller
{
    private $logger;

    public function init()
    {
        // Obtener logger del Service Locator
        try {
            $this->logger = $this->getService('logger');
        } catch (\Exception $e) {
            // Si no hay logger configurado, crear uno básico
            $this->logger = new \Tukuchi\Core\Logger();
        }
    }

    /**
     * Listar usuarios
     */
    public function indexAction($params = [])
    {
        try {
            $users = User::all();
            
            $this->logger->info('Lista de usuarios consultada', [
                'total_users' => count($users),
                'ip' => $this->request->getClientIp()
            ]);

            $data = [
                'title' => 'Gestión de Usuarios',
                'users' => $users,
                'csrf_token' => $this->generateCsrfToken()
            ];

            $this->renderWithLayout('users/index', $data);

        } catch (\Exception $e) {
            $this->logger->error('Error al consultar usuarios', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            $this->handleError('Error al cargar usuarios');
        }
    }

    /**
     * Mostrar formulario de creación
     */
    public function createAction($params = [])
    {
        $data = [
            'title' => 'Crear Usuario',
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->renderWithLayout('users/create', $data);
    }

    /**
     * Guardar nuevo usuario
     */
    public function storeAction($params = [])
    {
        if (!$this->isPost()) {
            $this->redirect('user', 'create');
            return;
        }

        try {
            // Validar token CSRF
            $this->validateCsrfToken();

            // Obtener datos del formulario
            $data = [
                'name' => $this->getPost('name'),
                'email' => $this->getPost('email'),
                'password' => $this->getPost('password'),
                'password_confirmation' => $this->getPost('password_confirmation'),
                'status' => $this->getPost('status', 'active')
            ];

            // Validar datos
            $validator = Validator::make($data, [
                'name' => 'required|min:2|max:100|alpha',
                'email' => 'required|email|max:150',
                'password' => 'required|min:6|confirmed',
                'status' => 'required|in:active,inactive'
            ], [
                'name.required' => 'El nombre es obligatorio',
                'name.alpha' => 'El nombre solo puede contener letras',
                'email.required' => 'El email es obligatorio',
                'password.confirmed' => 'Las contraseñas no coinciden'
            ]);

            if ($validator->fails()) {
                if ($this->isAjax()) {
                    $this->json([
                        'status' => 'error',
                        'message' => 'Errores de validación',
                        'errors' => $validator->errors()
                    ], 422);
                } else {
                    $data['errors'] = $validator->errors();
                    $data['old'] = $this->getPost();
                    $data['title'] = 'Crear Usuario';
                    $data['csrf_token'] = $this->generateCsrfToken();
                    
                    $this->renderWithLayout('users/create', $data);
                }
                return;
            }

            // Crear usuario
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->setPassword($data['password']);
            $user->status = $data['status'];

            if ($user->save()) {
                $this->logger->info('Usuario creado exitosamente', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'created_by_ip' => $this->request->getClientIp()
                ]);

                if ($this->isAjax()) {
                    $this->json([
                        'status' => 'success',
                        'message' => 'Usuario creado exitosamente',
                        'user' => $user->toArray()
                    ]);
                } else {
                    $this->redirect('user', 'index');
                }
            } else {
                throw new \Exception('Error al guardar el usuario');
            }

        } catch (\Exception $e) {
            $this->logger->error('Error al crear usuario', [
                'error' => $e->getMessage(),
                'data' => $data ?? [],
                'ip' => $this->request->getClientIp()
            ]);

            if ($this->isAjax()) {
                $this->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            } else {
                $this->handleError('Error al crear usuario: ' . $e->getMessage());
            }
        }
    }

    /**
     * Mostrar usuario específico
     */
    public function showAction($params = [])
    {
        $id = $params[0] ?? null;

        if (!$id) {
            $this->redirect('user', 'index');
            return;
        }

        try {
            $user = User::findOrFail($id);

            $this->logger->info('Usuario consultado', [
                'user_id' => $user->id,
                'ip' => $this->request->getClientIp()
            ]);

            $data = [
                'title' => 'Detalles del Usuario',
                'user' => $user
            ];

            if ($this->isAjax()) {
                $this->json([
                    'status' => 'success',
                    'user' => $user->toArray()
                ]);
            } else {
                $this->renderWithLayout('users/show', $data);
            }

        } catch (\Exception $e) {
            $this->logger->warning('Usuario no encontrado', [
                'user_id' => $id,
                'ip' => $this->request->getClientIp()
            ]);

            if ($this->isAjax()) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Usuario no encontrado'
                ], 404);
            } else {
                $this->handleError('Usuario no encontrado');
            }
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function editAction($params = [])
    {
        $id = $params[0] ?? null;

        if (!$id) {
            $this->redirect('user', 'index');
            return;
        }

        try {
            $user = User::findOrFail($id);

            $data = [
                'title' => 'Editar Usuario',
                'user' => $user,
                'csrf_token' => $this->generateCsrfToken()
            ];

            $this->renderWithLayout('users/edit', $data);

        } catch (\Exception $e) {
            $this->handleError('Usuario no encontrado');
        }
    }

    /**
     * Actualizar usuario
     */
    public function updateAction($params = [])
    {
        $id = $params[0] ?? null;

        if (!$id || !$this->isPost()) {
            $this->redirect('user', 'index');
            return;
        }

        try {
            // Validar token CSRF
            $this->validateCsrfToken();

            $user = User::findOrFail($id);

            // Obtener datos del formulario
            $data = [
                'name' => $this->getPost('name'),
                'email' => $this->getPost('email'),
                'status' => $this->getPost('status', 'active')
            ];

            // Validar datos
            $rules = [
                'name' => 'required|min:2|max:100|alpha',
                'email' => 'required|email|max:150',
                'status' => 'required|in:active,inactive'
            ];

            // Si se proporciona nueva contraseña, validarla
            if ($this->getPost('password')) {
                $data['password'] = $this->getPost('password');
                $data['password_confirmation'] = $this->getPost('password_confirmation');
                $rules['password'] = 'required|min:6|confirmed';
            }

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                if ($this->isAjax()) {
                    $this->json([
                        'status' => 'error',
                        'message' => 'Errores de validación',
                        'errors' => $validator->errors()
                    ], 422);
                } else {
                    $data['errors'] = $validator->errors();
                    $data['old'] = $this->getPost();
                    $data['title'] = 'Editar Usuario';
                    $data['user'] = $user;
                    $data['csrf_token'] = $this->generateCsrfToken();
                    
                    $this->renderWithLayout('users/edit', $data);
                }
                return;
            }

            // Actualizar usuario
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->status = $data['status'];

            if (isset($data['password'])) {
                $user->setPassword($data['password']);
            }

            if ($user->save()) {
                $this->logger->info('Usuario actualizado exitosamente', [
                    'user_id' => $user->id,
                    'updated_by_ip' => $this->request->getClientIp()
                ]);

                if ($this->isAjax()) {
                    $this->json([
                        'status' => 'success',
                        'message' => 'Usuario actualizado exitosamente',
                        'user' => $user->toArray()
                    ]);
                } else {
                    $this->redirect('user', 'show', [$user->id]);
                }
            } else {
                throw new \Exception('Error al actualizar el usuario');
            }

        } catch (\Exception $e) {
            $this->logger->error('Error al actualizar usuario', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'ip' => $this->request->getClientIp()
            ]);

            if ($this->isAjax()) {
                $this->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            } else {
                $this->handleError('Error al actualizar usuario: ' . $e->getMessage());
            }
        }
    }

    /**
     * Eliminar usuario
     */
    public function deleteAction($params = [])
    {
        $id = $params[0] ?? null;

        if (!$id || !$this->isPost()) {
            $this->redirect('user', 'index');
            return;
        }

        try {
            // Validar token CSRF
            $this->validateCsrfToken();

            $user = User::findOrFail($id);
            $userName = $user->name;

            if ($user->delete()) {
                $this->logger->info('Usuario eliminado exitosamente', [
                    'user_id' => $id,
                    'user_name' => $userName,
                    'deleted_by_ip' => $this->request->getClientIp()
                ]);

                if ($this->isAjax()) {
                    $this->json([
                        'status' => 'success',
                        'message' => 'Usuario eliminado exitosamente'
                    ]);
                } else {
                    $this->redirect('user', 'index');
                }
            } else {
                throw new \Exception('Error al eliminar el usuario');
            }

        } catch (\Exception $e) {
            $this->logger->error('Error al eliminar usuario', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'ip' => $this->request->getClientIp()
            ]);

            if ($this->isAjax()) {
                $this->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            } else {
                $this->handleError('Error al eliminar usuario: ' . $e->getMessage());
            }
        }
    }

    /**
     * API para búsqueda de usuarios
     */
    public function searchAction($params = [])
    {
        try {
            $query = $this->getGet('q', '');
            
            if (empty($query)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Parámetro de búsqueda requerido'
                ], 400);
                return;
            }

            // Búsqueda simple por nombre o email
            $users = User::where('name', 'LIKE', "%{$query}%");
            $emailUsers = User::where('email', 'LIKE', "%{$query}%");
            
            // Combinar resultados (en una implementación real usarías UNION)
            $allUsers = array_merge($users, $emailUsers);
            
            // Remover duplicados
            $uniqueUsers = [];
            $seenIds = [];
            
            foreach ($allUsers as $user) {
                if (!in_array($user->id, $seenIds)) {
                    $uniqueUsers[] = $user;
                    $seenIds[] = $user->id;
                }
            }

            $this->logger->info('Búsqueda de usuarios realizada', [
                'query' => $query,
                'results' => count($uniqueUsers),
                'ip' => $this->request->getClientIp()
            ]);

            $this->json([
                'status' => 'success',
                'users' => array_map(function($user) {
                    return $user->toArray();
                }, $uniqueUsers),
                'total' => count($uniqueUsers)
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Error en búsqueda de usuarios', [
                'error' => $e->getMessage(),
                'query' => $query ?? '',
                'ip' => $this->request->getClientIp()
            ]);

            $this->json([
                'status' => 'error',
                'message' => 'Error en la búsqueda'
            ], 500);
        }
    }

    /**
     * Manejar errores
     */
    private function handleError($message)
    {
        $data = [
            'title' => 'Error',
            'message' => $message
        ];

        $this->renderWithLayout('errors/general', $data);
    }
}