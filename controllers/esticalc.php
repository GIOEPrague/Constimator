<?php
/**
 * @version		$Id: registration.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * Registration controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class OpenmartControllerEsticalc extends JControllerForm
{
    public function calculateObject(){
        $mainframe =& JFactory::getApplication();
        header("content-type:application/json;charset=utf-8");
        $session = JFactory::getSession();
        $sObject = $session->get('object');
        $model = $this->getModel();
        $id = JRequest::getInt('id');
        $object = JRequest::getVar('object');
        $sObject[$id] = $object[$id];
        if($sObject[$id]['categoryId'] > 0 && $sObject[$id]['paymentZoneId'] > 0){
            $result = $model->calculateObjectPrice($sObject[$id]);
            $sObject[$id]['result'] = $result;
            $paymentZone = $model->calculateObjectPayment($sObject[$id]);
            $sObject[$id]['payment'] = $paymentZone['payment'];
            $sObject[$id]['percentage'] = $paymentZone['percentage'];
        } else {
            //return rather 0 then undefined
            $sObject[$id]['result'] = 0;
        }
        $session->set('object', $sObject);

        echo json_encode($sObject[$id]);
        $mainframe->close();
        
    }
    public function calculateSummary($ret = true){
        if($ret == true){
            $mainframe =& JFactory::getApplication();
            header("content-type:application/json;charset=utf-8");
        }
        
        $ival = $abcd = str_replace (" ", "", JRequest::getInt('ival'));
        $iname = JRequest::getString('iname');
        $session = JFactory::getSession();
        $sObject = $session->get('object');
        $summary = $session->get('summary');
        if($summary == null){
            $summary = $this->createNewSummary();
        }
        $summary['catC'] = 0;
        $summary['catCvat'] = 0;
        $summary['catA'] = 0;
        $summary['catH'] = 0;
        $summary['totalPrice'] = 0;
        $summary['totalVat'] = 0;
        $summary['result'] = 0;
        $summary['resultUp'] = 0;
        $summary['resultDown'] = 0;
        foreach($sObject as $object){
            $summary['catC'] += $object['result'];
            $summary['catCvat'] += $object['result']*$object['vat'];
            $summary['catH'] += $object['result']*$object['reserve'];
            $summary['catA'] += $object['payment'];
        }

        if($iname != '' && $iname != null){
            foreach($summary as $key=>$summ){
                if($key == $iname){
                    $summary[$key] = $ival;
                }
            }
        }
        $summary['catH'] = round($summary['catH']);
        $summary['catAvat'] = round($summary['catA']*$summary['vat']);
        $summary['catBvat'] = round($summary['catB']*$summary['vat']);
        $summary['catCvat'] = round($summary['catCvat']);
        $summary['catDvat'] = round($summary['catD']*$summary['vat']);
        //$summary['catEvat'] = $summary['catE']*$summary['vat']; Doesn't have VAT
        $summary['catF'] = round($summary['catC']*$summary['nusPercent']);
        $summary['catFvat'] = round($summary['catF']*$summary['vat']);
        $summary['catGvat'] = round($summary['catG']*$summary['vat']);
        $summary['catHvat'] = round($summary['catH']*$summary['vat']);
        $summary['catIvat'] = round($summary['catI']*$summary['vat']);
        $summary['catJvat'] = round($summary['catJ']*$summary['vat']);
        $summary['catKvat'] = round($summary['catK']*$summary['vat']);
        $summary['totalPrice'] = $summary['catA'] + $summary['catB'] + $summary['catC'] + $summary['catD'] + $summary['catE'] + $summary['catF'] + $summary['catG'] + $summary['catH'] + $summary['catI'] + $summary['catJ'] + $summary['catK'];
        $summary['totalVat'] = $summary['catAvat'] + $summary['catBvat'] + $summary['catCvat'] + $summary['catDvat'] + $summary['catFvat'] + $summary['catGvat'] + $summary['catHvat'] + $summary['catIvat'] + $summary['catJvat'] + $summary['catKvat'];
        $summary['result'] = $summary['totalPrice'] + $summary['totalVat'];
        $summary['resultUp'] = round($summary['result']*1.15);
        $summary['resultDown'] = round($summary['result']*0.85);
        
        
        $session->set('summary', $summary);
        if($ret == true){
            echo json_encode($summary);
            $mainframe->close();
        }
        
    }
    public function createNewSummary(){
        $summary = array();
        $summary['vat'] = 0.2;
        $summary['catC'] = 0;
        $summary['catA'] = 0;
        $summary['nusPercent'] = 0.05;
        $summary['reservePercent'] = 0.1;
        $summary['catB'] = 0;
        $summary['catD'] = 0;
        $summary['catE'] = 0;
        $summary['catG'] = 0;
        $summary['catI'] = 0;
        $summary['catJ'] = 0;
        $summary['catK'] = 0;
        return $summary;
    }
    public function clearSession(){
        $session = JFactory::getSession();
        $session->clear('object');
        $this->setRedirect(JRoute::_('index.php?option=com_openmart&view=esticalc', false), 'Session had been cleared');
    }
    public function createNewObject(){
        $model = $this->getModel();
        $model->createObject();
        $this->setRedirect(JRoute::_('/', false));
    }
    public function deleteObject(){
        $session = JFactory::getSession();
        $sObject = $session->get('object');
        $id = JRequest::getInt('object_id');
        unset($sObject[$id]);
        $session->set('object', $sObject);
        $this->calculateSummary(false);
        $this->setRedirect(JRoute::_('/', false));
    }
    public function saveConstruction(){
        $model = $this->getModel('product');
        $session = JFactory::getSession();
        $object = $session->get('summary');
        echo '<pre>';
        var_dump($object);
        //$model->save();
        //$this->setRedirect(JRoute::_('/', false),'saved');
    }
    public function convertConstructionForSave(){
        $session = JFactory::getSession();
        $summary = $session->get('summary');
        if(is_array($summary)){
            $summaryTD = array();
            $summaryTD['field'] = array();
            $summaryTD['field'][73] = $summary["vat"]; //TOTAL_VAT
            $summaryTD['field'][74] = $summary["catA"]; //CAT_A
            $summaryTD['field'][75] = $summary["catB"]; //CAT_B
            $summaryTD['field'][76] = $summary["catC"]; //CAT_C
            $summaryTD['field'][77] = $summary["catD"]; //CAT_D
            $summaryTD['field'][78] = $summary["catE"]; //CAT_E
            $summaryTD['field'][79] = $summary["catF"]; //CAT_F
            $summaryTD['field'][80] = $summary["catG"]; //CAT_G
            $summaryTD['field'][81] = $summary["catH"]; //CAT_H
            $summaryTD['field'][82] = $summary["catI"]; //CAT_I
            $summaryTD['field'][83] = $summary["catJ"]; //CAT_J
            $summaryTD['field'][84] = $summary["catK"]; //CAT_K
            return $summaryTD;
        } else {
            return false;
        }
    }
    
    //following method had been coded in Google I/O extended in Prague 2012
    public function getPrice(){
        $amount = JRequest::getInt('amount');
        $from = JRequest::getVar('from');
        $to = JRequest::getVar('to');
        $session = JFactory::getSession();
        $session->set('currency', $to);
        $app = JFactory::getApplication();
        $newPrice = $this->getData($this->getGoogleCurrencyUrl($amount, $from, $to));
        echo $newPrice;
        $app->close();
        
    }
    public function getData($url){
        
        $ch = curl_init();
        $timeout = 0;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $rawdata = curl_exec($ch);
        curl_close($ch);
        header('Content-Type: text/html; charset=UTF8');
        $data = explode('"', $rawdata);
        $data = explode(' ', $data['3']);
        //var_dump($data);
        $var = str_replace('%C2%A0','',urlencode($data['0']));
        if($data[1] == 'million'){
            $var = $var * 1000000;
        }
        return round($var);
    }
    public function getGoogleCurrencyUrl($amount, $from, $to, $lang = 'en'){
        return 'http://www.google.com/ig/calculator?hl='.$lang.'&q='.$amount.$from.'=?'.$to;
    }
}