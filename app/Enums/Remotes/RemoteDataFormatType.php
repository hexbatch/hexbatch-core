<?php
namespace App\Enums\Remotes;
enum RemoteDataFormatType : string {

    case TEXT = 'text';

    case XML = 'xml';

    case JSON = 'json';
    case YAML = 'yaml';
    case FORM_URLENCODED = 'form-urlencoded';
    case MULTIPART_FORM_DATA = 'multipart-form-data';
    case QUERY = 'query';


    const ALLOWED_FROM_REMOTE = [
      self::TEXT,self::XML,self::JSON,self::YAML
    ];

}
