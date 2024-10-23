<?php
namespace App\Enums\Paths;


/**
 * postgres enum path_relationship_type
 */
enum PathRelationshipType : string {
    case NO_RELATIONSHIP = 'no_relationship';


    case RULE_EVENT = 'rule_event';
    case RULE_ACTION = 'rule_action';
    case RULE_PARENT = 'rule_parent';
    case RULE_CHILD = 'rule_child';
    case OWNS_RULE = 'owns_rule'; //note: relationship owns_rule means that the rule references the type of the api or action


    case SHAPE_INTERSECTING = 'shape_intersecting';
    case SHAPE_BORDERING = 'shape_bordering';
    case SHAPE_SEPERATED = 'shape_seperated';

    case SEPERATED_MAP = 'seperated_map';
    case BORDERING_MAP = 'bordering_map';
    case INTERSECTING_MAP = 'intersecting_map';

    case TIME_OVERLAPPING = 'time_overlapping';
    case TIME_DISTINCT = 'time_distinct';




    case DOWN_TYPE = 'down_type';
    case UP_TYPE = 'up_type';
    case SHARES_TYPE = 'shares_type';

    case DOWN_ATTRIBUTE = 'down_attribute';
    case UP_ATTRIBUTE = 'up_attribute';
    case ATTRIBUTE_OF_TYPE = 'attribute_of_type';
    case SHARES_ATTRIBUTE = 'shares_attribute';

    case MEMBER_OF_THING_CONTAINER = 'member_of_thing_container';

    case NAMESPACE_OWNS_ELEMENT = 'namespace_owns_element';
    case NAMESPACE_OWNS_TYPE = 'namespace_owns_type';
    case NAMESPACE_OWNS_ATTRIBUTE = 'namespace_owns_attribute';
    case NAMESPACE_OWNS_SET = 'namespace_owns_set';
    case NAMESPACE_OWNS_LIVE = 'namespace_owns_live';

    case MEMBER_OF_NAMESPACE = 'member_of_namespace';
    case ADMIN_OF_NAMESPACE = 'admin_of_namespace';
    case OWNER_OF_NAMESPACE = 'owner_of_namespace';


    case LINK_ELEMENT = 'link_element';
    case HANDLE_ELEMENT = 'handle_element';
    case DESCRIPTION_ELEMENT = 'description_element';

    case LIVE_TYPE = 'live_type';
    case LIVE_REQUIREMENT = 'live_requirement';
    case LIVE_RULE = 'live_rule';

    case DOWN_SET = 'down_set';
    case UP_SET = 'up_set';
    case ELEMENT_OF_SET = 'element_of_set';


    public static function tryFromInput(string|int|bool|null $test ) : PathRelationshipType {
        $maybe  = PathRelationshipType::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(PathRelationshipType::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


