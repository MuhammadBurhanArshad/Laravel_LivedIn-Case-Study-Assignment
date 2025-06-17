<?php

function formatText(string $text): string
{
    return str_replace(['_', ' '], '', ucwords($text, '_ '));
}

function formatDate($date)
{
    if (empty($date))
        return null;
    return date('Y-m-d H:i:s', strtotime($date));
}
