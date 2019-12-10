<?php

namespace App\MetadataResolver;

use Exception;

/**
 * このResolverやParserが対応していないサイトであったことを表わします。
 */
class UnsupportedContentException extends Exception
{
}
