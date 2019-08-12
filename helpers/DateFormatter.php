<?php

class DateFormatter {
    public static function dateField($date) {
        return DateTime::createFromFormat('Ym', $date)->format('F Y');
    }

    public static function dateTimeField($dateTime) {
        return date("F d Y",strtotime($dateTime));
    }
}