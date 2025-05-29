# Hex Batch Core

This code is implenting the ideas in core-docs/core-overview.md

## Version 0.3.1

First update to master in some time, combines about 300 commits from the last year on topic branches. 
The project has totally changed, a major rewrite. This is a release of work in progress

# Branch plans

* Make user create/edit design attributes, try it out in api
* figure out default attribute values, or not, and if we are using json path or not at this stage, or later
   * in many cases the attribute value is not a constant, but from the read event, 
     so the element values may not be usable here, the value can just be null. 
   * the element values is only for static values
* Implement the element values, when element is created, fill out the table per attribute
* when an element is added to a set, do not a row until element is updated in the set, 
* and then only if the attribute has a policy of values per set or child. 
* when the element is removed from the set, remove set specific rows
* fill out 

[ElementChangeOwner.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/ElementChangeOwner.php)
[ElementDestroy.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/ElementDestroy.php)
[ElementEdit.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/ElementEdit.php)
[ElementOff.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/ElementOff.php)
[ElementOn.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/ElementOn.php)
[ElementPing.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/ElementPing.php)
[ElementPurge.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/ElementPurge.php)
[LinkAdd.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/LinkAdd.php)
[LinkRemove.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/LinkRemove.php)
[Read.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/Read.php)
[ReadLiveType.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/ReadLiveType.php)
[ReadTimeSpan.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/ReadTimeSpan.php)
[ReadType.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/ReadType.php)
[ReadVisual.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/ReadVisual.php)
[TypeOff.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/TypeOff.php)
[TypeOn.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/TypeOn.php)
[Write.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ele/Write.php)



[DesignAttributeCreate.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignAttributeCreate.php)
[DesignAttributeDestroy.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignAttributeDestroy.php)
[DesignAttributeEdit.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignAttributeEdit.php)
[DesignDestroy.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignDestroy.php)
[DesignEdit.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignEdit.php)
[DesignOwnerChange.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignOwnerChange.php)
[DesignParentRemove.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignParentRemove.php)

[DesignTimeCreate.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignTimeCreate.php)
[DesignTimeDestroy.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignTimeDestroy.php)
[DesignTimeEdit.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignTimeEdit.php)
[DesignLocationCreate.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignLocationCreate.php)
[DesignLocationDestroy.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignLocationDestroy.php)
[DesignLocationEdit.php](app/Sys/Res/Types/Stk/Root/Act/Cmd/Ds/DesignLocationEdit.php)


* make readable and writable checks in the types
* start moving all comments to the doc attr

# Links

for rendering the docs 
https://commonmark.thephpleague.com/2.7/extensions/overview/
