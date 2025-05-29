<?php declare(strict_types=1);

namespace App\Annotations\Documentation;



use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;

#[\Attribute(\Attribute::TARGET_CLASS)]
class HexbatchTitle
{
    public function __construct(
        protected string $title
    ) {

    }

    public function getTitle(): string
    {
        return mb_trim($this->title);
    }

    /**
     * @throws CommonMarkException
     */
    public function getTitleHtml() : string {
        $converter = new CommonMarkConverter();

        $iter =  $converter->convert($this->getTitle());
        return $iter->getContent();
    }
}
