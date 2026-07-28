<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Import;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView
{
 public array $options=[];public array $result=[];public array $penalties=[];public float $penaltyBalance=0.0;public array $dashboardConfigs=[];public array $languageOverview=[];
 public function display($tpl=null):void{$this->options=$this->get('Options');$this->penalties=(array)($this->get('Penalties')??[]);$this->penaltyBalance=(float)($this->get('PenaltyBalance')??0);$this->dashboardConfigs=(array)($this->get('DashboardConfigs')??[]);$this->languageOverview=(array)($this->get('LanguageOverview')??[]);$this->result=Factory::getApplication()->getUserState('com_jugendtraining.import.result',[]);Factory::getApplication()->setUserState('com_jugendtraining.import.result',null);ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_CONFIG_TITLE'), 'cog');parent::display($tpl);}
}
