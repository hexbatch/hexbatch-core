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
  case OWNS_RULE = 'owns_rule';
  case SHAPE_INTERSECTING = 'shape_intersecting';
  case SHAPE_BORDERING = 'shape_bordering';
  case SHAPE_SEPERATED = 'shape_seperated';
  case SEPERATED_MAP = 'seperated_map';
  case BORDERING_MAP = 'bordering_map';
  case INTERSECTING_MAP = 'intersecting_map';
  case TIME_OVERLAPPING = 'time_overlapping';
  case TIME_DISTINCT = 'time_distinct';
  case SET_CONTAINS = 'set_contains';


  case SHARES_TYPE = 'shares_type';
  case CHILDISH = 'childish';
  case LINKISH = 'linkish';
  case ANCESTOR = 'ancestor';
  case DESCENDANT = 'descendant';
  case NAMESPACE_OWNS_ELEMENT = 'namespace_owns_element';
  case IN_SUBTYPE = 'in_subtype';
  case THING_SET = 'thing_set';
  case NAMESPACE_OWNS_TYPE = 'namespace_owns_type';

  case MEMBER_OF_NAMESPACE = 'member_of_namespace';
  case ADMIN_OF_NAMESPACE = 'admin_of_namespace';
  case OWNER_OF_NAMESPACE = 'owner_of_namespace';


    public static function tryFromInput(string|int|bool|null $test ) : PathRelationshipType {
        $maybe  = PathRelationshipType::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(PathRelationshipType::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


