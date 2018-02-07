<?php defined('SYSPATH') or die('No direct script access.');

class Request_Client_Internal extends Kohana_Request_Client_Internal {

    /**
     * We override this method to allow for dashes in the action part of the url
     * (See Kohana_Request_Client_Internal::execute_request() for the details)
     *
     * @param   Request $request
     * @return  Response
     */
    public function execute_request(Request $request, Response $response)
    {
        // Modify action part of the request: transform all dashes to underscores
        $request->controller( self::dashesToCamelCase($request->controller(), FALSE) );
        $request->action( self::dashesToCamelCase($request->action(), TRUE) );

        // We are done, let the parent method do the heavy lifting
        return parent::execute_request($request, $response);
    }

    private static function dashesToCamelCase($str, $lcfirst = TRUE)
    {
        $pos = strpos($str, '-');
        if ( $pos !== FALSE && $pos !== 0 )
        {
            $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $str)));
            if ( $lcfirst )
            {
                $str[0] = strtolower($str[0]);
            }
        }
        return $str;
    }

} // end_class Request_Client_Internal