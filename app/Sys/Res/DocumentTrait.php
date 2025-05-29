<?php
namespace App\Sys\Res;

use App\Helpers\Annotations\Documentation\HexbatchBlurb;
use App\Helpers\Annotations\Documentation\HexbatchDescription;
use App\Helpers\Annotations\Documentation\HexbatchTitle;
use League\CommonMark\Exception\CommonMarkException;


trait DocumentTrait
{
    /**
     * @throws CommonMarkException
     */
    public static function getHexbatchTitle() : ?string {
        $reflection = new \ReflectionClass(static::class);
        $classAttributes = $reflection->getAttributes(HexbatchTitle::class);
        if (empty($classAttributes)) {return null;}
        /** @var HexbatchTitle $docs */
        $docs =  $classAttributes[0]->newInstance();

        return $docs->getTitleHtml();
    }

    /**
     * @throws CommonMarkException
     */
    public static function getHexbatchBlurb() : ?string {
        $reflection = new \ReflectionClass(static::class);
        $classAttributes = $reflection->getAttributes(HexbatchBlurb::class);
        if (empty($classAttributes)) {return null;}
        /** @var HexbatchBlurb $docs */
        $docs =  $classAttributes[0]->newInstance();

        return $docs->getBlurbHtml();
    }

    /**
     * @throws CommonMarkException
     */
    public static function getHexbatchDescriptionText() : ?string {
        return strip_tags(static::getHexbatchDescriptionHtml()??'');
    }

    /**
     * @throws CommonMarkException
     */
    public static function getHexbatchDescriptionHtml() : ?string {
        $reflection = new \ReflectionClass(static::class);
        $classAttributes = $reflection->getAttributes(HexbatchDescription::class);
        if (empty($classAttributes)) {return null;}
        /** @var HexbatchDescription $docs */
        $docs =  $classAttributes[0]->newInstance();

        return $docs->getDescriptionHtml();
    }

    public static function getHexbatchDescriptionMarkdown() : ?string {
        $reflection = new \ReflectionClass(static::class);
        $classAttributes = $reflection->getAttributes(HexbatchDescription::class);
        if (empty($classAttributes)) {return null;}
        /** @var HexbatchDescription $docs */
        $docs =  $classAttributes[0]->newInstance();

        return $docs->getDescription();
    }


}
