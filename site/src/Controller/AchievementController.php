<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;
use Jugendtraining\Component\Jugendtraining\Site\Service\AchievementService;

final class AchievementController extends BaseController
{
 public function grant():void{$this->checkToken();$app=Factory::getApplication();$athleteId=$app->input->post->getInt('athlete_id');$achievementId=$app->input->post->getInt('achievement_id');$note=trim($app->input->post->getString('note',''));$access=new AccessService();if(!$access->isTrainer()||!in_array($athleteId,$access->getTrainerAthleteIds(),true)){throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);} $service=new AchievementService();$ok=$service->grant($athleteId,$achievementId,(int)$app->getIdentity()->id,'manual',$note?:null);$app->enqueueMessage($ok?'COM_JUGENDTRAINING_ACHIEVEMENT_GRANTED':'COM_JUGENDTRAINING_ACHIEVEMENT_ALREADY_GRANTED',$ok?'message':'warning');$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainerachievements',false));}
 public function revoke():void{$this->checkToken();$app=Factory::getApplication();$athleteId=$app->input->post->getInt('athlete_id');$achievementId=$app->input->post->getInt('achievement_id');$access=new AccessService();if(!$access->isTrainer()||!in_array($athleteId,$access->getTrainerAthleteIds(),true)){throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);} $ok=(new AchievementService())->revoke($athleteId,$achievementId,(int)$app->getIdentity()->id);$app->enqueueMessage($ok?'COM_JUGENDTRAINING_ACHIEVEMENT_REVOKED':'COM_JUGENDTRAINING_ACHIEVEMENT_REVOKE_FAILED',$ok?'message':'warning');$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainerachievements',false));}
 public function evaluate():void{$this->checkToken();$app=Factory::getApplication();$access=new AccessService();if(!$access->isTrainer())throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);$count=(new AchievementService())->evaluateAthletes($access->getTrainerAthleteIds());$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('COM_JUGENDTRAINING_ACHIEVEMENTS_EVALUATED',$count));$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainerachievements',false));}
}
