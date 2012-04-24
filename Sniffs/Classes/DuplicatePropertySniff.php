<?php
/**
 * Hotelsnl_Sniffs_Classes_DuplicatePropertySniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2011 Hotelsnl Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Hotelsnl_Sniffs_Classes_DuplicatePropertySniff.
 *
 * Ensures JS classes dont contain duplicate property names.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2011 Hotelsnl Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.3
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Hotelsnl_Sniffs_Classes_DuplicatePropertySniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('JS');


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OBJECT);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being processed.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $start  = $tokens[$stackPtr]['scope_opener'];
        $end    = $tokens[$stackPtr]['scope_closer'];

        $properties   = array();
        $wantedTokens = array(
                         T_PROPERTY,
                         T_OPEN_CURLY_BRACKET,
                        );

        $next = $phpcsFile->findNext($wantedTokens, ($start + 1), $end);
        while ($next !== false && $next < $end) {
            // Skip nested objects.
            if ($tokens[$next]['code'] === T_OPEN_CURLY_BRACKET) {
                $next = $tokens[$next]['bracket_closer'];
            } else {
                $propName = $tokens[$next]['content'];
                if (isset($properties[$propName]) === true) {
                    $error = 'Duplicate property definition found for "%s"; previously defined on line %s';
                    $data  = array(
                              $propName,
                              $tokens[$properties[$propName]]['line'],
                             );
                    $phpcsFile->addError($error, $next, 'Found', $data);
                }

                $properties[$propName] = $next;
            }//end if

            $next = $phpcsFile->findNext($wantedTokens, ($next + 1), $end);
        }//end while

    }//end process()


}//end class


?>
