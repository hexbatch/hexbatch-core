<?php
namespace App\Enums\Paths;


/**
 * postgres enum path_relationship_type
 */
enum PathRelationshipType : string {
  case NO_RELATIONSHIP = 'no_relationship';
  case SHAPE_INTERSECTING = 'shape_intersecting';
  case SHAPE_BORDERING = 'shape_bordering';
  case SHAPE_SEPERATED = 'shape_seperated';
  case SHARES_TYPE = 'shares_type';
  case CHILDISH = 'childish';
  case LINKISH = 'linkish';

    public static function tryFromInput(string|int|bool|null $test ) : PathRelationshipType {
        $maybe  = PathRelationshipType::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(PathRelationshipType::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


