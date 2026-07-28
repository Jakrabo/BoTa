<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

final class UserpreferencesModel extends BaseDatabaseModel
{
    public function getTheme(): string
    {
        $user=Factory::getApplication()->getIdentity();
        if($user->guest)throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
        $theme=(string)$user->getParam('bota_theme','auto');
        return in_array($theme,['auto','light','dark'],true)?$theme:'auto';
    }

    public function saveTheme(string $theme): void
    {
        $user=Factory::getApplication()->getIdentity();
        if($user->guest)throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
        if(!in_array($theme,['auto','light','dark'],true))throw new \RuntimeException('Ungültige Darstellungsoption.');

        $user->setParam('bota_theme',$theme);
        if(!$user->save()){
            throw new \RuntimeException($user->getError()?:'Benutzerparameter konnten nicht gespeichert werden.');
        }
    }
}
