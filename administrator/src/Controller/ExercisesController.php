<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\AdminController;
final class ExercisesController extends AdminController {
  public function getModel($name = 'Exercise', $prefix = 'Administrator', $config = ['ignore_request' => true]) {
    return parent::getModel($name, $prefix, $config);
  }
}
