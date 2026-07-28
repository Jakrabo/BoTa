<?php

namespace Jugendtraining\Component\Jugendtraining\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

final class DisplayController extends BaseController
{
    protected $default_view = 'dashboard';

    public function display($cachable = true, $urlparams = []): BaseController
    {
        $this->applyUserTheme();
        $this->syncCurrentSportyear();
        $this->input->set('view', $this->input->getCmd('view', $this->default_view));

        return parent::display($cachable, $urlparams);
    }

private function applyUserTheme(): void
{
    $app=Factory::getApplication();
    $user=$app->getIdentity();
    $theme=$user->guest?'auto':(string)$user->getParam('bota_theme','auto');
    if(!in_array($theme,['auto','light','dark'],true))$theme='auto';

    $document=$app->getDocument();
    $document->getWebAssetManager()
        ->useStyle('com_jugendtraining.site')
        ->useScript('com_jugendtraining.theme');

    // Script options are rendered before the deferred component script.
    // A tiny inline bootstrap sets the attribute early to avoid a light-theme flash.
    $document->addScriptOptions('com_jugendtraining.theme',['theme'=>$theme]);
    $document->addCustomTag(
        '<script>document.documentElement.setAttribute("data-bota-theme",'
        .json_encode($theme,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)
        .');</script>'
    );
}

    private function syncCurrentSportyear(): void
    {
        $db=Factory::getContainer()->get('DatabaseDriver');$today=Factory::getDate()->format('Y-m-d');
        $q=$db->getQuery(true)->select('id')->from('#__jt_sportyears')->where('published=1')->where('date_start<='.$db->quote($today))->where('date_end>='.$db->quote($today))->order('date_start DESC,id DESC');
        $db->setQuery($q,0,1);$current=(int)$db->loadResult();
        $q=$db->getQuery(true)->update('#__jt_sportyears')->set('is_current=0')->where('is_current<>0');$db->setQuery($q)->execute();
        if($current>0){$q=$db->getQuery(true)->update('#__jt_sportyears')->set('is_current=1')->where('id='.$current);$db->setQuery($q)->execute();}
    }
}
