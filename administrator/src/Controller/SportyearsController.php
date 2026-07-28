<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\AdminController;
final class SportyearsController extends AdminController
{
 public function getModel($name='Sportyear', $prefix='Administrator', $config=['ignore_request'=>true])
 { return parent::getModel($name,$prefix,$config); }
}
