<?php
namespace Tukuchi\App\Controllers;

use Tukuchi\Core\Controller;

class HelloController extends Controller
{
    public function indexAction()
    {
        $data = ['mensaje' => '¡Hola, mundo desde Tukuchi!'];
        $this->renderWithLayout('hello/index', $data);
    }
}