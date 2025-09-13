<?php declare(strict_types=1);

namespace App\Annotations\Documentation;



use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;


#[\Attribute(\Attribute::TARGET_CLASS)]
class HexbatchDescription
{
    public function __construct(
        protected string $description
    ) {

    }

    public function getDescription() : string {
        return mb_trim($this->description);
    }

    /**
     * @throws CommonMarkException
     */
    public function getDescriptionHtml() : string {
        $converter = new CommonMarkConverter();

        $iter =  $converter->convert($this->getDescription());
        return $iter->getContent();
    }
}
