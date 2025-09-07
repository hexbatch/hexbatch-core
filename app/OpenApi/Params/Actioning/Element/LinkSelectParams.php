<?php

namespace App\OpenApi\Params\Actioning\Element;




use App\Models\ElementLink;
use App\OpenApi\ApiThingBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'LinkSelectParams')]
class LinkSelectParams extends ApiThingBase
{

    #[OA\Property(title: 'Link',description: 'The link')]
    protected ?string $link_ref = null;


    public function __construct(
        protected ?ElementLink    $given_link = null

    )
    {
        parent::__construct();
        $this->link_ref = $this->given_link?->ref_uuid;
    }


    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);


        if (!$this->given_link) {
            if ($col->has('link_ref') && $col->get('link_ref')) {
                $this->given_link = ElementLink::resolveLink(value: $col->get('link_ref'));
                $this->link_ref = $this->given_link->ref_uuid;
            }
        }

    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['link_ref'] = $this->link_ref;

        return $ret;
    }

    public function getElementRef(): ?string
    {
        return $this->given_link?->linking_element?->ref_uuid;
    }

    public function getTargetSetRef(): ?string
    {
        return $this->given_link?->linked_set?->ref_uuid;
    }



}
