<?php

class Touren_TerminalController extends Zend_Controller_Action {
    //put your code here

    public function indexAction() {
        $r = $this->getRequest();
        $dbg = (int)$r->getParam('dbg', '0' );
        $tag = $r->getParam('tag', '' );
        $lager_id = $r->getParam('lager_id', 1 );
        $testOffsetHeuteMorgen = $r->getParam('offsetHeuteMorgen', 0 );

        $nowHeuteMorgen = (!$testOffsetHeuteMorgen) ? time() : strtotime($testOffsetHeuteMorgen);
        $iPlusDays = 0;

        if (empty($tag)) {
            $tag = 'heute';
        }

        $easterTime   = easter_date( date('Y') );
        $easterMonday = date('m-d', strtotime('+1 day', $easterTime));

        $aFeiertage = [
            '01-01',
            '05-01',
            '10-03',
            '12-25',
            '12-26',
            $easterMonday,
        ];

        switch ($tag) {
            case 'heute':
                $date = date('Y-m-d');
                break;

            case 'morgen':
                $offsetTime = $nowHeuteMorgen;
                $loop = 0;
                do {
                    ++$loop;
                    $morgenTime = strtotime('+1 day', $offsetTime);
                    $morgenMT = date('m-d', $morgenTime);
                    $morgenWT = date('N', $morgenTime);
                    $date = date('Y-m-d', $morgenTime);

                    $offsetTime = $morgenTime;
                } while(
                    $loop < 10 && (
                        in_array($morgenMT, $aFeiertage)
                        || $morgenWT > 5
                    )
                );
                break;

            default:
                $date = date('Y-m-d', strtotime($tag));
        }
        $tagTime = strtotime($date);

        if (!preg_match('/^\d\d\d\d+-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[01])$/', $date)) {
            die('Ungültige Tagesangabe ' . $tag);
        }

        if (in_array( $tag, array('heute', 'morgen')) && $iPlusDays === 1) $tagTitel = ucfirst ($tag);
        elseif ($date == date('Y-m-d')) $tagTitel = 'Heute';
        elseif ($date == date('Y-m-d', strtotime('+1 day'))) $tagTitel = 'Morgen';
        else $tagTitel = '';

        $this->getHelper( 'layout' )->disableLayout();
        $modelDV = new Model_TourenDispoVorgaenge();
        $modelLG = new Model_Lager();

        $wochentagN = date( 'N', $tagTime);
        $this->view->thisKW = date('W', $tagTime);
        $this->view->thisKWMondayTime = strtotime( '-' . ($wochentagN-1) . ' days', $tagTime);
        $this->view->thisKWMondayDate = date( 'Y-m-d', $this->view->thisKWMondayTime);
        $this->view->prevKWMondayDate = date( 'Y-m-d', strtotime( '-1 week', $this->view->thisKWMondayTime));
        $this->view->nextKWMondayDate = date( 'Y-m-d', strtotime( '+1 week', $this->view->thisKWMondayTime));
        // die(print_r(['<pre>', __LINE__, __FILE__, 'wochentagN of tag' => $wochentagN, 'Montag der Woche' => $this->view->thisKWMondayDate ], 1));

        $this->view->weekdays = [ $this->view->thisKWMondayDate => 'Mo' ];
        $this->view->weekdays[ date( 'Y-m-d', strtotime( '+1 day', $this->view->thisKWMondayTime))] = 'Di';
        $this->view->weekdays[ date( 'Y-m-d', strtotime( '+2 day', $this->view->thisKWMondayTime))] = 'Mi';
        $this->view->weekdays[ date( 'Y-m-d', strtotime( '+3 day', $this->view->thisKWMondayTime))] = 'Do';
        $this->view->weekdays[ date( 'Y-m-d', strtotime( '+4 day', $this->view->thisKWMondayTime))] = 'Fr';



        $holiday = MyProject_Date_Holidays::getHolidayByDate($date);
        $w = array('Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag');
        $this->view->title= $tagTitel . ' ' . date('d.m.Y', strtotime($date)). ' / ' . $w[date('w', strtotime($date))];
        $this->view->holiday = $holiday;
        $this->view->date  = $date;
        $this->view->thisDay  = $date;
        $this->view->thisDayTime = $tagTime;
        $this->view->nextDay = date('Y-m-d', strtotime('+1 day', $tagTime));
        $this->view->prevDay = date('Y-m-d', strtotime('-1 day', $tagTime));
        $this->view->lager = $modelLG->fetchEntry($lager_id);
        $this->view->lagerList = $modelLG->getList();
        $this->view->data  = $modelDV->getFullDayData($date, $lager_id);

        if ($dbg) {
            die(print_r(['<pre>', __LINE__, __FILE__, $this->view],1));
        }
    }
}

