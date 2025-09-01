<?php

namespace App\OpenApi\Params\Actioning\Element;





use App\Models\Element;
use App\Models\UserNamespace;
use App\OpenApi\ApiDataBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'ChangeElementOwnerParams')]
class ChangeElementOwnerParams extends ApiDataBase
{

    #[OA\Property(title: 'Namespace',description: 'The new elements are put into this namespace. Can be uuid or name. If missing will be put into calling namespace')]
    protected ?string $namespace_ref = null;

    #[OA\Property(title: 'Elements',description: 'The elements to add to the set. This is uuid ')]
    /** @var string[] $element_refs */
    protected array $element_refs = [];



    public function __construct(
        protected ?UserNamespace       $given_namespace = null,
        /** @var Element[] $elements */
        protected array $elements = []

    )
    {
        $this->namespace_ref = $this->given_namespace?->ref_uuid;
        if (count($this->elements)) {
            foreach ($this->elements as $ele) {
                $this->element_refs[] = $ele->ref_uuid;
            }
        }

    }


    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);


        if (!$this->given_namespace) {
            if ($col->has('namespace_ref') && $col->get('namespace_ref')) {
                $this->given_namespace = UserNamespace::resolveNamespace(value: $col->get('namespace_ref'));
                $this->namespace_ref = $this->given_namespace->ref_uuid;
            }
        }

        if (!count($this->elements) ) {
            if ($col->has('element_refs') && is_array($col->get('element_refs')) ) {
                $this->elements = Element::resolveElements(values: $col->get('element_refs'));
                foreach ($this->elements as $ele) {
                    $this->element_refs[] = $ele->ref_uuid;
                }
            }
        }

    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['namespace_ref'] = $this->namespace_ref;
        $ret['element_refs'] = $this->element_refs;

        return $ret;
    }

    public function getNamespaceRef(): ?string
    {
        return $this->namespace_ref;
    }

    /** @return  string[] */
    public function getElementRefs(): array
    {
        return $this->element_refs;
    }








}
