<?php
namespace App\Services\Report;


interface IReportService{
    public function getSalesDetailsReportData($posId, $from, $to, $start, $length, $type);
    public function getExpenseDetailsReportData($posId, $from, $to, $start, $length, $type);
    public function getDiscountAdjustmentReportData($posId, $from, $to, $start, $length, $type);
    public function getRevenueReportData($posId, $from, $to, $start, $length, $type);
    public function getNetProfitReportData($posId, $from, $to, $start, $length, $type);
    public function getCustomerReportData($posId, $from, $to, $customerType, $start, $length, $type);
}
