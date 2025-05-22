<?php
namespace App\Sys\Build;


use App\Helpers\Utilities;
use App\Sys\Res\IDocument;

abstract class ActionMap
{
    public ?string $type_uuid = null;
    public ?string $full_class_name = null;
    public ?string $internal_name = null;
    public bool $is_system = false;
    public bool $has_events = true;
    public ?string $title = null;
    public ?string $blurb = null;
    public ?string $description_html = null;
    public ?string $description_text = null;


    public function __construct(array $info = [])
    {
        $this->full_class_name = $info['class'] ?? null;
        $this->type_uuid = $info['uuid'] ?? null;
        $this->internal_name = $info['internal_name'] ?? null;
        $this->title = $info['title'] ?? null;
        $this->blurb = $info['blurb'] ?? null;
        $this->description_html = $info['description_html'] ?? null;
        $this->description_text = $info['description_text'] ?? null;
        $this->is_system = Utilities::boolishToBool($info['is_system'] ?? false);
        $this->has_events = Utilities::boolishToBool($info['has_events'] ?? true);
    }

    public function isDataComplete(): bool
    {
        return $this->type_uuid && $this->internal_name && $this->full_class_name;
    }

    abstract public function setFromClassName(string $full_class_name);


    public function toArray()
    {
        if (!$this->isDataComplete()) {
            throw new \LogicException("Data not complete for $this->internal_name $this->type_uuid");
        }
        return [
            'class' => $this->getFullClassName(),
            'uuid' => $this->getUuid(),
            'internal_name' => $this->getInternalName(),
            'is_system' => $this->isProtected(),
            'has_events' => $this->hasEvents(),
            'title' => $this->getTitle(),
            'blurb' => $this->getBlurb(),
            'description_html' => $this->getDescriptionHtml(),
            'description_text' => $this->getDescriptionText(),
        ];
    }

    public function getUuid(): ?string
    {
        return $this->type_uuid;
    }

    public function getFullClassName(): ?string
    {
        return $this->full_class_name;
    }

    public function getInternalName(): ?string
    {
        return $this->internal_name;
    }

    public function isProtected(): bool
    {
        return $this->is_system;
    }

    public function hasEvents(): bool
    {
        return $this->has_events;
    }

    public function getTitle(): ?string
    {
        if ($this->title !== null) {return $this->title;}
        /** @var IDocument $class */
        $class =  $this->full_class_name;
        return $this->title = str_replace('"','\"',$class::getHexbatchTitle()??'');
    }

    public function getBlurb(): string
    {
        if ($this->blurb !== null) {return $this->blurb;}
        /** @var IDocument $class */
        $class =  $this->full_class_name;
        return $this->blurb = str_replace('"','\"',$class::getHexbatchBlurb()??'');
    }

    public function getDescriptionHtml(): string
    {
        if ($this->description_html !== null) {return $this->description_html;}
        /** @var IDocument $class */
        $class =  $this->full_class_name;
        return $this->description_html = str_replace('"','\"',$class::getHexbatchDescriptionHtml()??'');
    }

    public function getDescriptionText(): string
    {
        if ($this->description_text !== null) {return $this->description_text;}
        /** @var IDocument $class */
        $class =  $this->full_class_name;
        return $this->description_text = str_replace('"','\"',$class::getHexbatchDescriptionText()??'');
    }

    public function getDescriptionMarkdown(): ?string
    {
        /** @var IDocument $class */
        $class =  $this->full_class_name;
        return str_replace('"','\"',$class::getHexbatchDescriptionMarkdown()??'');
    }

}

