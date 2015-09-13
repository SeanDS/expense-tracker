<?php

namespace Expenses;

use \DateTime;
use \DateInterval;

class Totals {
    const PERIOD_TODAY                  =   1;
    const PERIOD_LAST_24_HOURS          =   2;
    const PERIOD_LAST_7_DAYS            =   4;
    const PERIOD_SINCE_START_OF_MONTH   =   8;
    const PERIOD_LAST_30_DAYS           =   16;
    const PERIOD_ALL_TIME               =   32;
    const PERIOD_ALL                    =   63;
    
    protected $expenses;
    
    public function __construct(ExpenseGroup $expenses) {        
        $this->expenses = $expenses;
    }
    
    public function getTotals(User $user, $selectedPeriods = self::PERIOD_ALL) {
        if (! self::validatePeriodBitfield($selectedPeriods)) {
            throw new Exception("Invalid period bitfield specified.");
        }
        
        /*
         * Get expenditure
         */

        $totals = array();
        $userTime = new DateTime($user->getCurrentUserDate(), $user->getTimeZone());

        // today
        if ($selectedPeriods && self::PERIOD_TODAY) {        
            $userMidnight = $userTime->setTime(0, 0, 0);
            
            $todayGroup = new ExpenseGroup(
                array_merge(
                    $this->expenses->getWhereCriteria(),
                    array(
                        array(
                            'column'    =>  'date',
                            'operator'  => ExpenseGroup::OPERATOR_GREATER_THAN_EQUALS,
                            'value'     =>  $userMidnight->format(DB_DATE_FORMAT)
                        )
                    )
                ),
                $this->expenses->getOrderBy(),
                $this->expenses->getStartRow(),
                $this->expenses->getRowLimit()
            );
            
            $totals[] = array(
                'range'     =>  'Today',
                'amount'    =>  $todayGroup->getTotalExpenses()
            );
        }

        // last 24 hours
        if ($selectedPeriods && self::PERIOD_LAST_24_HOURS) {
            $userOneDayAgo = $userTime->sub(new DateInterval('P1D'));
            
            $lastDayGroup = new ExpenseGroup(
                array_merge(
                    $this->expenses->getWhereCriteria(),
                    array(
                        array(
                            'column'    =>  'date',
                            'operator'  => ExpenseGroup::OPERATOR_GREATER_THAN_EQUALS,
                            'value'     =>  $userOneDayAgo->format(DB_DATE_FORMAT)
                        )
                    )
                ),
                $this->expenses->getOrderBy(),
                $this->expenses->getStartRow(),
                $this->expenses->getRowLimit()
            );
            
            $totals[] = array(
                'range'     =>  'Last 24 hours',
                'amount'    =>  $lastDayGroup->getTotalExpenses()
            );
        }

        // last 7 days
        if ($selectedPeriods && self::PERIOD_LAST_7_DAYS) {
            $userOneWeekAgo = $userTime->sub(new DateInterval('P7D'));
            
            $lastWeekGroup = new ExpenseGroup(
                array_merge(
                    $this->expenses->getWhereCriteria(),
                    array(
                        array(
                            'column'    =>  'date',
                            'operator'  => ExpenseGroup::OPERATOR_GREATER_THAN_EQUALS,
                            'value'     =>  $userOneWeekAgo->format(DB_DATE_FORMAT)
                        )
                    )
                ),
                $this->expenses->getOrderBy(),
                $this->expenses->getStartRow(),
                $this->expenses->getRowLimit()
            );
            
            $totals[] = array(
                'range'     =>  'Last 7 days',
                'amount'    =>  $lastWeekGroup->getTotalExpenses()
            );
        }
        
        // since start of month
        if ($selectedPeriods && self::PERIOD_SINCE_START_OF_MONTH) {
            $userStartOfMonth = $userTime->setDate($userTime->format('Y'), $userTime->format('m'), 1);
            $userStartOfMonth->setTime(0, 0, 0);
            
            $lastWeekGroup = new ExpenseGroup(
                array_merge(
                    $this->expenses->getWhereCriteria(),
                    array(
                        array(
                            'column'    =>  'date',
                            'operator'  => ExpenseGroup::OPERATOR_GREATER_THAN_EQUALS,
                            'value'     =>  $userStartOfMonth->format(DB_DATE_FORMAT)
                        )
                    )
                ),
                $this->expenses->getOrderBy(),
                $this->expenses->getStartRow(),
                $this->expenses->getRowLimit()
            );
            
            $totals[] = array(
                'range'     =>  sprintf('Since %s', $userStartOfMonth->format('jS F')),
                'amount'    =>  $lastWeekGroup->getTotalExpenses()
            );
        }

        // last 30 days
        if ($selectedPeriods && self::PERIOD_LAST_30_DAYS) {
            $userOneMonthAgo = $userTime->sub(new DateInterval('P30D'));
            
            $lastMonthGroup = new ExpenseGroup(
                array_merge(
                    $this->expenses->getWhereCriteria(),
                    array(
                        array(
                            'column'    =>  'date',
                            'operator'  => ExpenseGroup::OPERATOR_GREATER_THAN_EQUALS,
                            'value'     =>  $userOneMonthAgo->format(DB_DATE_FORMAT)
                        )
                    )
                ),
                $this->expenses->getOrderBy(),
                $this->expenses->getStartRow(),
                $this->expenses->getRowLimit()
            );
            
            $totals[] = array(
                'range'     =>  'Last 30 days',
                'amount'    =>  $lastMonthGroup->getTotalExpenses()
            );
        }
            
        // all time
        if ($selectedPeriods && self::PERIOD_ALL_TIME) {
            $allTimeGroup = new ExpenseGroup(
                $this->expenses->getWhereCriteria(),
                $this->expenses->getOrderBy(),
                $this->expenses->getStartRow(),
                $this->expenses->getRowLimit()
            );
            
            $totals[] = array(
                'range'     =>  'All Time',
                'amount'    =>  $allTimeGroup->getTotalExpenses()
            );
        }
        
        return $totals;
    }
    
    public static function validatePeriodBitfield($bitfield) {
        return (
            (is_int($bitfield)) &&
            ($bitfield <= self::PERIOD_ALL)
        );
    }
}

?>