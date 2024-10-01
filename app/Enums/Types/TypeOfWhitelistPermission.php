<?php
namespace App\Enums\Types;
/**
 * postgres enum type_of_server_status
 */
enum TypeOfWhitelistPermission : string {

  case INHERITING = 'inheriting';
  case CREATE_ELEMENTS = 'create_elements';
  case OWN_ELEMENTS = 'own_elements';
  case READ_ELEMENTS = 'read_elements';
  case WRITE_ELEMENTS = 'write_elements';


}


