<?php
namespace App\Sys\Build;



use App\Helpers\Utilities;

class AttributeMapEntry extends ActionMap
{
    public bool $is_abstract = true;
    public bool $is_final = true;
    public bool $is_seen = true;

    public function __construct(array $info = [])
    {
        parent::__construct($info);

        $this->is_abstract = Utilities::boolishToBool($info['is_abstract'] ?? false);
        $this->is_seen = Utilities::boolishToBool($info['is_seen'] ?? false);
        $this->is_final = Utilities::boolishToBool($info['is_final'] ?? false);
    }

    public function setFromClassName(string $full_class_name)
    {
        if (is_subclass_of($full_class_name, 'App\Sys\Res\Atr\BaseAttribute')) {

            $this->full_class_name = $full_class_name;
            $this->type_uuid = $full_class_name::getClassUuid();
            $this->internal_name = $full_class_name::getClassName();
            $this->is_system = $full_class_name::isSystem();
            $this->has_events = false;
            $this->is_abstract = $full_class_name::isAbstract();
            $this->is_seen = $full_class_name::isSeenChildrenTypes();
            $this->is_final = $full_class_name::isFinal();
        }

    }

    public function toArray()
    {
        $ret = parent::toArray();
        unset($ret['has_events']);

        return array_merge([
            'is_abstract' => $this->isAbstract(),
            'is_seen' => $this->isSeen(),
            'is_final' => $this->isFinal(),
        ],$ret);
    }

    public function isAbstract(): bool
    {
        return $this->is_abstract;
    }

    public function isFinal(): bool
    {
        return $this->is_final;
    }

    public function isSeen(): bool
    {
        return $this->is_seen;
    }



}

