<?php

namespace App\Sys\Res;

interface IDocument
{
    public static function getHexbatchTitle() :?string;
    public static function getHexbatchBlurb() :?string;
    public static function getHexbatchDescriptionText() :?string;
    public static function getHexbatchDescriptionHtml() :?string;
    public static function getHexbatchDescriptionMarkdown() :?string;

}
