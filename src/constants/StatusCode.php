<?php
/**
 * Define macro for status code
 * Created date : 19/03/2019
 *
 * PHP version 7
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */

/*Error Code */
define('REQUEST_METHOD_NOT_VALID', 100);
define('REQUEST_CONTENTTYPE_NOT_VALID', 101);
define('REQUEST_NOT_VALID', 102);
define('VALIDATE_PARAMETER_REQUIRED', 103);
define('VALIDATE_PARAMETER_DATATYPE', 104);
define('API_NAME_REQUIRED', 105);
define('API_PARAM_REQUIRED', 106);
define('API_DOST_NOT_EXIST', 107);
define('INVALID_USER_PASS', 108);

define('SUCCESS_RESPONSE', 200);
define('NEW_RECORD_CREATED', 201);
define('NO_CONTENT', 204);

/*Servers Errors */
define('AUTHORIZATION_HEADER_NOT_FOUND', 300);
define('ACCESS_TOKEN_ERRORS', 301);


define('USER_NOT_FOUND', 400);
define('UNAUTHORIZED_USER', 401);
define('INVALID_CREDINTIAL', 403);
define('NOT_ACCEPTABLE', 406);
define('CONFLICT_CONTENT', 409);

define('INTERNAL_SERVER_ERROR', 500);
