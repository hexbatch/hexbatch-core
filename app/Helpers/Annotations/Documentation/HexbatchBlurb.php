<?php declare(strict_types=1);

namespace App\Helpers\Annotations\Documentation;



use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;

#[\Attribute(\Attribute::TARGET_CLASS)]
class HexbatchBlurb
{
    public function __construct(
        protected string $blurb
    ) {

    }

    public function getBlurb() : string {
        return mb_trim($this->blurb);
    }

    /**
     * @throws CommonMarkException
     */
    public function getBlurbHtml() : string {
        $converter = new CommonMarkConverter();

        $iter =  $converter->convert($this->getBlurb());
        return $iter->getContent();
    }
}
