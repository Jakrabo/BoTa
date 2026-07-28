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

        if(!in_array($theme,['auto','light','dark'],true)){
            $theme='auto';
        }

        setcookie(
            'bota_theme',
            $theme,
            [
                'expires'=>time()+31536000,
                'path'=>'/',
                'secure'=>$app->isHttpsForced() || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off'),
                'httponly'=>false,
                'samesite'=>'Lax',
            ]
        );

        $_COOKIE['bota_theme']=$theme;

        $document=$app->getDocument();
        $document->getWebAssetManager()->useStyle('com_jugendtraining.site');

        // Apply the resolved theme immediately in the document head.
        // This deliberately does not depend on the Joomla WebAsset script registry.
        $themeJson=json_encode(
            $theme,
            JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT
        );

        $document->addCustomTag(
            '<script>(function(){'
            .'var configured='.$themeJson.';'
            .'var mq=window.matchMedia("(prefers-color-scheme: dark)");'
            .'function apply(){'
            .'var resolved=configured==="auto"?(mq.matches?"dark":"light"):configured;'
            .'document.documentElement.setAttribute("data-bota-theme",configured);'
            .'document.documentElement.setAttribute("data-bota-theme-resolved",resolved);'
            .'document.documentElement.style.colorScheme=resolved;'
            .'if(document.body){'
            .'document.body.classList.toggle("bota-theme-dark",resolved==="dark");'
            .'document.body.classList.toggle("bota-theme-light",resolved==="light");'
            .'}'
            .'}'
            .'apply();'
            .'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",apply,{once:true});}'
            .'if(configured==="auto"){'
            .'if(typeof mq.addEventListener==="function"){mq.addEventListener("change",apply);}'
            .'else if(typeof mq.addListener==="function"){mq.addListener(apply);}'
            .'}'
            .'})();</script>'
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
