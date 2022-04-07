<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 03.05.2019
 * Time: 16:31
 */

class MyProject_Date_Diff
{
    /** @var DateInterval */
    private $_diffTotal = null;

    /** @var DateTime  */
    private $_baseDate = null;

    /** @var DateTime  */
    private $_diffDate = null;

    public function __construct(DateTime $baseDate, DateTime $diffDate)
    {
        $this->_baseDate = $baseDate;
        $this->_diffDate = $diffDate;

        $this->_diffTotal = $baseDate->diff($diffDate);
    }

    public function getDiffTotal(): DateInterval {
        return $this->_diffTotal;
    }

    public function getDiffArbeitstage() {
        $oHolidays = new MyProject_Date_Holidays();
        $iDiffArbeitstage = 0;
        $currTime = $this->_baseDate->getTimestamp();
        $cmpDate = $this->_diffDate->format('Y-m-d');

        if ($this->_diffTotal->invert === 0) {
            // baseDate < diffDate
            while(date('Y-m-d', $currTime) < $cmpDate) {
                $nDayOfWeek = date('N', $currTime);
                $currDate = date('Y-m-d', $currTime);
                $currTime = strtotime('+1 day', $currTime);

                if ($nDayOfWeek > 5 ) {
                    continue;
                }

                $_holiday = $oHolidays->getHolidayByDate($currDate);
                if (is_null($_holiday)) {
                    $iDiffArbeitstage++;
                } elseif ($_holiday['halb']) {
                    $iDiffArbeitstage+= 0.5;
                }
            }
        } else {
            while(date('Y-m-d', $currTime) > $cmpDate) {
                $nDayOfWeek = date('N', $currTime);
                $currDate = date('Y-m-d', $currTime);
                $currTime = strtotime('+1 day', $currTime);

                if ($nDayOfWeek > 5 ) {
                    continue;
                }

                $_holiday = $oHolidays->getHolidayByDate($currDate);
                if ($nDayOfWeek < 6 && is_null($_holiday)) {
                    $iDiffArbeitstage--;
                } elseif ($_holiday['halb']) {
                    $iDiffArbeitstage-= 0.5;
                }
            }
        }
        return $iDiffArbeitstage;
    }

    public function getDiffTotalDays() {
        $vorzeichen = ($this->_diffTotal->invert) ? -1 : 1;
        return $this->_diffTotal->days * $vorzeichen;
    }
}