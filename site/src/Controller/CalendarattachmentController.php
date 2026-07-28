<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Controller\BaseController;use Jugendtraining\Component\Jugendtraining\Site\Service\CalendarService;
final class CalendarattachmentController extends BaseController{
 public function download():void{
  $app=Factory::getApplication();$calendar=new CalendarService();if(!$calendar->canReadCalendar())throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  $row=$calendar->attachment($app->input->getInt('id'));if(!$row)throw new \RuntimeException('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND',404);
  if(!$calendar->canReadAttachment($row))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  $data=base64_decode((string)$row->file_data,true);if($data===false)throw new \RuntimeException('Datei konnte nicht gelesen werden.',500);
  while(ob_get_level())ob_end_clean();$safe=preg_replace('/[^A-Za-z0-9._-]+/u','_',basename((string)$row->file_name))?:'Anhang.pdf';
  $app->setHeader('Content-Type','application/pdf',true);$app->setHeader('Content-Disposition','attachment; filename="'.$safe.'"',true);$app->setHeader('Content-Length',(string)strlen($data),true);$app->setHeader('Cache-Control','private, no-store, max-age=0',true);$app->sendHeaders();echo$data;$app->close();
 }
}
